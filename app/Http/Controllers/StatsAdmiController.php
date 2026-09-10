<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\Document;
use App\Models\Indicateur;
use App\Models\Commentaire;
use App\Models\Secteur;
use App\Models\Commune;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Localite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsAdmiController extends Controller
{
    /**
     * Page Dashboard — résumé rapide de la situation du territoire.
     * Données 100% base de données.
     */
    public function dashboard()
    {
        // ── KPIs ──
        $totalRegions = Region::count();
        $totalDepartements = Departement::count();
        $totalCommunes = Commune::count();
        $totalLocalites = Localite::count();
        $totalInfra = Infrastructure::count();
        $totalSecteurs = Secteur::count();
        $populationCouverte = DB::table('localite_couverts')
            ->sum('nbre_population_couvert');
        $commentairesEnAttente = Commentaire::independant()
            ->where('statut', 'en_attente')->count();

        // ── Alertes : infrastructures avec état Moyen/Mauvais/Hors_service ou non renseigné ──
        $alertes = Infrastructure::with(['secteur', 'commune', 'departement'])
            ->where(function ($q) {
                $q->whereIn('etat_lieu', ['Moyen', 'Mauvais', 'Hors_service'])
                    ->orWhereNull('etat_lieu');
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // ── Derniers documents ──
        $derniersDocs = Document::with(['commune', 'departement', 'infrastructure'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // ── Derniers commentaires indépendants ──
        $derniersCommentaires = Commentaire::independant()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── Données pour les charts (agrégation DB) ──
        // Répartition par secteur
        $parSecteur = Infrastructure::select('secteur_id', DB::raw('count(*) as total'))
            ->groupBy('secteur_id')
            ->with('secteur')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($r) => ['nom' => $r->secteur?->nom ?? 'Inconnu', 'total' => $r->total]);

        // Top 10 villages
        $topVillages = Infrastructure::whereNotNull('commune_id')
            ->join('communes', 'infrastructures.commune_id', '=', 'communes.id')
            ->select('communes.nom as village', DB::raw('count(*) as total'))
            ->groupBy('communes.nom')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('PageAdmi.StatsAdmi.Dashboard', compact(
            'totalRegions', 'totalDepartements', 'totalCommunes', 'totalLocalites',
            'totalInfra', 'totalSecteurs', 'populationCouverte', 'commentairesEnAttente',
            'alertes', 'derniersDocs', 'derniersCommentaires',
            'parSecteur', 'topVillages'
        ));
    }

    /**
     * Page Vue Générale — vue globale du territoire, filtrable par niveau
     * territorial (région / département / commune / localité).
     * Affiche l'information générale du périmètre choisi (population H/F,
     * ménages, superficie, localités…) puis des graphiques 100% base de
     * données bornés à ce périmètre.
     */
    public function vueGenerale(Request $request)
    {
        // ── Filtres : niveau territorial + entité (cascade) ──
        $niveau = in_array($request->get('niveau'), ['region', 'departement', 'commune', 'localite'], true)
            ? $request->get('niveau')
            : null;
        $entiteId = $request->integer('entite');

        // ── Référentiels pour les filtres en cascade (avec région dérivée) ──
        $regions = Region::orderBy('nom')->get();
        $regionDepts = Departement::select('id', 'nom', 'region_id')->orderBy('nom')->get();
        $communes = Commune::select('communes.id', 'communes.nom', 'communes.departement_id', 'departements.region_id')
            ->join('departements', 'departements.id', '=', 'communes.departement_id')
            ->orderBy('communes.nom')
            ->get();
        $localites = Localite::select('localites.id', 'localites.nom', 'localites.commune_id', 'departements.region_id')
            ->join('communes', 'communes.id', '=', 'localites.commune_id')
            ->join('departements', 'departements.id', '=', 'communes.departement_id')
            ->orderBy('localites.nom')
            ->get();

        // ── Libellé du périmètre choisi ──
        $labels = ['region' => 'Région', 'departement' => 'Département', 'commune' => 'Commune', 'localite' => 'Localité'];
        $pluriels = ['region' => 'Toutes les régions', 'departement' => 'Tous les départements', 'commune' => 'Toutes les communes', 'localite' => 'Toutes les localités'];

        $entiteNom = '';
        if ($niveau && $entiteId) {
            $listeEntites = match ($niveau) {
                'region'      => $regions,
                'departement' => $regionDepts,
                'commune'     => $communes,
                default       => $localites,
            };
            $entiteNom = $listeEntites->firstWhere('id', $entiteId)?->nom ?? '';
        }
        $scopeLabel = 'Ensemble du territoire';
        if ($niveau) {
            $scopeLabel = $entiteId ? ($labels[$niveau].' : '.$entiteNom) : $pluriels[$niveau];
        }

        // ── Périmètre territorial (jeux d'identifiants du niveau choisi) ──
        $scope = $this->scopeTerritoriale($niveau, $entiteId);

        // ── Infrastructures du périmètre ──
        $infraQuery = Infrastructure::query()->with(['secteur', 'commune', 'departement']);
        if ($niveau === 'localite') {
            $infraQuery->whereHas('localitesCouvertes', fn ($q) => $q->whereIn('localite_id', $scope['localites']));
        } elseif ($niveau) {
            if ($scope['depts']->isEmpty() && $scope['communes']->isEmpty()) {
                $infraQuery->whereRaw('1 = 0'); // périmètre vide (région sans département…)
            } else {
                $infraQuery->where(function ($q) use ($scope) {
                    if ($scope['depts']->isNotEmpty()) {
                        $q->whereIn('departement_id', $scope['depts']);
                    }
                    if ($scope['communes']->isNotEmpty()) {
                        $q->orWhereIn('commune_id', $scope['communes']);
                    }
                });
            }
        }
        $infras = $infraQuery->get();
        $infraIds = $infras->pluck('id');

        // ── KPI recensement (borné au périmètre) ──
        $totalInfra = $infras->count();

        // ── Information générale du périmètre choisie ──
        // Les fiches portent la démographie : selon le niveau, on somme les
        // enregistrements du niveau lui-même (région → régions, commune → communes…).
        if ($niveau === 'departement') {
            $infoRows = Departement::whereIn('id', $scope['depts'])->get();
        } elseif ($niveau === 'commune') {
            $infoRows = Commune::whereIn('id', $scope['communes'])->get();
        } elseif ($niveau === 'localite') {
            $infoRows = Localite::whereIn('id', $scope['localites'])->get();
        } else {
            $infoRows = Region::whereIn('id', $scope['regions'])->get();
        }

        $hommes = (int) $infoRows->sum('nbre_homme');
        $femmes = (int) $infoRows->sum('nbre_femme');
        $popTotale = (int) $infoRows->sum('taille_population') ?: ($hommes + $femmes);
        $menages = (int) $infoRows->sum('nbre_menage');

        // ── Comptes administratifs du périmètre ──
        $nbRegions = (int) $scope['regions']->count();
        $nbDepts = (int) $scope['depts']->count();
        $nbCommunes = (int) $scope['communes']->count();
        $nbLocalites = (int) $scope['localites']->count();

        // ── Cartes de sous-niveaux en cascade (contenus dans le périmètre) ──
        $cascade = match ($niveau) {
            'region' => [
                ['label' => 'Nbre de départements', 'value' => $nbDepts, 'icon' => 'fa-code-branch'],
                ['label' => 'Nbre de communes', 'value' => $nbCommunes, 'icon' => 'fa-city'],
            ],
            'departement' => [
                ['label' => 'Nbre de communes', 'value' => $nbCommunes, 'icon' => 'fa-city'],
            ],
            null => [
                ['label' => 'Nbre de régions', 'value' => $nbRegions, 'icon' => 'fa-layer-group'],
            ],
            default => [],
        };

        // ── Référentiels de libellés pour regrouper selon le niveau ──
        $nomRegion = $regions->pluck('nom', 'id');
        $nomDept = $regionDepts->pluck('nom', 'id');
        $regionDeDept = $regionDepts->pluck('region_id', 'id');
        $nomCommune = $communes->pluck('nom', 'id');
        $nomLocalite = $localites->pluck('nom', 'id');

        // ── Graphiques (bornés au périmètre) ──
        $parSecteur = $infras->groupBy('secteur_id')
            ->map(fn ($g) => ['nom' => $g->first()->secteur?->nom ?? 'Inconnu', 'total' => $g->count()])
            ->values()->sortByDesc('total')->values();

        $secteursDetail = $infras->groupBy(fn ($r) => $r->secteur?->nom ?? 'Inconnu')
            ->map(function ($groupe, $secteur) {
                $parType = $groupe
                    ->groupBy(fn ($r) => $r->type_infrastructure ?: 'Non précisé')
                    ->map->count()
                    ->sortDesc();

                return [
                    'secteur' => $secteur,
                    'total'   => $groupe->count(),
                    'types'   => $parType
                        ->map(fn ($n, $t) => ['nom' => $t, 'total' => $n])
                        ->values(),
                ];
            })
            ->values();

        $parEtat = $infras->groupBy(fn ($r) => $r->etat_lieu ?: 'Non renseigné')
            ->map(fn ($g, $e) => ['nom' => $e, 'total' => $g->count()])
            ->values()->sortByDesc('total')->values();

        // ___ Répartition adaptative par sous-unité du niveau choisi (doughnut + top) ___
        // global → régions ; région → départements ; département → communes ;
        // commune → localités couvertes ; localité → aucun (périmètre unique).
        $repartitionLabel = match ($niveau) {
            'region'      => 'Départements',
            'departement' => 'Communes',
            'commune'     => 'Localités',
            'localite'    => '',
            default       => 'Régions',
        };

        if ($niveau === 'commune') {
            $repartition = DB::table('localite_couverts')->whereIn('infrastructure_id', $infraIds)
                ->get()
                ->groupBy('localite_id')
                ->map(fn ($g, $lid) => ['nom' => $nomLocalite[$lid] ?? '—', 'total' => $g->count()])
                ->values()->sortByDesc('total')->values();
        } elseif ($niveau === 'departement') {
            $repartition = $infras->groupBy(fn ($r) => $r->commune_id ?? '—')
                ->reject(fn ($g, $cid) => $cid === '—')
                ->map(fn ($g, $cid) => ['nom' => $nomCommune[$cid] ?? '—', 'total' => $g->count()])
                ->values()->sortByDesc('total')->values();
        } elseif ($niveau === 'region' || $niveau === null) {
            // Une infrastructure est rattachée soit au département soit à la
            // commune : on déduit le département via l'un ou l'autre.
            $deptDeCommune = $communes->pluck('departement_id', 'id');
            $sousUniteDe = fn ($r) => $r->departement_id ?: ($deptDeCommune[$r->commune_id] ?? null);

            if ($niveau === 'region') {
                $repartition = $infras->groupBy(fn ($r) => $sousUniteDe($r))
                    ->reject(fn ($g, $did) => $did === null)
                    ->map(fn ($g, $did) => ['nom' => $nomDept[$did] ?? '—', 'total' => $g->count()])
                    ->values()->sortByDesc('total')->values();
            } else {
                $repartition = $infras->groupBy(fn ($r) => $regionDeDept[$sousUniteDe($r)] ?? null)
                    ->reject(fn ($g, $rid) => $rid === null)
                    ->map(fn ($g, $rid) => ['nom' => $nomRegion[$rid] ?? '—', 'total' => $g->count()])
                    ->values()->sortByDesc('total')->values();
            }
        } else {
            $repartition = collect();
        }
        $topUnits = $repartition->sortByDesc('total')->take(10)->values();

        // ___ Population hommes / femmes par sous-unité (graphe empilé) ___
        // global → régions ; région → départements ; département → communes ;
        // commune → localités ; localité → aucun (un seul enregistrement).
        if ($niveau === 'commune') {
            $hfRows = Localite::whereIn('id', $scope['localites'])->get(['id', 'nom', 'nbre_homme', 'nbre_femme']);
        } elseif ($niveau === 'departement') {
            $hfRows = Commune::whereIn('id', $scope['communes'])->get(['id', 'nom', 'nbre_homme', 'nbre_femme']);
        } elseif ($niveau === 'region') {
            $hfRows = Departement::whereIn('id', $scope['depts'])->get(['id', 'nom', 'nbre_homme', 'nbre_femme']);
        } elseif ($niveau === null) {
            $hfRows = Region::whereIn('id', $scope['regions'])->get(['id', 'nom', 'nbre_homme', 'nbre_femme']);
        } else {
            $hfRows = collect();
        }
        $populationHf = $hfRows
            ->map(fn ($r) => ['nom' => $r->nom, 'hommes' => (int) $r->nbre_homme, 'femmes' => (int) $r->nbre_femme])
            ->sortByDesc(fn ($r) => $r['hommes'] + $r['femmes'])
            ->values();

        $evolution = $infras->filter(fn ($i) => $i->date_creation)
            ->map(fn ($i) => (int) $i->date_creation->format('Y'))
            ->countBy()
            ->map(fn ($c, $y) => ['annee' => (int) $y, 'total' => $c])
            ->sortBy('annee')->values();

        return view('PageAdmi.StatsAdmi.VueGenerale', compact(
            'regions', 'regionDepts', 'communes', 'localites',
            'niveau', 'entiteId', 'scopeLabel',
            'totalInfra',
            'popTotale', 'menages',
            'nbRegions', 'nbDepts', 'nbCommunes', 'nbLocalites',
            'cascade', 'repartition', 'repartitionLabel', 'topUnits', 'populationHf',
            'parSecteur', 'secteursDetail', 'parEtat', 'evolution'
        ));
    }

    /**
     * Détermine le périmètre territorial du filtre : les identifiants des
     * régions, départements, communes et localités inclus dans le niveau choisi.
     * Laisse-faire : si aucun niveau n'est sélectionné, tout le territoire.
     *
     * @return array<string, \Illuminate\Support\Collection>
     */
    private function scopeTerritoriale(?string $niveau, int $entiteId): array
    {
        $regions = Region::pluck('id');
        $depts = Departement::pluck('id');
        $communes = Commune::pluck('id');
        $localites = Localite::pluck('id');

        if ($niveau === 'region') {
            $regions = $entiteId ? collect([$entiteId]) : $regions;
            $depts = Departement::whereIn('region_id', $regions)->pluck('id');
        } elseif ($niveau === 'departement') {
            $depts = $entiteId ? collect([$entiteId]) : $depts;
            $regions = Departement::whereIn('id', $depts)->pluck('region_id');
        } elseif ($niveau === 'commune') {
            $communes = $entiteId ? collect([$entiteId]) : $communes;
            $depts = Commune::whereIn('id', $communes)->pluck('departement_id')->unique()->values();
            $regions = Departement::whereIn('id', $depts)->pluck('region_id');
        } elseif ($niveau === 'localite') {
            $localites = $entiteId ? collect([$entiteId]) : $localites;
            $communes = Localite::whereIn('id', $localites)->pluck('commune_id')->unique()->values();
            $depts = Commune::whereIn('id', $communes)->pluck('departement_id')->unique()->values();
            $regions = Departement::whereIn('id', $depts)->pluck('region_id');
        }

        // Dérivation descendante (communes / localités du périmètre)
        $communes = Commune::whereIn('departement_id', $depts)->pluck('id');
        $localites = Localite::whereIn('commune_id', $communes)->pluck('id');

        return compact('regions', 'depts', 'communes', 'localites');
    }

    /**
     * Page Indicateurs — analyse détaillée d'un SECTEUR précis.
     * Filtres : secteur (obligatoire) → région → niveau territorial
     * (département / commune / localité) → entité précise.
     * Propose des agrégations 100% base de données : grand tableau croisé
     * (entités × indicateurs), valeurs par type d'infrastructure, valeur par
     * localité (population couverte + nb d'infrastructures) et graphiques.
     */
    public function indicateur(Request $request)
    {
        // ── Filtres ──
        $secteurId = $request->integer('secteur');
        $regionId = $request->integer('region');
        $niveau = in_array($request->get('niveau'), ['region', 'departement', 'commune', 'localite'], true)
            ? $request->get('niveau')
            : 'region';
        $entiteId = $request->integer('entite');

        // ── Niveau régional : la région choisie pilote l'analyse (pas de comparaison) ──
        // La région peut venir du filtre « Région » ou de l'entité quand niveau = région.
        // Sans région ni entité sélectionnée, le secteur est analysé sur TOUT le
        // territoire : on affiche alors les statistiques globales du secteur.
        if ($niveau === 'region') {
            $regionId = $regionId ?: $entiteId;
        }

        // Référentiels pour les filtres en cascade (avec région dérivée) : keep
        $secteurs = Secteur::withCount('infrastructures')->orderBy('nom')->get();
        $regions = Region::orderBy('nom')->get();
        $regionDepts = Departement::select('id', 'nom', 'region_id')->orderBy('nom')->get();

        $communes = Commune::select('communes.id', 'communes.nom', 'communes.departement_id', 'departements.region_id')
            ->join('departements', 'departements.id', '=', 'communes.departement_id')
            ->orderBy('communes.nom')
            ->get();

        $localites = Localite::select('localites.id', 'localites.nom', 'localites.commune_id', 'departements.region_id')
            ->join('communes', 'communes.id', '=', 'localites.commune_id')
            ->join('departements', 'departements.id', '=', 'communes.departement_id')
            ->orderBy('localites.nom')
            ->get();

        // ── Libellés du périmètre sélectionné (bandeau secteur) ──
        $regionNom = $regionId ? (Region::find($regionId)?->nom ?? '') : '';
        $entiteNom = '';
        if ($entiteId && $niveau !== 'region') {
            $entiteList = match ($niveau) {
                'departement' => $regionDepts,
                'commune'     => $communes,
                default       => $localites,
            };
            $entiteNom = $entiteList->firstWhere('id', $entiteId)?->nom ?? '';
        }

        // ── Secteur sélectionné → analyse ──
        $secteur = $secteurId ? Secteur::with('indicateurs')->find($secteurId) : null;
        $stats = null;

        if ($secteur) {
            $query = Infrastructure::query()
                ->with(['commune.departement.region', 'departement.region', 'secteur', 'localitesCouvertes', 'indicateurs'])
                ->where('secteur_id', $secteur->id);

            // Filtre région : infrastructure rattachée à un département OU une commune de la région
            if ($regionId) {
                $deptRegion = Departement::where('region_id', $regionId)->pluck('id');
                if ($deptRegion->isNotEmpty()) {
                    $communesRegion = Commune::whereIn('departement_id', $deptRegion)->pluck('id');
                    $query->where(function ($q) use ($deptRegion, $communesRegion) {
                        $q->whereIn('departement_id', $deptRegion);
                        if ($communesRegion->isNotEmpty()) {
                            $q->orWhereIn('commune_id', $communesRegion);
                        }
                    });
                } else {
                    // Région sans département : aucun résultat possible
                    $query->whereRaw('1 = 0');
                }
            }

            // Filtre entité du niveau choisi
            if ($niveau === 'departement' && $entiteId) {
                $communesDept = Commune::where('departement_id', $entiteId)->pluck('id');
                $query->where(function ($q) use ($entiteId, $communesDept) {
                    $q->where('departement_id', $entiteId);
                    if ($communesDept->isNotEmpty()) {
                        $q->orWhereIn('commune_id', $communesDept);
                    }
                });
            } elseif ($niveau === 'commune' && $entiteId) {
                $query->where('commune_id', $entiteId);
            } elseif ($niveau === 'localite' && $entiteId) {
                $query->whereHas('localitesCouvertes', fn ($q) => $q->where('localite_id', $entiteId));
            }

            $infras = $query->get();
            $stats = $this->indicateurStats($secteur, $infras, $niveau);
        }

        return view('PageAdmi.StatsAdmi.Indicateur', compact(
            'secteurs', 'regions', 'regionDepts', 'communes', 'localites',
            'secteur', 'secteurId', 'regionId', 'regionNom', 'niveau', 'entiteId', 'entiteNom',
            'stats'
        ));
    }

    /**
     * Agrège les statistiques du secteur sur un lot d'infrastructures filtrées.
     * Retourne un tableau prêt pour la vue : KPIs, grand tableau croisé et
     * tableaux annexes, données pour graphiques.
     *
     * @param  \App\Models\Secteur     $secteur  secteur analysé
     * @param  \Illuminate\Support\Collection $infras   infrastructures dans le périmètre filtré
     * @param  string                   $niveau   niveau territorial (departement|commune|localite)
     */
    private function indicateurStats(Secteur $secteur, $infras, string $niveau): array
    {
        $indicateurs = $secteur->indicateurs; // indicateurs du secteur (colonnes)
        $indicIds = $indicateurs->pluck('id');

        // ── KPIs ──
        $totalInfra = $infras->count();
        $totalIndicateurs = $indicateurs->count();

        $totalMesures = 0;
        foreach ($infras as $infra) {
            foreach ($infra->indicateurs as $ind) {
                if ($indicIds->contains($ind->id) && $ind->pivot->valeur !== null) {
                    $totalMesures++;
                }
            }
        }

        // ── Grand tableau croisé : entités du niveau choisi × indicateurs ──
        // clé => ['nom', 'nb', 'vals' => [indicateur_id => somme]]
        $matrice = [];

        // ── Valeurs par localité (population couverte + nb infra, option retenue) ──
        // clé localite_id => ['nom','commune','hommes','femmes','nbInfra','pop']
        $localitesTab = [];
        $populationCouverte = 0.0;

        // ── Répartition par type d'infrastructure et par état ──
        $parTypes = [];
        $etat = [];

        foreach ($infras as $infra) {
            // -- entités cibles pour le grand tableau (selon le niveau) --
            $targets = [];
            if ($niveau === 'region') {
                // Niveau régional : la région choisie (filtre Région) est le cadre,
                // le grand tableau agrège donc sur cette seule région.
                $reg = $infra->departement?->region ?? $infra->commune?->departement?->region;
                $targets[$reg ? 'r'.$reg->id : 'nr'.$infra->id] = $reg->nom ?? '—';
            } elseif ($niveau === 'departement') {
                $dept = $infra->departement ?? $infra->commune?->departement;
                $targets[$dept ? 'd'.$dept->id : 'nd'.$infra->id] = $dept->nom ?? $infra->commune?->nom ?? '—';
            } elseif ($niveau === 'commune') {
                $targets[$infra->commune ? 'c'.$infra->commune->id : 'dc'.$infra->id] =
                    $infra->commune?->nom ?? ($infra->departement?->nom.' (dépt.)');
            } else { // localite
                if ($infra->localitesCouvertes->isNotEmpty()) {
                    foreach ($infra->localitesCouvertes as $lc) {
                        $targets['l'.$lc->id] = $lc->nom;
                    }
                } else {
                    $targets['nc'.$infra->id] = $infra->commune?->nom ?? $infra->departement?->nom ?? '—';
                }
            }

            foreach ($targets as $key => $nom) {
                if (! isset($matrice[$key])) {
                    $matrice[$key] = ['nom' => $nom, 'nb' => 0, 'vals' => []];
                }
                $matrice[$key]['nb']++;
                foreach ($infra->indicateurs as $ind) {
                    if ($indicIds->contains($ind->id) && $ind->pivot->valeur !== null) {
                        $matrice[$key]['vals'][$ind->id] = ($matrice[$key]['vals'][$ind->id] ?? 0) + (float) $ind->pivot->valeur;
                    }
                }
            }

            // -- localités couvertes : population couverte, nb d'infras, H/F --
            foreach ($infra->localitesCouvertes as $lc) {
                $pop = (float) ($lc->pivot->nbre_population_couvert ?? 0);
                $populationCouverte += $pop;
                $r = $localitesTab[$lc->id] ??= [
                    'nom'     => $lc->nom,
                    'commune' => $lc->commune?->nom ?? $infra->commune?->nom ?? '—',
                    'hommes'  => (int) ($lc->nbre_homme ?? 0),
                    'femmes'  => (int) ($lc->nbre_femme ?? 0),
                    'nbInfra' => 0,
                    'pop'     => 0.0,
                ];
                $r['nbInfra']++;
                $r['pop'] += $pop;
                $localitesTab[$lc->id] = $r;
            }

            // -- répartition par type --
            $type = trim((string) $infra->type_infrastructure) ?: 'Non renseigné';
            if (! isset($parTypes[$type])) {
                $parTypes[$type] = ['nom' => $type, 'nb' => 0, 'vals' => []];
            }
            $parTypes[$type]['nb']++;
            foreach ($infra->indicateurs as $ind) {
                if ($indicIds->contains($ind->id) && $ind->pivot->valeur !== null) {
                    $parTypes[$type]['vals'][$ind->id] = ($parTypes[$type]['vals'][$ind->id] ?? 0) + (float) $ind->pivot->valeur;
                }
            }

            // -- répartition par état --
            $etatNom = trim((string) $infra->etat_lieu) ?: 'Non renseigné';
            $etat[$etatNom] = ($etat[$etatNom] ?? 0) + 1;
        }

        // Tri des lignes par nom
        uasort($matrice, fn ($a, $b) => strcasecmp($a['nom'], $b['nom']));
        uasort($parTypes, fn ($a, $b) => strcasecmp($a['nom'], $b['nom']));
        uasort($localitesTab, fn ($a, $b) => strcasecmp($a['nom'], $b['nom']));

        // ── Données pour les graphiques ──
        // Un bar chart par indicateur : valeurs par entité du niveau choisi
        $indicateurCharts = [];
        foreach ($indicateurs as $ind) {
            $labels = [];
            $data = [];
            foreach ($matrice as $row) {
                if (isset($row['vals'][$ind->id])) {
                    $labels[] = $row['nom'];
                    $data[] = round((float) $row['vals'][$ind->id], 2);
                }
            }
            if ($labels) {
                $indicateurCharts[] = [
                    'id'     => $ind->id,
                    'nom'    => $ind->nom_indicateur,
                    'unites' => $ind->unites,
                    'labels' => $labels,
                    'data'   => $data,
                ];
            }
        }

        $typesChart = collect($parTypes)->map(fn ($t) => ['nom' => $t['nom'], 'total' => $t['nb']])->values()->all();
        $etatChart = collect($etat)->map(fn ($total, $nom) => ['nom' => $nom, 'total' => $total])->values()->all();
        $hfChart = collect($localitesTab)->map(fn ($l) => [
            'nom'    => $l['nom'],
            'hommes' => $l['hommes'],
            'femmes' => $l['femmes'],
        ])->values()->all();
        $popChart = collect($localitesTab)->map(fn ($l) => [
            'nom' => $l['nom'],
            'pop' => $l['pop'],
        ])->values()->all();

        // ── Comparateur d'indicateurs (choisir 2+ indicateurs, ex. garçons/filles) ──
        $zones = [];
        foreach ($matrice as $row) {
            if (! in_array($row['nom'], $zones)) {
                $zones[] = $row['nom'];
            }
        }
        $comparateur = ['totaux' => [], 'parZone' => ['labels' => $zones, 'series' => []]];
        foreach ($indicateurs as $ind) {
            $total   = 0.0;
            $notes   = array_fill(0, count($zones), 0.0);
            $mesure  = false;
            foreach ($matrice as $row) {
                if (isset($row['vals'][$ind->id])) {
                    $idx = array_search($row['nom'], $zones);
                    $val = round((float) $row['vals'][$ind->id], 2);
                    $notes[$idx] = $val;
                    $total += $val;
                    $mesure = true;
                }
            }
            if (! $mesure) {
                continue;
            }
            $comparateur['totaux'][] = [
                'id'     => $ind->id,
                'nom'    => $ind->nom_indicateur,
                'unites' => $ind->unites,
                'total'  => round($total, 2),
            ];
            $comparateur['parZone']['series'][] = [
                'id'     => $ind->id,
                'nom'    => $ind->nom_indicateur,
                'unites' => $ind->unites,
                'data'   => $notes,
            ];
        }

        return [
            'totalInfra'          => $totalInfra,
            'totalIndicateurs'    => $totalIndicateurs,
            'totalMesures'        => $totalMesures,
            'populationCouverte'  => (int) round($populationCouverte),
            'totalLocalites'      => count($localitesTab),
            'matrice'             => array_values($matrice),
            'parTypes'            => array_values($parTypes),
            'parEtat'             => $etat,
            'localitesTable'      => array_values($localitesTab),
            'indicateurCharts'    => $indicateurCharts,
            'comparateur'         => $comparateur,
            'typesChart'          => $typesChart,
            'etatChart'           => $etatChart,
            'hfChart'             => $hfChart,
            'popChart'            => $popChart,
        ];
    }
}

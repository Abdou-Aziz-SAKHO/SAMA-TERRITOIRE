<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Departement;
use App\Models\Commune;
use App\Models\Localite;
use App\Models\Secteur;
use App\Models\Infrastructure;
use App\Models\Indicateur;
use App\Models\Photo;
use App\Models\Actualite;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class DonneesController extends Controller
{
    /**
     * Affiche la page de gestion des données avec filtres cascendants.
     *
     * Filtres supportés :
     * - tab : onglet actif (regions, departements, communes, localites, secteurs, infrastructures)
     * - region_id : filtre les départements par région
     * - departement_id : filtre les communes par département
     * - commune_id : filtre les localités par commune
     * - secteur_id : filtre les infrastructures par secteur
     * - localite_id : filtre les infrastructures par localité
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'regions');

        // Récupération des valeurs de filtres
        $regionId = $request->get('region_id');
        $departementId = $request->get('departement_id');
        $communeId = $request->get('commune_id');
        $secteurId = $request->get('secteur_id');
        $localiteId = $request->get('localite_id');

        // ── Régions : pas de filtre parent ──
        $regions = Region::orderBy('nom')->paginate(10, ['*'], 'regions_page');

        // ── Départements : filtrés par région si sélectionnée ──
        $departements = Departement::with('region')
            ->when($regionId, fn($q) => $q->where('region_id', $regionId))
            ->orderBy('nom')
            ->paginate(10, ['*'], 'departements_page');

        // ── Communes : filtrées par département SI choisi, sinon par la région
        //    (une commune est visible si le département auquel elle appartient
        //    se situe dans la région sélectionnée).
        $communes = Commune::with('departement')
            ->when($departementId, fn($q) => $q->where('departement_id', $departementId))
            ->when(!$departementId && $regionId, fn($q) => $q->whereHas('departement', fn($d) => $d->where('region_id', $regionId)))
            ->orderBy('nom')
            ->paginate(10, ['*'], 'communes_page');

        // ── Localités : filtres cascadants ──
        //    Priorité : commune > département > région.
        //    - Une commune choisie affiche ses localités.
        //    - Sinon un département affiche les localités de ses communes.
        //    - Sinon une région affiche les localités des communes de ses départements.
        $localites = Localite::with('commune')
            ->when($communeId, fn($q) => $q->where('commune_id', $communeId))
            ->when(!$communeId && $departementId, fn($q) => $q->whereHas('commune', fn($c) => $c->where('departement_id', $departementId)))
            ->when(!$communeId && !$departementId && $regionId, fn($q) => $q->whereHas('commune.departement', fn($d) => $d->where('region_id', $regionId)))
            ->orderBy('nom')
            ->paginate(10, ['*'], 'localites_page');

        // ── Secteurs : pas de filtre parent ──
        $secteurs = Secteur::orderBy('nom')->paginate(10, ['*'], 'secteurs_page');

        // ── Infrastructures : rattachées à 1 département OU 1 commune OU 1..n localités ──
        // Les filtres région/département "traversent" la commune quand l'infrastructure
        // n'est pas rattachée directement à un département.
        $infrastructures = Infrastructure::with(['departement', 'commune.departement', 'secteur', 'localitesCouvertes', 'indicateurs'])
            ->when($regionId, fn($q) => $q->where(function ($q) use ($regionId) {
                // Via le département direct OU via le département de la commune
                $q->whereHas('departement', fn($d) => $d->where('region_id', $regionId))
                  ->orWhereHas('commune.departement', fn($d) => $d->where('region_id', $regionId));
            }))
            ->when($departementId, fn($q) => $q->where(function ($q) use ($departementId) {
                // Rattachement direct au département OU via sa commune
                $q->where('departement_id', $departementId)
                  ->orWhereHas('commune', fn($c) => $c->where('departement_id', $departementId));
            }))
            ->when($communeId, fn($q) => $q->where('commune_id', $communeId))
            ->when($secteurId, fn($q) => $q->where('secteur_id', $secteurId))
            // Filtre par localité : passe désormais par la table des localités COUVERTES
            ->when($localiteId, fn($q) => $q->whereHas('localitesCouvertes', fn($l) => $l->where('localites.id', $localiteId)))
            ->orderBy('nom')
            ->paginate(10, ['*'], 'infrastructures_page');

        // Données pour les dropdowns de filtres et formulaires
        $allRegions = Region::orderBy('nom')->get();
        $allDepartements = Departement::orderBy('nom')->get();
        $allCommunes = Commune::orderBy('nom')->get();
        $allSecteurs = Secteur::orderBy('nom')->get();
        $allLocalites = Localite::orderBy('nom')->get();
        // Tous les indicateurs (pour alimenter les listes de valeurs par secteur côté client)
        $allIndicateurs = Indicateur::orderBy('nom_indicateur')->get();

        return view('PageAdmi.DonneesAdmi', compact(
            'tab',
            'regions', 'departements', 'communes', 'localites', 'secteurs', 'infrastructures',
            'allRegions', 'allDepartements', 'allCommunes', 'allSecteurs', 'allLocalites', 'allIndicateurs',
            'regionId', 'departementId', 'communeId', 'secteurId', 'localiteId'
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  MÉTHODES STORE — Création de chaque entité
    //  Chaque méthode valide les données, crée l'enregistrement,
    //  et redirige vers la page avec un message de succès.
    // ══════════════════════════════════════════════════════════

    /**
     * Créer une nouvelle région
     */
    public function storeRegion(Request $request)
    {
        $validated = $request->validate([
            'nom'                => 'required|string|max:255|unique:regions,nom',
            'nbre_infrastructure'=> 'nullable|integer|min:0',
        ] + $this->reglesStats());

        Region::create($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'regions'])
            ->with('success', 'Région créée avec succès !');
    }

    /**
     * Créer un nouveau département
     */
    public function storeDepartement(Request $request)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:255',
            'region_id'         => 'required|exists:regions,id',
        ] + $this->reglesStats());

        Departement::create($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'departements'])
            ->with('success', 'Département créé avec succès !');
    }

    /**
     * Créer une nouvelle commune
     */
    public function storeCommune(Request $request)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:255',
            'departement_id'    => 'required|exists:departements,id',
        ] + $this->reglesStats());

        Commune::create($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'communes'])
            ->with('success', 'Commune créée avec succès !');
    }

    /**
     * Créer une nouvelle localité
     */
    public function storeLocalite(Request $request)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:255',
            // seule la commune est enregistrée ; les champs region_filtre /
            // departement_filtre servent d'aide à la recherche côté client.
            'commune_id'        => [
                'required',
                'exists:communes,id',
                // Cohérence : on accepte le couple (region_filtre, departement_filtre)
                // envoyé par le formulaire pour vérifier que la commune choisie appartient
                // bien au département sélectionné (défense en profondeur).
                function ($attribute, $value, $fail) use ($request) {
                    $commune = Commune::find($value);
                    $deptFiltre = $request->filled('departement_filtre') ? (int) $request->input('departement_filtre') : null;
                    // Si un département a été choisi comme filtre, la commune doit y appartenir
                    if ($commune && $deptFiltre && (int) $commune->departement_id !== $deptFiltre) {
                        $fail('La commune sélectionnée n\'appartient pas au département choisi.');
                    }
                },
            ],
        ] + $this->reglesStats());

        Localite::create($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'localites'])
            ->with('success', 'Localité créée avec succès !');
    }

    /**
     * Créer un nouveau secteur, avec ses indicateurs (critères de mesure).
     *
     * Les indicateurs sont facultatifs : on peut créer un secteur "nu" puis
     * ajouter/modifier ses indicateurs plus tard. Chaque indicateur soumis
     * doit avoir au minimum un nom ; l'unité et la description sont facultatives.
     */
    public function storeSecteur(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:secteurs,nom',
            // Tableau d'indicateurs à créer en même temps que le secteur.
            // Format attendu : indicateurs[0][nom_indicateur], [unites], [description]...
            'indicateurs'              => 'nullable|array',
            'indicateurs.*.nom_indicateur' => 'required_with:indicateurs|string|max:255',
            'indicateurs.*.unites'         => 'nullable|string|max:255',
            'indicateurs.*.description'    => 'nullable|string',
        ]);

        // Création atomique : le secteur et ses indicateurs dans une seule transaction
        DB::transaction(function () use ($validated) {
            $secteur = Secteur::create(collect($validated)->except('indicateurs')->all());

            // On ne crée que les indicateurs dont le nom n'est pas vide
            foreach (($validated['indicateurs'] ?? []) as $indicateur) {
                if (empty(trim($indicateur['nom_indicateur'] ?? ''))) {
                    continue;
                }
                $secteur->indicateurs()->create([
                    'nom_indicateur' => trim($indicateur['nom_indicateur']),
                    'unites'         => trim($indicateur['unites'] ?? '') ?: null,
                    'description'    => trim($indicateur['description'] ?? '') ?: null,
                ]);
            }
        });

        return redirect()->route('DonneesAdmi', ['tab' => 'secteurs'])
            ->with('success', 'Secteur créé avec succès !');
    }

    // ══════════════════════════════════════════════════════════
    //  MÉTHODES UPDATE — Modification de chaque entité
    //  Mêmes règles que la création, mais l'unicité du nom ignore
    //  l'enregistrement en cours de modification.
    // ══════════════════════════════════════════════════════════

    /**
     * Modifier une région
     */
    public function updateRegion(Request $request, Region $region)
    {
        $validated = $request->validate([
            // unique ... ->ignore() : autorise de garder le nom actuel de la région
            'nom' => ['required', 'string', 'max:255', Rule::unique('regions', 'nom')->ignore($region->id)],
        ] + $this->reglesStats());

        $region->update($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'regions'])
            ->with('success', 'Région modifiée avec succès !');
    }

    /**
     * Modifier un département
     */
    public function updateDepartement(Request $request, Departement $departement)
    {
        $validated = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'region_id' => ['required', 'exists:regions,id'],
        ] + $this->reglesStats());

        $departement->update($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'departements'])
            ->with('success', 'Département modifié avec succès !');
    }

    /**
     * Modifier une commune
     */
    public function updateCommune(Request $request, Commune $commune)
    {
        $validated = $request->validate([
            'nom'            => ['required', 'string', 'max:255'],
            'departement_id' => ['required', 'exists:departements,id'],
        ] + $this->reglesStats());

        $commune->update($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'communes'])
            ->with('success', 'Commune modifiée avec succès !');
    }

    /**
     * Modifier une localité
     */
    public function updateLocalite(Request $request, Localite $localite)
    {
        $validated = $request->validate([
            'nom'        => ['required', 'string', 'max:255'],
            'commune_id' => ['required', 'exists:communes,id'],
        ] + $this->reglesStats());

        $localite->update($validated);

        return redirect()->route('DonneesAdmi', ['tab' => 'localites'])
            ->with('success', 'Localité modifiée avec succès !');
    }

    /**
     * Modifier un secteur (nom) ainsi que ses indicateurs :
     * renommer les existants, en ajouter de nouveaux, en supprimer.
     */
    public function updateSecteur(Request $request, Secteur $secteur)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('secteurs', 'nom')->ignore($secteur->id)],
            // Indicateurs existants : tableau indexé par leur id
            'indicateurs'                    => 'nullable|array',
            'indicateurs.*.nom_indicateur'   => 'required_with:indicateurs|string|max:255',
            'indicateurs.*.unites'           => 'nullable|string|max:255',
            'indicateurs.*.description'      => 'nullable|string',
            // Ids des indicateurs existants à supprimer
            'indicateurs_supprimer'          => 'nullable|array',
            'indicateurs_supprimer.*'        => 'integer|exists:indicateurs,id',
            // Nouveaux indicateurs à ajouter
            'indicateurs_nouveaux'           => 'nullable|array',
            'indicateurs_nouveaux.*.nom_indicateur' => 'required_with:indicateurs_nouveaux|string|max:255',
            'indicateurs_nouveaux.*.unites'  => 'nullable|string|max:255',
            'indicateurs_nouveaux.*.description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $secteur, $validated) {
            $secteur->update(collect($validated)->except([
                'indicateurs', 'indicateurs_supprimer', 'indicateurs_nouveaux',
            ])->all());

            // 1) Mise à jour des indicateurs existants (renommés/remplis)
            foreach (($validated['indicateurs'] ?? []) as $id => $donnees) {
                // On ne modifie qu'un indicateur appartenant réellement à ce secteur
                if ($indicateur = $secteur->indicateurs()->find($id)) {
                    $indicateur->update([
                        'nom_indicateur' => trim($donnees['nom_indicateur']),
                        'unites'         => trim($donnees['unites'] ?? '') ?: null,
                        'description'    => trim($donnees['description'] ?? '') ?: null,
                    ]);
                }
            }

            // 2) Suppression des indicateurs demandés (les liaisons vers les
            //    infrastructures sont retirées par cascade sur la pivot)
            $idsASupprimer = $validated['indicateurs_supprimer'] ?? [];
            if (!empty($idsASupprimer)) {
                $secteur->indicateurs()->whereIn('id', $idsASupprimer)->delete();
            }

            // 3) Ajout des nouveaux indicateurs
            foreach (($validated['indicateurs_nouveaux'] ?? []) as $nouveau) {
                if (empty(trim($nouveau['nom_indicateur'] ?? ''))) {
                    continue;
                }
                $secteur->indicateurs()->create([
                    'nom_indicateur' => trim($nouveau['nom_indicateur']),
                    'unites'         => trim($nouveau['unites'] ?? '') ?: null,
                    'description'    => trim($nouveau['description'] ?? '') ?: null,
                ]);
            }
        });

        return redirect()->route('DonneesAdmi', ['tab' => 'secteurs'])
            ->with('success', 'Secteur modifié avec succès !');
    }

    // ══════════════════════════════════════════════════════════
    //  MÉTHODES DESTROY — Suppression de chaque entité
    //  Les clés étrangères RESTRICT protègent les parents utilisés :
    //  on intercepte l'erreur SQL pour afficher un message clair.
    // ══════════════════════════════════════════════════════════

    /**
     * Supprimer une région (bloquée si elle contient encore des départements)
     */
    public function destroyRegion(Region $region)
    {
        try {
            $region->delete();
            return redirect()->route('DonneesAdmi', ['tab' => 'regions'])
                ->with('success', 'Région supprimée avec succès !');
        } catch (QueryException $e) {
            return redirect()->route('DonneesAdmi', ['tab' => 'regions'])
                ->with('error', 'Suppression impossible : cette région contient encore des départements.');
        }
    }

    /**
     * Supprimer un département (bloqué s'il contient encore des communes)
     */
    public function destroyDepartement(Departement $departement)
    {
        try {
            $departement->delete();
            return redirect()->route('DonneesAdmi', ['tab' => 'departements'])
                ->with('success', 'Département supprimé avec succès !');
        } catch (QueryException $e) {
            return redirect()->route('DonneesAdmi', ['tab' => 'departements'])
                ->with('error', 'Suppression impossible : ce département contient encore des communes.');
        }
    }

    /**
     * Supprimer une commune (bloquée si elle contient encore des localités)
     */
    public function destroyCommune(Commune $commune)
    {
        try {
            $commune->delete();
            return redirect()->route('DonneesAdmi', ['tab' => 'communes'])
                ->with('success', 'Commune supprimée avec succès !');
        } catch (QueryException $e) {
            return redirect()->route('DonneesAdmi', ['tab' => 'communes'])
                ->with('error', 'Suppression impossible : cette commune contient encore des localités.');
        }
    }

    /**
     * Supprimer une localité (bloquée si couverte par des infrastructures)
     */
    public function destroyLocalite(Localite $localite)
    {
        try {
            $localite->delete();
            return redirect()->route('DonneesAdmi', ['tab' => 'localites'])
                ->with('success', 'Localité supprimée avec succès !');
        } catch (QueryException $e) {
            return redirect()->route('DonneesAdmi', ['tab' => 'localites'])
                ->with('error', 'Suppression impossible : cette localité est couverte par des infrastructures.');
        }
    }

    /**
     * Supprimer un secteur (bloqué si des infrastructures y sont rattachées)
     */
    public function destroySecteur(Secteur $secteur)
    {
        try {
            $secteur->delete();
            return redirect()->route('DonneesAdmi', ['tab' => 'secteurs'])
                ->with('success', 'Secteur supprimé avec succès !');
        } catch (QueryException $e) {
            return redirect()->route('DonneesAdmi', ['tab' => 'secteurs'])
                ->with('error', 'Suppression impossible : ce secteur est utilisé par des infrastructures.');
        }
    }

    /**
     * Créer une nouvelle infrastructure.
     *
     * RÈGLE MÉTIER — l'infrastructure appartient à UN SEUL niveau territorial :
     *   - soit 1 département          (departement_id seul)
     *   - soit 1 commune              (commune_id seule)
     *   - soit 1..n localités         (commune_id + tableau localites[])
     *
     * La population couverte est calculée automatiquement : c'est la somme
     * des populations (taille_population) des localités sélectionnées.
     */
    public function storeInfrastructure(Request $request)
    {
        $validated = $request->validate($this->reglesInfrastructure($request));

        // ── Vérifications métier complémentaires (au-delà de la validation) ──

        // 0. Un rattachement territorial est obligatoire (département, commune ou localités)
        $verif = $this->verifierRattachement($validated);
        if ($verif !== null) {
            return back()->withErrors($verif)->withInput();
        }

        // 1. Les localités couvertes exigent une commune de rattachement
        if (!empty($validated['localites']) && empty($validated['commune_id'])) {
            return back()
                ->withErrors(['localites' => 'Les localités couvertes nécessitent de choisir une commune.'])
                ->withInput();
        }

        // 2. Toutes les localités choisies doivent appartenir à la commune choisie
        //    (le JS filtre déjà la liste, mais on ne fait jamais confiance au client)
        if (!empty($validated['localites'])) {
            $nbInvalides = Localite::whereIn('id', $validated['localites'])
                ->where('commune_id', '!=', $validated['commune_id'])
                ->count();

            if ($nbInvalides > 0) {
                return back()
                    ->withErrors(['localites' => 'Certaines localités sélectionnées n\'appartiennent pas à la commune choisie.'])
                    ->withInput();
            }
        }

        // ── Création en transaction : infrastructure + pivots en un seul bloc atomique ──
        DB::transaction(function () use ($validated) {
            // On retire "localites", "population_couverte" et "indicateurs_valeurs"
            // avant la création (ce ne sont pas des colonnes de la table infrastructures)
            $infrastructure = Infrastructure::create(
                collect($validated)->except(['localites', 'population_couverte', 'indicateurs_valeurs'])->all()
            );

            // Enregistrement des localités couvertes dans la pivot
            $this->synchroniserLocalitesCouvertes(
                $infrastructure,
                $validated['localites'] ?? [],
                isset($validated['population_couverte']) ? (int) $validated['population_couverte'] : null
            );

            // Enregistrement des valeurs des indicateurs du secteur dans la pivot
            $this->synchroniserValeursIndicateurs(
                $infrastructure,
                $validated['indicateurs_valeurs'] ?? []
            );
        });

        return redirect()->route('DonneesAdmi', ['tab' => 'infrastructures'])
            ->with('success', 'Infrastructure créée avec succès !');
    }

    /**
     * Modifier une infrastructure existante.
     * Mêmes règles que la création ; le nom unique ignore l'enregistrement courant.
     */
    public function updateInfrastructure(Request $request, Infrastructure $infrastructure)
    {
        $validated = $request->validate($this->reglesInfrastructure($request, $infrastructure->id));

        // Mêmes vérifications métier que lors de la création
        $verif = $this->verifierRattachement($validated);
        if ($verif !== null) {
            return back()->withErrors($verif)->withInput();
        }
        if (!empty($validated['localites']) && empty($validated['commune_id'])) {
            return back()
                ->withErrors(['localites' => 'Les localités couvertes nécessitent de choisir une commune.'])
                ->withInput();
        }
        if (!empty($validated['localites'])) {
            $nbInvalides = Localite::whereIn('id', $validated['localites'])
                ->where('commune_id', '!=', $validated['commune_id'])
                ->count();
            if ($nbInvalides > 0) {
                return back()
                    ->withErrors(['localites' => 'Certaines localités sélectionnées n\'appartiennent pas à la commune choisie.'])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($infrastructure, $validated) {
            $infrastructure->update(
                collect($validated)->except(['localites', 'population_couverte', 'indicateurs_valeurs'])->all()
            );

            // sync() remplace l'ancienne couverture par la nouvelle :
            // les localités décochées sont retirées, les nouvelles ajoutées.
            $this->synchroniserLocalitesCouvertes(
                $infrastructure,
                $validated['localites'] ?? [],
                isset($validated['population_couverte']) ? (int) $validated['population_couverte'] : null
            );

            // Mise à jour des valeurs des indicateurs (les absentes sont retirées,
            // d'où l'utilisation de sync() plutôt que attach())
            $this->synchroniserValeursIndicateurs(
                $infrastructure,
                $validated['indicateurs_valeurs'] ?? []
            );
        });

        return redirect()->route('DonneesAdmi', ['tab' => 'infrastructures'])
            ->with('success', 'Infrastructure modifiée avec succès !');
    }

    /**
     * Supprimer une infrastructure.
     * Les lignes de la pivot "localite_couverts" sont supprimées automatiquement
     * (cascadeOnDelete défini dans la migration).
     */
    public function destroyInfrastructure(Infrastructure $infrastructure)
    {
        try {
            $infrastructure->delete();
            return redirect()->route('DonneesAdmi', ['tab' => 'infrastructures'])
                ->with('success', 'Infrastructure supprimée avec succès !');
        } catch (QueryException $e) {
            return redirect()->route('DonneesAdmi', ['tab' => 'infrastructures'])
                ->with('error', 'Suppression impossible : cette infrastructure est référencée ailleurs (documents, photos...).');
        }
    }

    // ══════════════════════════════════════════════════════════
    //  IMPACT DE SUPPRESSION — alimente l'écran de confirmation
    // ══════════════════════════════════════════════════════════

    /**
     * Calcule et renvoie en JSON la liste complète de ce qui sera détruit
     * si l'utilisateur confirme la suppression d'un élément.
     * Appelé par le bouton "Supprimer" AVANT toute suppression effective :
     * l'écran de confirmation affiche le détail (arbre territorial en cascade,
     * infrastructures, actualités, documents...).
     */
    public function impact(string $type, int $id)
    {
        // Carte des types autorisés -> classe de modèle associée
        $modeles = [
            'region'         => Region::class,
            'departement'    => Departement::class,
            'commune'        => Commune::class,
            'localite'       => Localite::class,
            'secteur'        => Secteur::class,
            'infrastructure' => Infrastructure::class,
        ];

        // Type inconnu ou élément introuvable : réponse 404 JSON
        if (! isset($modeles[$type])) {
            return response()->json(['erreur' => 'Type inconnu'], 404);
        }
        $modele = $modeles[$type]::find($id);
        if (! $modele) {
            return response()->json(['erreur' => 'Élément introuvable'], 404);
        }

        // Chaque helper renvoie [titre, éléments, bloqué?, message?]
        [$titre, $elements, $bloque, $message] = match ($type) {
            'region'         => $this->impactRegion($modele),
            'departement'    => $this->impactDepartement($modele),
            'commune'        => $this->impactCommune($modele),
            'localite'       => $this->impactLocalite($modele),
            'secteur'        => $this->impactSecteur($modele),
            'infrastructure' => $this->impactInfrastructure($modele),
        };

        return response()->json([
            'titre'    => $titre,
            'bloque'   => $bloque,
            'message'  => $message,
            'elements' => $elements,
        ]);
    }

    /**
     * Région : la suppression cascade sur tout son arbre
     * (départements -> communes -> localités) + infrastructures rattachées.
     */
    private function impactRegion(Region $region): array
    {
        // On descend l'arbre : départements, puis leurs communes, puis leurs localités
        $departements = Departement::where('region_id', $region->id)->get();
        $idsDepts     = $departements->pluck('id');
        $communes     = Commune::whereIn('departement_id', $idsDepts)->get();
        $idsComs      = $communes->pluck('id');
        $localites    = Localite::whereIn('commune_id', $idsComs)->get();
        $idsLocs      = $localites->pluck('id');

        // Grâce à la contrainte CHECK, une infrastructure est liée soit au
        // département soit à la commune : additionner les deux ne compte rien en double.
        $nbInfra = Infrastructure::whereIn('departement_id', $idsDepts)->count()
                 + Infrastructure::whereIn('commune_id', $idsComs)->count();

        // Actualités et documents liés à N'IMPORTE QUEL niveau de l'arbre (cascade)
        $nbActus = Actualite::where(fn ($q) => $q->where('region_id', $region->id)
            ->orWhereIn('departement_id', $idsDepts)->orWhereIn('commune_id', $idsComs)
            ->orWhereIn('localite_id', $idsLocs))->count();
        $nbDocs = Document::where(fn ($q) => $q->where('region_id', $region->id)
            ->orWhereIn('departement_id', $idsDepts)->orWhereIn('commune_id', $idsComs)
            ->orWhereIn('localite_id', $idsLocs))->count();

        return [
            "Supprimer définitivement la région « {$region->nom} » ?",
            $this->filtreImpact([
                $this->ligneImpact('département(s)', $departements),
                $this->ligneImpact('commune(s)', $communes),
                $this->ligneImpact('localité(s)', $localites),
                ['label' => 'infrastructure(s) rattachée(s)', 'nombre' => $nbInfra],
                ['label' => 'actualité(s) liée(s)', 'nombre' => $nbActus],
                ['label' => 'document(s) lié(s)', 'nombre' => $nbDocs],
            ]),
            false,
            null,
        ];
    }

    /** Département : cascade sur communes -> localités + infrastructures du département. */
    private function impactDepartement(Departement $departement): array
    {
        $communes  = Commune::where('departement_id', $departement->id)->get();
        $idsComs   = $communes->pluck('id');
        $localites = Localite::whereIn('commune_id', $idsComs)->get();
        $idsLocs   = $localites->pluck('id');

        // Infrastructures liées au département OU aux communes de l'arbre (exclusives via CHECK)
        $nbInfra = Infrastructure::where('departement_id', $departement->id)->count()
                 + Infrastructure::whereIn('commune_id', $idsComs)->count();

        $nbActus = Actualite::where(fn ($q) => $q->where('departement_id', $departement->id)
            ->orWhereIn('commune_id', $idsComs)->orWhereIn('localite_id', $idsLocs))->count();
        $nbDocs = Document::where(fn ($q) => $q->where('departement_id', $departement->id)
            ->orWhereIn('commune_id', $idsComs)->orWhereIn('localite_id', $idsLocs))->count();

        return [
            "Supprimer définitivement le département « {$departement->nom} » ?",
            $this->filtreImpact([
                $this->ligneImpact('commune(s)', $communes),
                $this->ligneImpact('localité(s)', $localites),
                ['label' => 'infrastructure(s) rattachée(s)', 'nombre' => $nbInfra],
                ['label' => 'actualité(s) liée(s)', 'nombre' => $nbActus],
                ['label' => 'document(s) lié(s)', 'nombre' => $nbDocs],
            ]),
            false,
            null,
        ];
    }

    /** Commune : cascade sur ses localités + infrastructures rattachées à la commune. */
    private function impactCommune(Commune $commune): array
    {
        $localites = Localite::where('commune_id', $commune->id)->get();
        $idsLocs   = $localites->pluck('id');

        // Seules les infrastructures liées À LA COMMUNE disparaissent ;
        // celles rattachées par département restent intactes.
        $nbInfra = Infrastructure::where('commune_id', $commune->id)->count();

        $nbActus = Actualite::where(fn ($q) => $q->where('commune_id', $commune->id)
            ->orWhereIn('localite_id', $idsLocs))->count();
        $nbDocs = Document::where(fn ($q) => $q->where('commune_id', $commune->id)
            ->orWhereIn('localite_id', $idsLocs))->count();

        return [
            "Supprimer définitivement la commune « {$commune->nom} » ?",
            $this->filtreImpact([
                $this->ligneImpact('localité(s)', $localites),
                ['label' => 'infrastructure(s) rattachée(s) à cette commune', 'nombre' => $nbInfra],
                ['label' => 'actualité(s) liée(s)', 'nombre' => $nbActus],
                ['label' => 'document(s) lié(s)', 'nombre' => $nbDocs],
            ]),
            false,
            null,
        ];
    }

    /**
     * Localité : les infrastructures qui la couvrent sont CONSERVÉES
     * (elles restent rattachées à leur commune) ; seules les liaisons de
     * couverture et les actualités/documents liés disparaissent.
     */
    private function impactLocalite(Localite $localite): array
    {
        // Nombre d'infrastructures qui couvrent actuellement cette localité (pivot)
        $nbLiaisons = DB::table('localite_couverts')->where('localite_id', $localite->id)->count();

        $nbActus = Actualite::where('localite_id', $localite->id)->count();
        $nbDocs  = Document::where('localite_id', $localite->id)->count();

        return [
            "Supprimer définitivement la localité « {$localite->nom} » ?",
            $this->filtreImpact([
                ['label' => 'couverture(s) retirée(s) — les infrastructures concernées sont conservées', 'nombre' => $nbLiaisons],
                ['label' => 'actualité(s) liée(s)', 'nombre' => $nbActus],
                ['label' => 'document(s) lié(s)', 'nombre' => $nbDocs],
            ]),
            false,
            'Les infrastructures qui couvraient cette localité resteront, mais elles ne la compteront plus dans leur population couverte.',
        ];
    }

    /**
     * Secteur : la base REFUSE la suppression si des infrastructures
     * l'utilisent (restrictOnDelete) -> on bloque le bouton Confirmer.
     * Sinon seuls les indicateurs du secteur sont détruits (cascade).
     */
    private function impactSecteur(Secteur $secteur): array
    {
        $infras  = Infrastructure::where('secteur_id', $secteur->id)->get();
        $nbIndic = Indicateur::where('secteur_id', $secteur->id)->count();

        // Cas bloqué : des infrastructures dépendent encore de ce secteur
        if ($infras->isNotEmpty()) {
            return [
                "Supprimer le secteur « {$secteur->nom} » ?",
                [$this->ligneImpact('infrastructure(s) utilisant ce secteur', $infras)],
                true,
                "Suppression impossible : déplacez ou supprimez d'abord ces infrastructures.",
            ];
        }

        return [
            "Supprimer définitivement le secteur « {$secteur->nom} » ?",
            $this->filtreImpact([
                ['label' => 'indicateur(s) du secteur', 'nombre' => $nbIndic],
            ]),
            false,
            null,
        ];
    }

    /** Infrastructure : indicateurs, photos, documents, actualités et liaisons de couverture. */
    private function impactInfrastructure(Infrastructure $infrastructure): array
    {
        return [
            "Supprimer définitivement l'infrastructure « {$infrastructure->nom} » ?",
            $this->filtreImpact([
                // Nombre d'indicateurs mesurés par cette infrastructure (via la pivot)
                ['label' => 'indicateur(s) associé(s)', 'nombre' => DB::table('indicateur_infrastructure')->where('infrastructure_id', $infrastructure->id)->count()],
                ['label' => 'photo(s) associée(s)', 'nombre' => Photo::where('infrastructure_id', $infrastructure->id)->count()],
                ['label' => 'document(s) lié(s)', 'nombre' => Document::where('infrastructure_id', $infrastructure->id)->count()],
                ['label' => 'actualité(s) liée(s)', 'nombre' => Actualite::where('infrastructure_id', $infrastructure->id)->count()],
                ['label' => 'localité(s) qui perdront sa couverture', 'nombre' => DB::table('localite_couverts')->where('infrastructure_id', $infrastructure->id)->count()],
            ]),
            false,
            null,
        ];
    }

    /**
     * Construit une ligne d'impact standard : libellé, nombre d'éléments
     * et quelques noms d'exemple pour rendre l'écran concret.
     */
    private function ligneImpact(string $label, $collection): array
    {
        return [
            'label'    => $label,
            'nombre'   => $collection->count(),
            'exemples' => $collection->count() > 0
                ? $collection->sortBy('nom')->take(5)->pluck('nom')->implode(', ')
                  . ($collection->count() > 5 ? ', …' : '')
                : '',
        ];
    }

    /** Ne garde que les lignes d'impact dont le nombre est > 0 (re-indexation). */
    private function filtreImpact(array $lignes): array
    {
        return array_values(array_filter($lignes, fn ($ligne) => $ligne['nombre'] > 0));
    }

    // ══════════════════════════════════════════════════════════
    //  HELPERS PRIVÉS — réutilisés par création ET modification
    // ══════════════════════════════════════════════════════════

    /**
     * Règles de validation communes aux entités territoriales
     * (région, département, commune, localité) : statistiques
     * démographiques + coordonnées géographiques.
     */
    private function reglesStats(): array
    {
        return [
            'superficie'         => 'nullable|numeric|min:0',
            'taille_population'  => [
                'nullable', 'integer', 'min:0',
                // RÈGLE MÉTIER : la somme hommes + femmes ne doit pas dépasser
                // la population totale. Vérifiée uniquement si les 3 champs
                // sont renseignés (sinon on ne peut pas comparer).
                function ($attribute, $value, $fail) {
                    $hommes = (int) request('nbre_homme');
                    $femmes = (int) request('nbre_femme');
                    // On ne contrôle que si hommes ET femmes sont aussi renseignés
                    if (request()->filled('nbre_homme') && request()->filled('nbre_femme') && $value !== null) {
                        if ($hommes + $femmes > (int) $value) {
                            $fail("La somme des hommes et des femmes ({$hommes} + {$femmes} = " . ($hommes + $femmes) . ") ne peut pas dépasser la population totale ({$value}).");
                        }
                    }
                },
            ],
            'nbre_menage'        => 'nullable|integer|min:0',
            'nbre_homme'         => 'nullable|integer|min:0',
            'nbre_femme'         => 'nullable|integer|min:0',
            'latitude'           => 'nullable|numeric',
            'longitude'          => 'nullable|numeric',
        ];
    }

    /**
     * Règles de validation d'une infrastructure (création ET modification).
     *
     * @param Request $request   Requête en cours (pour prohibitedIf)
     * @param int|null $idIgnore Id à ignorer pour l'unicité du nom (cas modification)
     */
    private function reglesInfrastructure(Request $request, ?int $idIgnore = null): array
    {
        return [
            'nom'                  => [
                'required', 'string', 'max:255',
                // Nom unique au sein du MÊME territoire (commune OU département) ;
                // deux infrastructures homonymes dans des territoires différents restent autorisées.
                Rule::unique('infrastructures')
                    ->where(fn ($q) => $q
                        ->when($request->filled('commune_id'), fn ($qq) => $qq->where('commune_id', $request->commune_id))
                        ->when($request->filled('departement_id'), fn ($qq) => $qq->where('departement_id', $request->departement_id)))
                    ->ignore($idIgnore),
            ],
            'description'          => 'nullable|string',
            'type_infrastructure'  => 'nullable|string|max:255',

            // ═══ Rattachement territorial : UN SEUL mode, jamais deux. ═══
            // Règle (cascade avec arrêt) : on s'arrête au niveau le plus profond.
            //   - Département : departement_id seul (ni commune, ni localités)
            //   - Commune     : commune_id seule (pas de département, localités optionnelles)
            //   - Localités   : commune implicite + tableau localites[] (jamais de département)
            // Grâce aux prohibitions réciproques, on ne peut plus remplir deux
            // champs de rattachement à la fois → plus d'erreur "prohibited".
            'departement_id' => [
                'nullable',
                'exists:departements,id',
                Rule::prohibitedIf(fn () => $request->filled('commune_id') || !empty($request->input('localites'))),
            ],
            'commune_id' => [
                'nullable',
                'exists:communes,id',
                Rule::prohibitedIf(fn () => $request->filled('departement_id')),
            ],

            'secteur_id'           => 'required|exists:secteurs,id',
            'date_creation'        => 'nullable|date',
            // État du lieu : valeurs de l'énumération définie dans la table
            'etat_lieu'            => ['nullable', Rule::in(['Bon', 'Moyen', 'Mauvais', 'Hors_service'])],
            'latitude'             => 'nullable|numeric',
            'longitude'            => 'nullable|numeric',

            // Localités couvertes : tableau d'IDs (multi-select), sans doublons.
            // Interdit si rattachement département -> seul le mode commune+localités l'utilise.
            'localites'            => [
                'nullable',
                'array',
                Rule::prohibitedIf(fn () => $request->filled('departement_id')),
            ],
            'localites.*'          => 'integer|distinct|exists:localites,id',

            // Population couverte personnalisée (optionnelle).
            // Si renseignée, elle remplace la somme automatique et est répartie
            // proportionnellement entre les localités sélectionnées.
            'population_couverte'  => 'nullable|integer|min:0',

            // Valeurs des indicateurs de ce secteur : tableau "indicateur_id => valeur".
            // Les valeurs manquantes restent null dans la pivot.
            'indicateurs_valeurs'      => 'nullable|array',
            'indicateurs_valeurs.*'    => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Vérifie le rattachement territorial d'une infrastructure.
     *
     * Retourne null si le rattachement est valide, sinon un tableau d'erreurs
     * (à passer à withErrors). Complète la validation : ici on garantit qu'au
     * moins un mode de rattachement (département, commune, ou localités) est
     * renseigné.
     *
     * @param array $validated Données validées de la requête
     * @return array|null
     */
    private function verifierRattachement(array $validated): ?array
    {
        $aDepartement = !empty($validated['departement_id']);
        $aCommune     = !empty($validated['commune_id']);
        $aLocalites   = !empty($validated['localites']);

        // Aucun choix de rattachement -> erreur
        if (! $aDepartement && ! $aCommune && ! $aLocalites) {
            return [
                'territoire' => "L'infrastructure doit être rattachée à un département, une commune ou des localités.",
            ];
        }

        return null;
    }

    /**
     * Enregistre les localités couvertes dans la pivot "localite_couverts".
     *
     * Par défaut, chaque localité reçoit sa propre population (taille_population).
     * Si $populationCouverte est fournie, cette valeur cible est répartie
     * proportionnellement entre les localités sélectionnées.
     *
     * @param Infrastructure $infrastructure
     * @param array          $localiteIds        IDs des localités couvertes
     * @param int|null       $populationCouverte Population totale souhaitée (optionnel)
     */
    private function synchroniserLocalitesCouvertes(Infrastructure $infrastructure, array $localiteIds, ?int $populationCouverte): void
    {
        // Aucune localité cochée → on vide la couverture (sync([]) retire tout)
        if (empty($localiteIds)) {
            $infrastructure->localitesCouvertes()->sync([]);
            return;
        }

        $syncData = [];
        $localites = Localite::whereIn('id', $localiteIds)->get();

        foreach ($localites as $localite) {
            $syncData[$localite->id] = [
                'nbre_population_couvert' => $localite->taille_population,
            ];
        }

        // Répartition proportionnelle si une population personnalisée est saisie
        if ($populationCouverte !== null && $populationCouverte > 0) {
            $totalReel = array_sum(array_column($syncData, 'nbre_population_couvert'));

            if ($totalReel > 0) {
                foreach ($syncData as &$ligne) {
                    $ligne['nbre_population_couvert'] =
                        (int) round($ligne['nbre_population_couvert'] * $populationCouverte / $totalReel);
                }
                unset($ligne); // bonne pratique après boucle par référence
            }
        }

        $infrastructure->localitesCouvertes()->sync($syncData);
    }

    /**
     * Enregistre les valeurs des indicateurs d'une infrastructure dans la pivot
     * `indicateur_infrastructure`.
     *
     * On n'accepte que les indicateurs appartenant réellement au secteur de
     * l'infrastructure (sécurité : le client pourrait truquer l'id). Les
     * indicateurs sans valeur saisie sont quand même liés (valeur null) afin de
     * rester cohérents avec la liste des indicateurs du secteur ; seule une
     * valeur strictement vide ('' ou null) est stockée en tant que null.
     *
     * @param Infrastructure $infrastructure
     * @param array          $valeurs        Tableau "indicateur_id => valeur"
     */
    private function synchroniserValeursIndicateurs(Infrastructure $infrastructure, array $valeurs): void
    {
        // Indicateurs autorisés : ceux du secteur de l'infrastructure
        $indicateursSecteur = Indicateur::where('secteur_id', $infrastructure->secteur_id)->pluck('id');

        $syncData = [];
        foreach ($indicateursSecteur as $indicateurId) {
            // Valeur passée (si elle existe pour cet indicateur)
            $raw = $valeurs[$indicateurId] ?? $valeurs[(string) $indicateurId] ?? null;

            // '' (champ resté vide) -> valeur null dans la pivot
            $syncData[$indicateurId] = [
                'valeur' => ($raw === '' || $raw === null) ? null : (float) $raw,
            ];
        }

        // sync() : remplace les valeurs précédentes ; les indicateurs du secteur
        // non présents dans $syncData seraient retirés (comportement voulu quand
        // le secteur change, mais ici on garde toujours les indicateurs du secteur).
        $infrastructure->indicateurs()->sync($syncData);
    }
}

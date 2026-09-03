<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Commentaire;
use App\Models\Departement;
use App\Models\Infrastructure;
use App\Models\Localite;
use App\Models\Commune;
use App\Models\Photo;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActualiteController extends Controller
{
    // ══════════════════════════════════════════════════════════
    //  LISTE — page Admin /ActualitesAdmi
    // ══════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $regionId      = $request->integer('region_id');
        $departementId = $request->integer('departement_id');
        $communeId     = $request->integer('commune_id');
        $localiteId    = $request->integer('localite_id');
        $infrastructureId = $request->integer('infrastructure_id');

        $query = Actualite::with(['photos', 'commentaires', 'infrastructure.commune', 'infrastructure.departement'])
            ->withCount('photos')
            ->withCount(['commentaires as commentaires_total'])
            ->withCount(['commentaires as commentaires_en_attente' => function ($q) {
                $q->where('statut', 'en_attente');
            }]);

        // ── Filtres cascendants (rattachement direct OU via infrastructure) ──
        // Une actu peut être rattachée directement (commune_id / departement_id / region_id)
        // OU indirectement via son infrastructure (infrastructure.commune / infrastructure.departement).
        if ($localiteId) {
            $query->where(function ($q) use ($localiteId) {
                $q->where('localite_id', $localiteId)
                  ->orWhereHas('infrastructure.localitesCouvertes', fn ($sq) => $sq->where('localites.id', $localiteId));
            });
        } elseif ($communeId) {
            $query->where(function ($q) use ($communeId) {
                $q->where('commune_id', $communeId)
                  ->orWhereHas('infrastructure', fn ($sq) => $sq->where('commune_id', $communeId));
            });
        } elseif ($departementId) {
            $query->where(function ($q) use ($departementId) {
                $q->where('departement_id', $departementId)
                  ->orWhereHas('infrastructure', function ($sq) use ($departementId) {
                      $sq->where('departement_id', $departementId)
                        ->orWhereHas('commune', fn ($c) => $c->where('departement_id', $departementId));
                  });
            });
        } elseif ($regionId) {
            $query->where(function ($q) use ($regionId) {
                $q->where('region_id', $regionId)
                  ->orWhereHas('infrastructure.departement.region', fn ($r) => $r->where('regions.id', $regionId))
                  ->orWhereHas('infrastructure.commune.departement.region', fn ($r) => $r->where('regions.id', $regionId));
            });
        }

        // ── Filtre Infrastructure (indépendant du rattachement territorial) ──
        if ($infrastructureId) {
            $query->where('infrastructure_id', $infrastructureId);
        }

        $actualites = $query->orderByDesc('date_publication')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Données pour les cascades JS (sidebar filtres)
        $allRegions      = Region::orderBy('nom')->get();
        $allDepartements = Departement::orderBy('nom')->get();
        $allCommunes     = Commune::orderBy('nom')->get();
        $allLocalites    = Localite::orderBy('nom')->get();
        $allInfrastructures = Infrastructure::orderBy('nom')->get();

        // ── Infrastructures disponibles selon le territoire choisi (cascade) ──
        // Une infrastructure est rattachée à UNE commune OU UN département :
        //  • commune  → infra de cette commune
        //  • départ.  → infra du département ou d'une de ses communes
        //  • région   → infra des départements / communes de la région
        //  • sinon    → toutes
        if ($communeId) {
            $filteredInfrastructures = $allInfrastructures->where('commune_id', $communeId);
        } elseif ($departementId) {
            $communesDept = $allCommunes->where('departement_id', $departementId)->pluck('id')->all();
            $filteredInfrastructures = $allInfrastructures->filter(fn ($i) =>
                $i->departement_id == $departementId
                || in_array($i->commune_id, $communesDept)
            )->values();
        } elseif ($regionId) {
            $deptIds    = $allDepartements->where('region_id', $regionId)->pluck('id')->all();
            $communeIds = $allCommunes->whereIn('departement_id', $deptIds)->pluck('id')->all();
            $filteredInfrastructures = $allInfrastructures->filter(fn ($i) =>
                in_array($i->departement_id, $deptIds)
                || in_array($i->commune_id, $communeIds)
            )->values();
        } else {
            $filteredInfrastructures = $allInfrastructures;
        }

        // Nombre total de commentaires en attente (pour le badge sidebar)
        $totalEnAttente = Commentaire::where('statut', 'en_attente')->count();

        $connecte = Auth::user();

        return view('PageAdmi.Actualite', compact(
            'actualites',
            'regionId', 'departementId', 'communeId', 'localiteId', 'infrastructureId',
            'allRegions', 'allDepartements', 'allCommunes', 'allLocalites',
            'allInfrastructures', 'filteredInfrastructures', 'totalEnAttente',
            'connecte',
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  CRÉATION — POST /Actualites/Actualite
    // ══════════════════════════════════════════════════════════

    public function store(Request $request)
    {
        $validated = $this->valider($request);

        // ── Rattachement territorial : niveau le plus profond rempli ──
        $this->aplatirTerritoire($validated);

        DB::transaction(function () use ($validated, $request) {
            $actualite = Actualite::create([
                'titre'             => $validated['titre'],
                'contenu'           => $validated['contenu'],
                'date_publication'  => $validated['date_publication'] ?? null,
                'region_id'         => $validated['region_id'],
                'departement_id'    => $validated['departement_id'],
                'commune_id'        => $validated['commune_id'],
                'localite_id'       => $validated['localite_id'],
                'infrastructure_id' => $validated['infrastructure_id'] ?? null,
            ]);

            $this->enregistrerPhotos($actualite, $request->file('photos', []));
        });

        return redirect()->route('ActualitesAdmi')
            ->with('success', 'Actualité créée avec succès !');
    }

    // ══════════════════════════════════════════════════════════
    //  MODIFICATION — PUT /Actualites/Actualite/{actualite}
    // ══════════════════════════════════════════════════════════

    public function update(Request $request, Actualite $actualite)
    {
        $validated = $this->valider($request);

        $this->aplatirTerritoire($validated);

        // Photos : conserve (photos cochées pour suppression retirées) + ajoute
        // les nouvelles ; le total final ne doit pas dépasser 10.
        $aEnlever   = $request->input('photos_supprimer', []);
        $gagees     = $actualite->photos()->whereNotIn('id', $aEnlever)->count();
        $aAjouter   = $request->file('photos', []);
        $ajoutees   = count(array_filter($aAjouter, fn ($f) => $f instanceof \Illuminate\Http\UploadedFile));

        if ($gagees + $ajoutees > 10) {
            return back()
                ->withInput()
                ->withErrors(['photos' => 'Le total de photos ne peut pas dépasser 10.']);
        }

        DB::transaction(function () use ($actualite, $validated, $aEnlever, $aAjouter) {
            $actualite->update([
                'titre'             => $validated['titre'],
                'contenu'           => $validated['contenu'],
                'date_publication'  => $validated['date_publication'] ?? null,
                'region_id'         => $validated['region_id'],
                'departement_id'    => $validated['departement_id'],
                'commune_id'        => $validated['commune_id'],
                'localite_id'       => $validated['localite_id'],
                'infrastructure_id' => $validated['infrastructure_id'] ?? null,
            ]);

            // Suppression des photos cochées (fichier + ligne)
            if (! empty($aEnlever)) {
                $aSupprimer = $actualite->photos()->whereIn('id', $aEnlever)->get();
                foreach ($aSupprimer as $photo) {
                    Storage::disk('photos')->delete($photo->chemin_photo);
                    $photo->delete();
                }
            }

            // Ajout des nouvelles photos
            $this->enregistrerPhotos($actualite, $aAjouter);
        });

        return redirect()->route('ActualitesAdmi')
            ->with('success', 'Actualité modifiée avec succès !');
    }

    // ══════════════════════════════════════════════════════════
    //  SUPPRESSION — DELETE /Actualites/Actualite/{actualite}
    // ══════════════════════════════════════════════════════════

    public function destroy(Actualite $actualite)
    {
        DB::transaction(function () use ($actualite) {
            // Fichiers photos
            foreach ($actualite->photos as $photo) {
                Storage::disk('photos')->delete($photo->chemin_photo);
            }
            $actualite->photos()->delete();
            $actualite->commentaires()->delete();
            $actualite->delete();
        });

        return redirect()->route('ActualitesAdmi')
            ->with('success', 'Actualité supprimée avec succès !');
    }

    // ══════════════════════════════════════════════════════════
    //  IMPACT — GET /Actualites/Impact/{actualite} (JSON)
    // ══════════════════════════════════════════════════════════

    public function impact(Actualite $actualite)
    {
        $commentaires = $actualite->commentaires()->get();
        $photos       = $actualite->photos()->get();

        $lignes = [];
        if ($actualite->photos()->count() > 0) {
            $lignes[] = [
                'label'    => 'photo(s) effacée(s) du stockage',
                'nombre'   => $photos->count(),
                'exemples' => $photos->take(5)->pluck('nom')->implode(', ') . ($photos->count() > 5 ? ', …' : ''),
            ];
        }
        if ($commentaires->count() > 0) {
            $lignes[] = [
                'label'    => 'commentaire(s)',
                'nombre'   => $commentaires->count(),
                'exemples' => $commentaires->take(5)->pluck('nom')->implode(', ') . ($commentaires->count() > 5 ? ', …' : ''),
            ];
        }

        return response()->json([
            'intro'   => "Supprimer définitivement l'actualité « {$actualite->titre} » ?",
            'lignes'  => $lignes,
            'blocage' => false,
            'note'    => 'Le fichier physique des photos sera définitivement effacé du disque.',
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  VALIDATION + APLATISSEMENT TERRITORIAL
    // ══════════════════════════════════════════════════════════

    private function valider(Request $request): array
    {
        return $request->validate([
            'titre'             => 'required|string|max:255',
            'contenu'           => 'required|string',
            'date_publication'  => 'nullable|date',
            'region_id'         => 'nullable|exists:regions,id',
            'departement_id'    => 'nullable|exists:departements,id',
            'commune_id'        => 'nullable|exists:communes,id',
            'localite_id'       => 'nullable|exists:localites,id',
            'infrastructure_id' => 'nullable|exists:infrastructures,id',
            'photos'            => 'nullable|array|max:10',
            'photos.*'          => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ]);
    }

    /**
     * Rattachement territorial : on conserve le niveau le plus profond
     * rempli (localité > commune > département > région), les autres sont
     * remis à null.
     */
    private function aplatirTerritoire(array &$v): void
    {
        $v['region_id']      = $v['region_id']      ?? null;
        $v['departement_id'] = $v['departement_id'] ?? null;
        $v['commune_id']     = $v['commune_id']     ?? null;
        $v['localite_id']    = $v['localite_id']    ?? null;

        if (! empty($v['localite_id'])) {
            $v['commune_id'] = $v['departement_id'] = $v['region_id'] = null;
        } elseif (! empty($v['commune_id'])) {
            $v['departement_id'] = $v['region_id'] = null;
        } elseif (! empty($v['departement_id'])) {
            $v['region_id'] = null;
        }
    }

    // ══════════════════════════════════════════════════════════
    //  PHOTOS — enregistrement sur disque + ligne en base
    // ══════════════════════════════════════════════════════════

    /**
     * Stocke chaque fichier sur le disque "photos" et crée
     * la ligne Photo rattachée à l'actualité.
     */
    private function enregistrerPhotos(Actualite $actualite, array $fichiers): void
    {
        foreach ($fichiers as $fichier) {
            if (! $fichier instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }
            $chemin = $fichier->store(date('Y'), 'photos');

            Photo::create([
                'actualite_id'  => $actualite->id,
                'nom'           => $fichier->getClientOriginalName(),
                'chemin_photo'  => $chemin,
                'description'   => null,
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════
    //  COMMENTAIRES — marquer lue / supprimer
    // ══════════════════════════════════════════════════════════

    public function marquerLue(Commentaire $commentaire)
    {
        $commentaire->update(['statut' => 'lue']);

        return back()->with('success', 'Commentaire marqué comme lu.');
    }

    public function supprimerCommentaire(Commentaire $commentaire)
    {
        $commentaire->delete();

        return back()->with('success', 'Commentaire supprimé.');
    }
}

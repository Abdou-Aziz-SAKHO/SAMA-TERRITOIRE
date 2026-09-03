<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

/**
 * Gestion des documents (fichiers Excel, Word, PDF) importés dans l'application.
 *
 * Règles :
 * - formats autorisés : pdf, doc, docx, xls, xlsx, csv (10 Mo max) ;
 * - rattachement territorial en "arrêt au niveau" : le niveau le plus profond
 *   rempli (localité > commune > département > région) est conservé, les autres
 *   sont annulés (un document ne peut être rattaché qu'à UN niveau) ;
 * - rattachement à une infrastructure : facultatif et indépendant du territoire ;
 * - stockage : disque dédié "documents" (config/filesystems.php), privé en local
 *   (storage/app/private/documents) et basculable vers S3/MinIO/R2 en production
 *   via la seule variable DOCUMENT_DISK. Les fichiers ne sont servis que via les
 *   routes preview/download (aucune exposition directe), méthodes driver-agnostiques.
 */
class DocumentController extends Controller
{
    /** Formats acceptés (extensions) et taille maximale (Ko). */
    protected const EXTENSIONS_AUTORISEES = 'pdf,doc,docx,xls,xlsx,csv';
    protected const TAILLE_MAX_KO = 10240; // 10 Mo

    /** Nom du disque dédié aux documents (voir config/filesystems.php). */
    protected const DISQUE = 'documents';

    /**
     * Enregistre un nouveau document : valide le fichier et les métadonnées,
     * stocke le fichier, puis crée l'enregistrement avec le rattachement
     * territorial "arrêt au niveau".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'      => 'required|string|max:255',
            'type_document' => ['required', Rule::in(['rapport', 'fiche', 'document', 'PDC'])],
            'description'   => 'nullable|string|max:1000',
            'fichier'       => ['required', 'file', 'max:' . self::TAILLE_MAX_KO, 'mimes:' . self::EXTENSIONS_AUTORISEES],
            'region_id'         => 'nullable|exists:regions,id',
            'departement_id'    => 'nullable|exists:departements,id',
            'commune_id'        => 'nullable|exists:communes,id',
            'localite_id'       => 'nullable|exists:localites,id',
            'infrastructure_id' => 'nullable|exists:infrastructures,id',
        ]);

        // ── Rattachement territorial : niveau le plus profond rempli ──
        // Si une localité est choisie, la commune/dept/région saisis en amont
        // de la cascade sont ignorés ; idem pour la commune face au département...
        $regionId      = $validated['region_id']      ?? null;
        $departementId = $validated['departement_id'] ?? null;
        $communeId     = $validated['commune_id']     ?? null;
        $localiteId    = $validated['localite_id']    ?? null;

        if ($localiteId) {
            $communeId = $departementId = $regionId = null;
        } elseif ($communeId) {
            $departementId = $regionId = null;
        } elseif ($departementId) {
            $regionId = null;
        }

        // Stockage : année/fichier_hash.ext, chemin relatif AU DISQUE documents
        // (ex. "2026/abcd.pdf" → disque local : storage/app/private/documents/2026/…)
        $chemin = $request->file('fichier')->store(date('Y'), self::DISQUE);

        Document::create([
            'titre'            => $validated['titre'],
            'type_document'    => $validated['type_document'],
            'description'      => $validated['description'] ?? null,
            'region_id'        => $regionId,
            'departement_id'   => $departementId,
            'commune_id'       => $communeId,
            'localite_id'      => $localiteId,
            'infrastructure_id' => $validated['infrastructure_id'] ?? null,
            'nom_fichier'      => $request->file('fichier')->getClientOriginalName(),
            'chemin_document'  => $chemin,
            'extension'        => strtolower($request->file('fichier')->getClientOriginalExtension()),
            'mime_type'        => $request->file('fichier')->getClientMimeType(),
            'taille'           => $request->file('fichier')->getSize(),
        ]);

        return redirect()->route('DonneesAdmi', ['tab' => 'documents'])
            ->with('success', 'Document importé avec succès !');
    }

    /**
     * Aperçu inline (lecture directe dans l'app) — utilisé par l'iframe
     * de la modal de consultation. Ne force pas le téléchargement.
     * Driver-agnostique : fonctionne en local comme en S3/MinIO/R2.
     */
    public function preview(Document $document)
    {
        $chemin = $this->resoudreChemin($document->chemin_document);
        $disque = Storage::disk(self::DISQUE);
        abort_unless($disque->exists($chemin), 404, 'Fichier introuvable.');

        return $disque->response($chemin, $document->nom_fichier, [
            'Content-Type' => $document->mime_type ?? 'application/octet-stream',
        ]);
    }

    /**
     * Téléchargement du fichier avec son nom d'origine.
     */
    public function download(Document $document)
    {
        $chemin = $this->resoudreChemin($document->chemin_document);
        $disque = Storage::disk(self::DISQUE);
        abort_unless($disque->exists($chemin), 404, 'Fichier introuvable.');

        return $disque->download($chemin, $document->nom_fichier);
    }

    /**
     * Supprime le fichier sur le disque puis l'enregistrement.
     */
    public function destroy(Document $document)
    {
        $disque = Storage::disk(self::DISQUE);
        // Nettoyage du fichier physique avant suppression de la ligne
        $disque->delete($this->resoudreChemin($document->chemin_document));

        $document->delete();

        return redirect()->route('DonneesAdmi', ['tab' => 'documents'])
            ->with('success', 'Document supprimé avec succès !');
    }

    /**
     * Normalise un chemin stocké pour le disque "documents".
     *
     * Avant le disque dédié, les chemins étaient préfixés de "documents/"
     * (ex. "documents/2026/abcd.pdf"). Depuis, le chemin est relatif au disque
     * (ex. "2026/abcd.pdf"). Ce correctif tolère les deux formats.
     */
    private function resoudreChemin(string $chemin): string
    {
        return preg_replace('#^documents/#', '', $chemin);
    }
}
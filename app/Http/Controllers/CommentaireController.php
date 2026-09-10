<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    /**
     * Page admin : liste des commentaires indépendants (pas liés à une actualité).
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        $statut = $request->get('statut');
        $mois = $request->get('mois');

        $commentaires = Commentaire::independant()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($statut, fn($q) => $q->where('statut', $statut))
            ->when($mois, function ($q) use ($mois) {
                // Filtre par mois : format attendu "YYYY-MM"
                if (preg_match('/^\d{4}-\d{2}$/', $mois)) {
                    [$annee, $moisNum] = explode('-', $mois);
                    $q->whereYear('created_at', (int) $annee)
                      ->whereMonth('created_at', (int) $moisNum);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Badge : nombre de commentaires en attente (pour la navbar)
        $totalEnAttente = Commentaire::independant()
            ->where('statut', 'en_attente')->count();

        return view('PageAdmi.CommentairesAdmi', compact('commentaires', 'type', 'statut', 'mois', 'totalEnAttente'));
    }

    /**
     * Marquer un commentaire comme lu.
     */
    public function marquerLu(Commentaire $commentaire)
    {
        $commentaire->update(['statut' => 'lu']);
        return back()->with('succes', 'Commentaire marqué comme lu.');
    }

    /**
     * Marquer un commentaire comme traité.
     */
    public function marquerTraite(Commentaire $commentaire)
    {
        $commentaire->update(['statut' => 'traite']);
        return back()->with('succes', 'Commentaire marqué comme traité.');
    }

    /**
     * Supprimer un commentaire.
     */
    public function destroy(Commentaire $commentaire)
    {
        $commentaire->delete();
        return back()->with('succes', 'Commentaire supprimé.');
    }

    /**
     * Exporter les commentaires/plaintes sélectionnés en PDF
     * pour transmission à la mairie ou aux personnes concernées.
     */
    public function exportPdf(Request $request)
    {
        $ids = $request->input('ids', []);
        $destinataire = trim($request->input('destinataire', ''));

        // Récupère les commentaires sélectionnés (ordre chronologique)
        $commentaires = Commentaire::independant()
            ->whereIn('id', $ids)
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupère les actualités liées aux commentaires si présentes
        $commentaires->loadMissing('actualite');

        // Aucun commentaire sélectionné → retour à la page avec message
        if ($commentaires->isEmpty()) {
            return back()->with('erreur', 'Sélectionnez au moins un commentaire à exporter.');
        }

        // Compteurs par type pour le récapitulatif
        $recap = [
            'commentaire' => $commentaires->where('type', 'commentaire')->count(),
            'suggestion'  => $commentaires->where('type', 'suggestion')->count(),
            'plainte'     => $commentaires->where('type', 'plainte')->count(),
        ];

        // Logo en-tête PDF : fichier déposé dans public/assets/img/
        // (le nom est configurable dans config/sama.php — ici en data URI pour
        //  que dompdf l'affiche sans dépendre de l'extension GD)
        $logoNom = config('sama.logo_pdf', 'ENTETE-PDF.jpg');
        $logoChemin = public_path('assets/img/' . $logoNom);
        $logoPdf = null;
        if (is_file($logoChemin)) {
            $mime = match (strtolower(pathinfo($logoNom, PATHINFO_EXTENSION))) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png'         => 'image/png',
                'gif'         => 'image/gif',
                'webp'        => 'image/webp',
                default       => 'image/jpeg',
            };
            $logoPdf = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoChemin));
        }

        $pdf = Pdf::loadView('pdf.commentaires', [
            'commentaires' => $commentaires,
            'destinataire' => $destinataire,
            'date'         => now(),
            'recap'        => $recap,
            'logoPdf'      => $logoPdf,
            'logoNom'      => $logoNom,
        ])->setPaper('a4', 'portrait');

        $nomFichier = 'SAMA_TERRITOIRE_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($nomFichier);
    }
}

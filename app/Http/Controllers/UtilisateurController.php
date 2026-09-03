<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UtilisateurController extends Controller
{
    // ══════════════════════════════════════════════════════════
    //  LISTE — page Admin /UtilisateursAdmi
    // ══════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        // Connecté (peut être null si routé sans middleware -> lecture seule)
        /** @var \App\Models\User|null $connecte */
        $connecte = Auth::user();

        // ── Onglet 1 : Comptes (admins + super admins) avec recherche + filtres ──
        $recherche   = trim((string) $request->input('q'));
        $roleFiltre  = $request->input('role');
        $statutFiltre = $request->input('statut');

        $query = User::query();

        if ($recherche !== '') {
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', "%{$recherche}%")
                  ->orWhere('prenom', 'like', "%{$recherche}%")
                  ->orWhere('email', 'like', "%{$recherche}%");
            });
        }

        if (in_array($roleFiltre, ['ADMINISTRATEUR', 'SUPER_ADMINISTRATEUR'], true)) {
            $query->where('role', $roleFiltre);
        }

        if (in_array($statutFiltre, ['actif', 'bloque'], true)) {
            $query->where('statut', $statutFiltre === 'actif' ? 1 : 0);
        }

        $utilisateurs = $query->orderBy('role')->orderBy('prenom')->get();

        // ── Onglet 2 : Commentateurs (commentaires anonymes agrégés par email) ──
        $commentateurs = Commentaire::select('nom', 'email')
            ->selectRaw('COUNT(*) as total_commentaires')
            ->selectRaw('MAX(created_at) as derniere_date')
            ->groupBy('email', 'nom')
            ->orderByDesc('derniere_date')
            ->get();

        return view('PageAdmi.Utilisateur', compact(
            'connecte',
            'utilisateurs', 'recherche', 'roleFiltre', 'statutFiltre',
            'commentateurs',
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  MODIFICATION — PUT /Utilisateurs/{user}
    // ══════════════════════════════════════════════════════════

    public function update(Request $request, User $user)
    {
        /** @var \App\Models\User|null $connecte */
        $connecte = Auth::user();

        // Droit : super admin modifie tout ; admin ne modifie que les admins (pas les super admins).
        if (!$connecte || (!$connecte->isSuperAdmin() && $user->isSuperAdmin())) {
            return back()->withErrors(['error' => 'Vous n\'avez pas le droit de modifier ce compte.']);
        }

        $data = $request->validate([
            'prenom'    => 'required|string|max:255',
            'nom'       => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'required|digits:9|unique:users,telephone,' . $user->id,
            'cni'       => 'nullable|max:13',
            'password'  => 'nullable|min:6',
        ]);

        // Le rôle n'est JAMAIS modifiable ici (gardé tel quel).
        $user->prenom    = $data['prenom'];
        $user->nom       = $data['nom'];
        $user->email     = $data['email'];
        $user->telephone = $data['telephone'];
        $user->cni       = $data['cni'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Utilisateur modifié avec succès.');
    }

    // ══════════════════════════════════════════════════════════
    //  BLOQUER / DÉBLOQUER — PUT /Utilisateurs/{user}/statut
    // ══════════════════════════════════════════════════════════

    public function toggleStatut(Request $request, User $user)
    {
        /** @var \App\Models\User|null $connecte */
        $connecte = Auth::user();

        // Seul un Super Admin connecté peut bloquer/débloquer.
        if (!$connecte || !$connecte->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Seul un Super Administrateur peut bloquer ou débloquer un compte.']);
        }

        // Garde-fou : impossible de se bloquer soi-même.
        if ($connecte->id === $user->id) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas bloquer votre propre compte.']);
        }

        // On bloque (statut -> 0) OU on débloque (statut -> 1), selon l'état actuel.
        $vaBloquer = $user->isActif();

        // Garde-fou : impossible de bloquer le dernier super admin actif.
        if ($vaBloquer && $user->isSuperAdmin()) {
            $nbSuperAdminsActifs = User::where('role', 'SUPER_ADMINISTRATEUR')
                ->where('statut', 1)
                ->where('id', '!=', $user->id)
                ->count();

            if ($nbSuperAdminsActifs === 0) {
                return back()->withErrors(['error' => 'Impossible de bloquer le dernier Super Administrateur actif.']);
            }
        }

        $user->statut = $vaBloquer ? 0 : 1;
        $user->save();

        return back()->with('success', $vaBloquer
            ? "Le compte de {$user->nomComplet()} a été bloqué."
            : "Le compte de {$user->nomComplet()} a été débloqué.");
    }
}

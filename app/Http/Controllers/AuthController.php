<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login()
    {
        return view('Authentification.AuthAdmi');
    }

    public function register()
    {
        return view('PageAdmi.RegisterAdmi');
    }

    public function registerAdmi(Request $request)
    {
        $request->validate([
            'prenom' => 'required|min:3',
            'nom' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'telephone' => 'required|digits:9|unique:users',
            'cni' => 'nullable|min:13|max:13',
            'password' => 'required|min:6|confirmed',
            // Le rôle est validé : seules les deux valeurs de l'enum sont acceptées
            'role' => 'required|in:ADMINISTRATEUR,SUPER_ADMINISTRATEUR',
        ]);

        // SÉCURITÉ : Seul un Super Administrateur connecté a le droit de créer un Super Administrateur.
        // Si un simple Admin (ou un visiteur non connecté) tente d'envoyer 'SUPER_ADMINISTRATEUR',
        // le rôle est automatiquement rétrogradé en 'ADMINISTRATEUR'.
        $role = $request->role;

        // On récupère l'utilisateur connecté et on vérifie son type avec instanceof :
        // cela permet à l'éditeur (Intelephense/VS Code) de connaître la vraie classe
        // et de ne plus signaler "Undefined method isSuperAdmin" (faux positif).
        /** @var \App\Models\User|null $connecte */
        $connecte = Auth::user();

        if ($role === 'SUPER_ADMINISTRATEUR' && (!$connecte instanceof User || !$connecte->isSuperAdmin())) {
            $role = 'ADMINISTRATEUR';
        }

        //  Creer un utilisateur Admin
        $user = User::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'cni' => $request->cni,
            'password' => Hash::make($request->password),
            'role' => $role,
            'statut' => 1
        ]);

        $user->save();

        // Authentification
        $request->session()->regenerate();

        return redirect('Dashboard');
    }

    public function auth(Request $request)
    {
        $auth = $request->validate([
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if (Auth::attempt($auth)) {
            $request->session()->regenerate();
            return redirect()->route('Dashboard');
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');

        // return back()->with('error','email ou mot de passe invalide');

    }

    public function showForgotPassword()
    {
        return view('Authentification.ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        //   indique que seul email est obligatoire
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Un lien de réinitialisation a été envoyé à votre email.')
            : back()->withErrors(['email' => 'Impossible d\'envoyer le lien. Vérifiez votre adresse email.']);
    }
}

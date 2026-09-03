<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Ce test utilise la VRAIE base MySQL (pas sqlite in-memory).
// On force la config DB dans setUp() pour pointer vers MySQL réel.
beforeEach(function () {
    config([
        'database.default'              => 'mysql',
        'database.connections.mysql.host'     => '127.0.0.1',
        'database.connections.mysql.port'     => '3306',
        'database.connections.mysql.database' => 'sama_territoire',
        'database.connections.mysql.username' => 'root',
        'database.connections.mysql.password' => '',
    ]);
    DB::purge('mysql');
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

function makeUser(array $attrs): User
{
    return User::create(array_merge([
        'prenom' => 'Test',
        'nom' => 'User',
        'email' => uniqid() . '@test.sn',
        'telephone' => '771234567',
        'password' => Hash::make('password'),
        'role' => 'ADMINISTRATEUR',
        'statut' => 1,
    ], $attrs));
}

it('un admin simple ne peut PAS bloquer/debloquer', function () {
    $admin    = makeUser(['role' => 'ADMINISTRATEUR']);
    $super    = makeUser(['role' => 'SUPER_ADMINISTRATEUR']);
    $cible    = makeUser(['role' => 'ADMINISTRATEUR']);

    $this->actingAs($admin)
        ->put('/Utilisateurs/' . $cible->id . '/statut');

    $cible->refresh();
    expect($cible->statut)->toBe(1); // inchangé (bloquage refusé)
});

it('un admin ne peut PAS modifier un super admin', function () {
    $admin = makeUser(['role' => 'ADMINISTRATEUR']);
    $super = makeUser(['role' => 'SUPER_ADMINISTRATEUR', 'nom' => 'Original']);

    $this->actingAs($admin)
        ->put('/Utilisateurs/' . $super->id, [
            'prenom' => 'Hacker', 'nom' => 'Hacker', 'email' => $super->email,
            'telephone' => $super->telephone, 'cni' => $super->cni,
        ]);

    $super->refresh();
    expect($super->nom)->toBe('Original'); // non modifié
});

it('un admin PEUT modifier un autre admin', function () {
    $admin = makeUser(['role' => 'ADMINISTRATEUR', 'telephone' => '771000001']);
    $autre = makeUser(['role' => 'ADMINISTRATEUR', 'nom' => 'Original', 'telephone' => '771000002']);

    $this->actingAs($admin)->put('/Utilisateurs/' . $autre->id, [
        'prenom' => 'Nouveau', 'nom' => 'Nom', 'email' => $autre->email,
        'telephone' => $autre->telephone, 'cni' => $autre->cni,
    ]);

    $autre->refresh();
    expect($autre->prenom)->toBe('Nouveau');
});

it('un super admin PEUT bloquer un admin', function () {
    $super = makeUser(['role' => 'SUPER_ADMINISTRATEUR']);
    $cible = makeUser(['role' => 'ADMINISTRATEUR', 'statut' => 1]);

    $this->actingAs($super)->put('/Utilisateurs/' . $cible->id . '/statut');

    $cible->refresh();
    expect($cible->statut)->toBe(0);
});

it('un super admin PEUT bloquer un autre super admin', function () {
    $super  = makeUser(['role' => 'SUPER_ADMINISTRATEUR']);
    $cible  = makeUser(['role' => 'SUPER_ADMINISTRATEUR', 'statut' => 1]);

    $this->actingAs($super)->put('/Utilisateurs/' . $cible->id . '/statut');

    $cible->refresh();
    expect($cible->statut)->toBe(0);
});

it('un super admin NE PEUT PAS se bloquer lui-meme', function () {
    $super = makeUser(['role' => 'SUPER_ADMINISTRATEUR', 'statut' => 1]);

    $this->actingAs($super)->put('/Utilisateurs/' . $super->id . '/statut');

    $super->refresh();
    expect($super->statut)->toBe(1); // inchangé
});

it('un super admin NE PEUT PAS bloquer le dernier super admin actif', function () {
    // Seul super admin actif restant = le connecté
    User::where('role', 'SUPER_ADMINISTRATEUR')->update(['statut' => 0]);
    $super = User::where('role', 'SUPER_ADMINISTRATEUR')->first();
    if (!$super) {
        $super = makeUser(['role' => 'SUPER_ADMINISTRATEUR', 'statut' => 1]);
    }
    DB::table('users')->where('role', 'SUPER_ADMINISTRATEUR')->where('id', '!=', $super->id)->update(['statut' => 0]);

    // un autre super admin (connecté) essaie de bloquer le seul restant actif
    $connecte = makeUser(['role' => 'SUPER_ADMINISTRATEUR', 'statut' => 1]);
    $this->actingAs($connecte)->put('/Utilisateurs/' . $super->id . '/statut');

    $super->refresh();
    expect($super->statut)->toBe(1); // non bloqué (garde-fou)
});

it('la page affiche les boutique bloquer pour un super admin connecté', function () {
    $super = makeUser(['role' => 'SUPER_ADMINISTRATEUR']);
    $this->actingAs($super)->get('/UtilisateursAdmi')
        ->assertSee('Gestion des utilisateurs');
});

it('la page est accessible en lecture seule sans connexion', function () {
    $this->get('/UtilisateursAdmi')->assertSee('Gestion des utilisateurs');
});
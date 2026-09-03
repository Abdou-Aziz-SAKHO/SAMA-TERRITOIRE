<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(new \Illuminate\Http\Request());
$users = \App\Models\User::all();
echo "=== USERS ===\n";
foreach ($users as $u) {
    printf("id=%d | %s %s | %s | role=%s | statut=%s\n", $u->id, $u->prenom, $u->nom, $u->email, $u->role, $u->statut ? 'actif' : 'bloque');
}
echo "=== COMMENTAIRES ===\n";
foreach (\App\Models\Commentaire::all() as $c) {
    printf("nom=%s | email=%s | msg=%s | statut=%s\n", $c->nom, $c->email, $c->message, $c->statut);
}

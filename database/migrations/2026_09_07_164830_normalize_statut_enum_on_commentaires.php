<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Ajouter les nouveaux statuts avant de convertir (MySQL valide l'enum en ALTER)
        DB::statement("ALTER TABLE commentaires MODIFY statut ENUM('en_attente','lue','lu','traite') NOT NULL DEFAULT 'en_attente'");
        // 2) Convertir les anciens statuts
        DB::statement("UPDATE commentaires SET statut = 'lu' WHERE statut = 'lue'");
        // 3) Retirer les valeurs obsolètes
        DB::statement("ALTER TABLE commentaires MODIFY statut ENUM('en_attente','lu','traite') NOT NULL DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE commentaires MODIFY statut ENUM('en_attente','lue') NOT NULL DEFAULT 'en_attente'");
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * NOUVELLE RÈGLE MÉTIER :
     * Une infrastructure appartient à UN SEUL niveau territorial :
     *   - soit 1 département          (departement_id rempli, commune_id vide)
     *   - soit 1 commune              (commune_id remplie, departement_id vide)
     *   - soit 1 ou PLUSIEURS localités (commune_id remplie + lignes dans la pivot
     *                                   "localite_couverts", l'ancienne colonne
     *                                   unique localite_id disparaît)
     *
     * Cette migration corrige aussi :
     *  - le bug bloquant : l'ancienne contrainte CHECK exigeait exactement UNE colonne
     *    alors que le contrôleur exigeait les TROIS (erreur SQL 3819 à chaque création) ;
     *  - le nom de la table pivot : créée sous "localite__couverts" (double underscore)
     *    alors que les modèles utilisaient "localite_couverts".
     */
    public function up(): void
    {
        // ── 1. Suppression de l'ANCIENNE contrainte CHECK "exactement UN territoire" ──
        // Syntaxe compatible MySQL 8+ (DROP CONSTRAINT) avec repli MariaDB (DROP CHECK)
        try {
            DB::statement('ALTER TABLE infrastructures DROP CONSTRAINT chk_infrastructure_territoire');
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE infrastructures DROP CHECK chk_infrastructure_territoire');
        }

        // ── 2. Suppression de la colonne localite_id ──
        // Une infrastructure ne possède plus UNE localité unique :
        // elle peut en COUVRIR plusieurs via la table pivot.
        Schema::table('infrastructures', function (Blueprint $table) {
            if (Schema::hasColumn('infrastructures', 'localite_id')) {
                $table->dropForeign(['localite_id']);
                $table->dropIndex(['localite_id']);
                $table->dropColumn('localite_id');
            }
        });

        // ── 3. Nouvelle contrainte CHECK adaptée à la règle métier ──
        // Le territoire d'implantation = département OU commune (jamais les deux,
        // jamais aucun). Les localités couvertes sont gérées par la pivot :
        // elles exigent une commune (vérifié côté application).
        DB::statement("
            ALTER TABLE infrastructures
            ADD CONSTRAINT chk_infrastructure_territoire
            CHECK (
                (departement_id IS NOT NULL) +
                (commune_id IS NOT NULL) = 1
            )
        ");

        // ── 4. Harmonisation du nom de la table pivot ──
        if (Schema::hasTable('localite__couverts') && ! Schema::hasTable('localite_couverts')) {
            Schema::rename('localite__couverts', 'localite_couverts');
        }
    }

    /**
     * Restaure l'ancienne structure (localite_id unique + ancienne contrainte CHECK).
     */
    public function down(): void
    {
        // Suppression de la nouvelle contrainte
        try {
            DB::statement('ALTER TABLE infrastructures DROP CONSTRAINT chk_infrastructure_territoire');
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE infrastructures DROP CHECK chk_infrastructure_territoire');
        }

        // Ré-ajout de l'ancienne colonne localite_id
        Schema::table('infrastructures', function (Blueprint $table) {
            $table->foreignId('localite_id')
                ->nullable()
                ->constrained('localites')
                ->cascadeOnDelete();
            $table->index('localite_id');
        });

        // Restauration de l'ancienne contrainte CHECK (exactement UN territoire parmi 3)
        DB::statement("
            ALTER TABLE infrastructures
            ADD CONSTRAINT chk_infrastructure_territoire
            CHECK (
                (departement_id IS NOT NULL) +
                (commune_id IS NOT NULL) +
                (localite_id IS NOT NULL) = 1
            )
        ");

        // Restauration de l'ancien nom de la table pivot
        if (Schema::hasTable('localite_couverts') && ! Schema::hasTable('localite__couverts')) {
            Schema::rename('localite_couverts', 'localite__couverts');
        }
    }
};

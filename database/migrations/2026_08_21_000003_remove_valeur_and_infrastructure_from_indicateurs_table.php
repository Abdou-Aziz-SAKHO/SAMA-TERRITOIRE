<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nettoie la table `indicateurs` après le passage à la table pivot.
 *
 * Les colonnes `valeur` et `infrastructure_id` sont déplacées vers la table
 * d'association `indicateur_infrastructure` :
 *   - `valeur`           → pivot (la valeur dépend de l'infrastructure)
 *   - `infrastructure_id`→ plus nécessaire, remplacé par la relation many-to-many
 *
 * L'indicateur garde son `secteur_id` (il reste rattaché à son secteur).
 */
return new class extends Migration
{
    /**
     * Retire les colonnes devenues obsolètes de la table indicateurs.
     */
    public function up(): void
    {
        Schema::table('indicateurs', function (Blueprint $table) {
            // Ordre important : il faut supprimer la clé étrangère AVANT la colonne.
            $table->dropForeign(['infrastructure_id']);
            $table->dropColumn(['valeur', 'infrastructure_id']);
        });
    }

    /**
     * Ré-ajoute les colonnes en cas de rollback (perte de données ==> les valeurs
     * saisies dans la pivot ne sont pas rétablies, mais la structure redevient
     * compatible avec l'état précédent).
     */
    public function down(): void
    {
        Schema::table('indicateurs', function (Blueprint $table) {
            $table->decimal('valeur', 15, 2)->nullable();

            $table->foreignId('infrastructure_id')
                ->nullable()
                ->constrained('infrastructures')
                ->cascadeOnDelete();
        });
    }
};

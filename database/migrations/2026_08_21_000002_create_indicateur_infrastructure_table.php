<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table d'association (pivot) entre un indicateur et une infrastructure.
 *
 * ❓ POURQUOI CETTE TABLE ?
 * La valeur d'un indicateur dépend de l'infrastructure que cet indicateur mesure.
 * Exemple : le secteur "Santé" a l'indicateur "Nombre de lits (unité)". Chaque
 * infrastructure du secteur Santé aura SA propre valeur (hôpital A : 120 lits,
 * hôpital B : 80 lits...).
 *
 * On ne peut donc pas stocker la valeur directement sur l'indicateur (elle varierait),
 * ni sur l'infrastructure (plusieurs indicateurs). On la stocke ici, dans la table
 * pivot qui fait le lien entre les deux.
 */
return new class extends Migration
{
    /**
     * Crée la table pivot indicateur_infrastructure.
     */
    public function up(): void
    {
        Schema::create('indicateur_infrastructure', function (Blueprint $table) {
            $table->id();

            // Côté indicateur : impossible de créer une ligne sans indicateur
            $table->foreignId('indicateur_id')
                ->constrained('indicateurs')
                ->cascadeOnDelete();

            // Côté infrastructure : impossible de créer une ligne sans infrastructure
            $table->foreignId('infrastructure_id')
                ->constrained('infrastructures')
                ->cascadeOnDelete();

            // Valeur que prend l'indicateur pour CETTE infrastructure précise.
            // Nullable : un indicateur peut être défini mais sa valeur saisie plus tard.
            $table->decimal('valeur', 15, 2)->nullable();

            $table->timestamps();

            // Un même couple (indicateur, infrastructure) ne peut apparaître qu'une fois
            $table->unique(['indicateur_id', 'infrastructure_id']);
        });
    }

    /**
     * Supprime la table pivot.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicateur_infrastructure');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('actualites', function (Blueprint $table) {
            $table->id();



            // Champs principaux du contenu
            $table->string('titre');
            $table->longText('contenu');

            // Date de publication optionnelle
            $table->dateTime('date_publication')->nullable();

            // Relations avec les territoires et l'infrastructure
            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->cascadeOnDelete();

            $table->foreignId('departement_id')
                ->nullable()
                ->constrained('departements')
                ->cascadeOnDelete();

            $table->foreignId('commune_id')
                ->nullable()
                ->constrained('communes')
                ->cascadeOnDelete();

            $table->foreignId('localite_id')
                ->nullable()
                ->constrained('localites')
                ->cascadeOnDelete();

            $table->foreignId('infrastructure_id')
                ->nullable()
                ->constrained('infrastructures')
                ->cascadeOnDelete();

            // Horodatages Laravel
            $table->timestamps();

            // Contraintes d'unicité et index pour optimiser les recherches
            $table->unique(['titre', 'date_publication']);
            $table->index('region_id');
            $table->index('departement_id');
            $table->index('commune_id');
            $table->index('localite_id');
            $table->index('infrastructure_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actualites');
    }
};

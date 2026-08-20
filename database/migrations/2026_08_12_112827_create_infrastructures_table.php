<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->id();


            $table->string('nom');

            $table->text('description')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->date('date_creation')->nullable();

            $table->enum('etat_lieu', [
                'Bon',
                'Moyen',
                'Mauvais',
                'Hors_service',
            ])->default('Bon')->nullable();
            $table->string('type_infrastructure')->nullable();

            //  permet d'ajouter des cles etranger au table au table infrastructure

            $table->foreignId('secteur_id')
                ->constrained('secteurs')
                ->restrictOnDelete();

            $table->foreignId('departement_id')
                ->nullable()
                ->constrained('departements')
                ->restrictOnDelete()
                ->cascadeOnDelete();


            $table->foreignId('commune_id')
                ->nullable()
                ->constrained('communes')
                ->restrictOnDelete()
                ->cascadeOnDelete();


            $table->foreignId('localite_id')
                ->nullable()
                ->constrained('localites')
                ->restrictOnDelete()
                ->cascadeOnDelete();


            $table->timestamps();
            // Ajouter les index de recherche pour les colonnes de cles etrangeres
            $table->index('secteur_id');
            $table->index('departement_id');
            $table->index('commune_id');
            $table->index('localite_id');
        });


        /*
         * Une infrastructure doit appartenir à exactement
         * UN territoire : département OU commune OU localité.
         */
        DB::statement('
            ALTER TABLE infrastructures
            ADD CONSTRAINT chk_infrastructure_territoire
            CHECK (
                (departement_id IS NOT NULL) +
                (commune_id IS NOT NULL) +
                (localite_id IS NOT NULL) = 1
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructures');
    }
};

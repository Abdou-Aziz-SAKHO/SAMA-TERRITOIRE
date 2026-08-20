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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->enum('type_document', ['rapport', 'fiche', 'document','PDC',])->nullable();
            $table->string('chemin_document');

            $table->foreignId('departement_id')
                ->nullable()
                ->constrained('departements')
                ->cascadeOnDelete();

            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->cascadeOnDelete();

            $table->foreignId('localite_id')
                ->nullable()
                ->constrained('localites')
                ->cascadeOnDelete();


            $table->foreignId('commune_id')
                ->nullable()
                ->constrained('communes')
                ->cascadeOnDelete();

            $table->foreignId('infrastructure_id')
                ->nullable()
                ->constrained('infrastructures')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('departement_id');
            $table->index('commune_id');
            $table->index('infrastructure_id');
            $table->index('localite_id');
            $table->index('region_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

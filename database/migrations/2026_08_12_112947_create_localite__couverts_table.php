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
        Schema::create('localite__couverts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('localite_id')
                ->constrained('localites')
                ->cascadeOnDelete();

            $table->foreignId('infrastructure_id')
                ->constrained('infrastructures')
                ->cascadeOnDelete();

            $table->integer('nbre_population_couvert')->nullable();

            $table->timestamps();

            $table->unique([
                'localite_id',
                'infrastructure_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localite__couverts');
    }
};

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
        Schema::create('indicateurs', function (Blueprint $table) {
            $table->id();

            $table->string('nom_indicateur');
            $table->string('unites')->nullable();
            $table->text('description')->nullable();

            $table->decimal('valeur', 15, 2)->nullable();

            $table->foreignId('secteur_id')
                ->constrained('secteurs')
                ->cascadeOnDelete();

            $table->foreignId('infrastructure_id')
                ->nullable()
                ->constrained('infrastructures')
                ->cascadeOnDelete();


            $table->timestamps();

            $table->unique(['secteur_id', 'nom_indicateur']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicateurs');
    }
};

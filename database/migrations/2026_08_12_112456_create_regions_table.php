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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();

            $table->double('superficie')->nullable();
            $table->double('taille_population')->nullable();
            $table->integer('nbre_menage')->nullable();

            $table->integer('nbre_homme')->nullable();
            $table->integer('nbre_femme')->nullable();
            $table->integer('nbre_infrastructure')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};

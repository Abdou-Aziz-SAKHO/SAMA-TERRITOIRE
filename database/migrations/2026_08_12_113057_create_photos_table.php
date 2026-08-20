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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('chemin_photo');
            $table->string('description')->nullable();

            $table->foreignId('infrastructure_id')
                ->nullable()
                ->constrained('infrastructures')
                ->cascadeOnDelete();

            $table->foreignId('actualite_id')
                ->nullable()
                ->constrained('actualites')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('infrastructure_id');
            $table->index('actualite_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};

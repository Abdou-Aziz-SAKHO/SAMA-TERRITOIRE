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
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('email');

            $table->foreignId('actualite_id')
                ->nullable()
                ->constrained('actualites')
                ->cascadeOnDelete();

            $table->text('message');

            $table->enum('statut', ['en_attente', 'lue'])->default('en_attente');

            $table->timestamps();

            $table->index('actualite_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};

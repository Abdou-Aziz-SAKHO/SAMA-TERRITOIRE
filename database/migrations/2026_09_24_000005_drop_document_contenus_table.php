<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('document_contenus');
    }

    public function down(): void
    {
        Schema::create('document_contenus', function ($table) {
            $table->id();
            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();
            $table->longText('contenu');
            $table->timestamps();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute des métadonnées de fichier à la table documents :
 * nom d'origine, extension, type MIME, taille et description.
 * Ces colonnes complètent les métadonnées existantes (titre, type_document,
 * chemin_document) sans rien casser.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('nom_fichier')->nullable()->after('chemin_document');
            $table->string('extension', 10)->nullable()->after('nom_fichier');
            $table->string('mime_type', 100)->nullable()->after('extension');
            $table->unsignedBigInteger('taille')->nullable()->after('mime_type');
            $table->text('description')->nullable()->after('taille');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['nom_fichier', 'extension', 'mime_type', 'taille', 'description']);
        });
    }
};
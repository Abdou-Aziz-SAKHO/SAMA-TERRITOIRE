<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convertir les feedback existants en commentaire (valeur valide restante)
        DB::table('commentaires')->where('type', 'feedback')->update(['type' => 'commentaire']);

        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropColumn('page');
            $table->enum('type', ['commentaire', 'suggestion', 'plainte'])
                ->default('commentaire')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->string('page', 50)->nullable()->after('type');
            $table->enum('type', ['commentaire', 'suggestion', 'plainte', 'feedback'])
                ->default('commentaire')
                ->change();
        });
    }
};
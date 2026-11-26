<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes optionnelles « commune » et « projet concerné »
     * au formulaire commentaire/plainte (peuvent rester nulles).
     */
    public function up(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->string('commune')->nullable()->after('email');
            $table->string('projet')->nullable()->after('commune');
        });
    }

    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropColumn(['commune', 'projet']);
        });
    }
};

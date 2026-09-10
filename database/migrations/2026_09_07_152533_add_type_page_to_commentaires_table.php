<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->string('type', 20)->default('commentaire')->after('email');
            $table->string('page', 50)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropColumn(['type', 'page']);
        });
    }
};

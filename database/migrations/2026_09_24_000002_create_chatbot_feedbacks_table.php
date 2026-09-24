<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('question', 500);
            $table->text('reponse')->nullable();
            $table->tinyInteger('score'); // 1 = utile, -1 = pas utile
            $table->string('page', 120)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['score', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_feedbacks');
    }
};
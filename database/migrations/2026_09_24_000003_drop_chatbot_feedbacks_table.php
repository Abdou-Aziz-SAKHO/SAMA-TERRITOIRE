<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chatbot_feedbacks');
    }

    public function down(): void
    {
        Schema::create('chatbot_feedbacks', function ($table) {
            $table->id();
            $table->string('question', 500);
            $table->text('reponse')->nullable();
            $table->tinyInteger('score');
            $table->string('page', 120)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });
    }
};
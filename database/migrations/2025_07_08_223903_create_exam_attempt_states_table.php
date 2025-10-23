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
       Schema::create('exam_attempt_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->integer('current_question_index')->default(0);
            $table->integer('remaining_time')->nullable(); // Soniyalarda
            $table->json('user_answers')->nullable(); // JSON massivi
            $table->json('question_statuses')->nullable(); // JSON obyekti
            $table->timestamps();

            $table->unique(['user_id', 'quiz_id']); // Har bir foydalanuvchi uchun bitta test holati
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_states');
    }
};

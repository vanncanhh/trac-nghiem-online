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
        Schema::create('exam_questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('order')->default(0);
            $t->unsignedInteger('points')->default(1);
            $t->unique(['exam_id','question_id']);
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};

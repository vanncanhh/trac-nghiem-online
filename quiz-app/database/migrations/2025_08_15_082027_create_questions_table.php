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
        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $t->text('content');                   // nội dung câu hỏi
            $t->enum('difficulty',['easy','med','hard'])->default('med')->index();
            $t->unsignedInteger('points')->default(1);
            $t->softDeletes();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

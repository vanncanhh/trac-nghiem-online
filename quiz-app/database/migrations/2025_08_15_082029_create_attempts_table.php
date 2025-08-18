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
        Schema::create('attempts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->dateTime('started_at');
            $t->dateTime('submitted_at')->nullable();
            $t->unsignedInteger('score')->default(0);
            $t->unsignedInteger('max_score')->default(0);
            $t->timestamps();
            $t->unique(['exam_id','user_id','started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};

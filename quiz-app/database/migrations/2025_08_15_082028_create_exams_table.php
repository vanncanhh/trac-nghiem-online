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
        Schema::create('exams', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $t->unsignedInteger('duration_minutes')->default(30);
            $t->boolean('is_public')->default(false);
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

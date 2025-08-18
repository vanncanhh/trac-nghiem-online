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
        Schema::table('attempt_answers', function (Blueprint $t) {
            $t->foreignId('attempt_id')->after('id')->constrained('attempts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attempt_answers', function (Blueprint $t) {
            $t->dropConstrainedForeignId('attempt_id');
        });
    }
};

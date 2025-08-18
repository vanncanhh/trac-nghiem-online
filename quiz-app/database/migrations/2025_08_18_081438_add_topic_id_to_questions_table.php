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
        if (!Schema::hasColumn('questions', 'topic_id')) {
            Schema::table('questions', function (Blueprint $t) {
                $t->foreignId('topic_id')
                  ->nullable()
                  ->after('subject_id')
                  ->constrained('topics')
                  ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('questions', 'topic_id')) {
            Schema::table('questions', function (Blueprint $t) {
                $t->dropConstrainedForeignId('topic_id');
            });
        }
    }
};

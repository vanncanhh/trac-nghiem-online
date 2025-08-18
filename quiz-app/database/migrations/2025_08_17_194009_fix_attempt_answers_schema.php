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
            if (!Schema::hasColumn('attempt_answers', 'attempt_id')) {
                $t->foreignId('attempt_id')->after('id')->constrained('attempts')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('attempt_answers', 'question_id')) {
                $t->foreignId('question_id')->after('attempt_id')->constrained('questions')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('attempt_answers', 'selected_option_id')) {
                $t->foreignId('selected_option_id')->nullable()->after('question_id')
                  ->constrained('options')->nullOnDelete();
            }
            if (!Schema::hasColumn('attempt_answers', 'is_correct')) {
                $t->boolean('is_correct')->default(false)->after('selected_option_id')->index();
            }
            if (!Schema::hasColumn('attempt_answers', 'awarded_points')) {
                $t->unsignedInteger('awarded_points')->default(0)->after('is_correct');
            }
            // tạo unique nếu chưa có
            // (nếu đã tồn tại thì MySQL sẽ báo lỗi – khi đó bạn có thể bỏ dòng dưới)
            try { $t->unique(['attempt_id','question_id']); } catch (\Throwable $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attempt_answers', function (Blueprint $table) {
            //
        });
    }
};

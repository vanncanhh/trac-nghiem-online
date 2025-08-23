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
        if (!Schema::hasColumn('exams', 'classroom_id')) {
            Schema::table('exams', function (Blueprint $t) {
                $t->foreignId('classroom_id')->nullable()->after('subject_id')
                  ->constrained('classrooms')->nullOnDelete();
            });
        } else {
            // (tuỳ chọn) nếu muốn đảm bảo có FK, có thể thêm thử:
            try {
                Schema::table('exams', function (Blueprint $t) {
                    // Nếu đã có FK rồi thì câu lệnh dưới sẽ bị lỗi -> try/catch bỏ qua
                    $t->foreign('classroom_id')->references('id')->on('classrooms')->nullOnDelete();
                });
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('exams', 'classroom_id')) {
            Schema::table('exams', function (Blueprint $t) {
                // Laravel 9+
                try { $t->dropConstrainedForeignId('classroom_id'); } catch (\Throwable $e) {
                    // fallback nếu không có FK tên chuẩn
                    try { $t->dropForeign(['classroom_id']); } catch (\Throwable $e2) {}
                    try { $t->dropColumn('classroom_id'); } catch (\Throwable $e3) {}
                }
            });
        }
    }
};

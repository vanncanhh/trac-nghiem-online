<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;

class ExamGeneratorService
{
    public function generate(Exam $exam, array $filters): void
    {
        // filters: subject_id, total, mix (['easy'=>50,'med'=>30,'hard'=>20]) (theo %)
        $total = $filters['total'] ?? 20;
        $mix   = $filters['mix']   ?? ['easy'=>40,'med'=>40,'hard'=>20];
        $subjectId = $filters['subject_id'] ?? null;

        // xóa câu hỏi cũ trước khi sinh mới
        $exam->questions()->detach();

        $add = function (string $diff, int $count) use ($exam, $subjectId) {
            if ($count <= 0) return;

            $qs = Question::when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
                ->where('difficulty', $diff)
                ->inRandomOrder()->limit($count)->get();

            foreach ($qs as $i => $q) {
                $exam->questions()->attach($q->id, [
                    'order'  => $exam->questions()->count() + $i + 1,
                    'points' => $q->points,
                ]);
            }
        };

        foreach ($mix as $diff => $pct) {
            $add($diff, (int) round($total * ($pct / 100)));
        }
    }
}

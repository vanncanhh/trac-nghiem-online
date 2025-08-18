<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Attempt, AttemptAnswer, Exam, Question, Option};  
use Carbon\Carbon;

class AttemptController extends Controller
{
    // danh sách kết quả của tôi
    public function index()
    {
        $attempts = Attempt::with('exam')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')->get();

        return view('attempts.index', compact('attempts'));
    }

    // bắt đầu thi
    public function start(Exam $exam, Request $r)
    {
        abort_unless($exam->is_public, 403);

        // 1. Kiểm tra đề có câu hỏi chưa
        $examQuestionIds = $exam->questions()->pluck('questions.id')->toArray();
        if (count($examQuestionIds) === 0) {
            return redirect()->route('exams.catalog')
                ->withErrors('Đề này chưa có câu hỏi. Vui lòng liên hệ giáo viên.');
        }

        $forceNew = $r->boolean('new');

        // 2. Tìm attempt đang làm dở
        $attempt = Attempt::where('exam_id',$exam->id)
            ->where('user_id',auth()->id())
            ->whereNull('submitted_at')
            ->latest()->first();

        // 3. Nếu yêu cầu tạo mới → xoá attempt cũ (nếu có)
        if ($forceNew && $attempt) {
            $attempt->answers()->delete();
            $attempt->delete();
            $attempt = null;
        }

        // 4. Tạo attempt mới nếu chưa có
        if (!$attempt) {
            $attempt = Attempt::create([
                'exam_id'     => $exam->id,
                'user_id'     => auth()->id(),
                'started_at'  => now(),
                'max_score'   => $exam->questions()->sum('exam_questions.points'),
            ]);

            // Tạo answers theo thứ tự ngẫu nhiên
            $qids = collect($examQuestionIds)->shuffle()->values();
            foreach ($qids as $qid) {
                $attempt->answers()->create([
                    'question_id'        => $qid,
                    'selected_option_id' => null,
                    'is_correct'         => false,
                    'awarded_points'     => 0,
                ]);
            }
        } else {
            // 5. Đảm bảo có answers (trường hợp lỗi cũ)
            if ($attempt->answers()->count() === 0) {
                $qids = collect($examQuestionIds)->shuffle()->values();
                foreach ($qids as $qid) {
                    $attempt->answers()->create([
                        'question_id'        => $qid,
                        'selected_option_id' => null,
                        'is_correct'         => false,
                        'awarded_points'     => 0,
                    ]);
                }
            }
        }

        // 6. Lấy answers kèm câu hỏi + phương án để render
        $answers = $attempt->answers()->with(['question.subject','question.options'])->get();

        // 7. Tính mốc hết giờ bằng server time để gửi cho JS
        $endAt = Carbon::parse($attempt->started_at)->addMinutes($exam->duration_minutes);

        return view('attempts.take', [
            'exam'       => $exam,
            'attempt'    => $attempt,
            'answers'    => $answers,
            'endAt'      => $endAt->timestamp,      // epoch (giây)
            'serverNow'  => now()->timestamp,       // epoch (giây)
        ]);
    }

    // nộp bài
    public function submit(Request $r, Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        abort_if($attempt->submitted_at, 403);

        $exam = $attempt->exam()->first();
        $deadline = $attempt->started_at->copy()->addMinutes($exam->duration_minutes);
        if (now()->greaterThan($deadline)) {
            // quá giờ: chấm những gì đã chọn
        }

        $payload = $r->input('answers',[]); // [question_id => option_id]

        DB::transaction(function () use ($attempt, $payload) {
            $score = 0;

            foreach ($attempt->answers as $ans) {
                $qid = $ans->question_id;
                $oid = $payload[$qid] ?? null;

                $isCorrect = false; $points = $this->pointsFor($attempt->exam_id, $qid);
                if ($oid) {
                    $isCorrect = Option::where('id',$oid)->where('question_id',$qid)->value('is_correct') ?? false;
                }

                $ans->update([
                    'selected_option_id'=>$oid,
                    'is_correct'=>$isCorrect,
                    'awarded_points'=>$isCorrect ? $points : 0
                ]);

                $score += $isCorrect ? $points : 0;
            }

            $attempt->update(['score'=>$score,'submitted_at'=>now()]);
        });

        return redirect()->route('attempts.show',$attempt)->with('ok','Đã nộp bài');
    }

    public function show(Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        $attempt->load(['exam','answers.question.options']);
        return view('attempts.show', compact('attempt'));
    }

    private function authorizeAttempt(Attempt $a){
        abort_if($a->user_id !== auth()->id(), 403);
    }

    private function pointsFor($examId,$questionId): int {
        return (int) DB::table('exam_questions')
            ->where(['exam_id'=>$examId,'question_id'=>$questionId])
            ->value('points') ?? 1;
    }
}

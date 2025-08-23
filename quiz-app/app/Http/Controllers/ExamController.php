<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Exam, Question, Subject, Classroom};   


class ExamController extends Controller
{
  public function index(Request $r)
  {
    $q = Exam::with(['subject','classroom'])
        ->when(auth()->user()->role==='teacher', fn($x)=>$x->where('created_by',auth()->id()))
        ->when($r->filled('classroom_id'), fn($x)=>$x->where('classroom_id',$r->classroom_id))
        ->when($r->filled('subject_id'),   fn($x)=>$x->where('subject_id',$r->subject_id))
        ->latest();

    return view('exams.index', [
        'exams'      => $q->paginate(12)->withQueryString(),
        'subjects'   => Subject::orderBy('name')->get(),
        'classrooms' => Classroom::orderBy('name')->get(),
        'filters'    => $r->only('classroom_id','subject_id'),
    ]);
  }

  public function create()
  {
    return view('exams.create', [
            'subjects'   => Subject::orderBy('name')->get(),
            'classrooms' => Classroom::orderBy('name')->get(),
        ]);
  }

  public function store(Request $r)
  {
    $data = $r->validate([
        'title'            => 'required|string|max:255',
        'classroom_id'     => 'required|exists:classrooms,id',
        'subject_id'       => 'nullable|exists:subjects,id',
        'duration_minutes' => 'required|integer|min:5',
        'is_public'        => 'sometimes|boolean',
    ]);
    $data['created_by'] = auth()->id();
    $data['is_public']  = $r->boolean('is_public');

    $exam = Exam::create($data);

    return redirect()->route('exams.edit',$exam)->with('ok','Đã tạo đề, hãy thêm câu hỏi');
  }

  public function edit(Exam $exam, Request $r)
  {
    $this->ownerOrAdmin($exam->created_by);

    $selectedIds = $exam->questions()->pluck('questions.id')->toArray();

    $questions = Question::with('subject')
        ->when(auth()->user()->role === 'teacher', fn($x) => $x->where('created_by', auth()->id()))
        ->when($r->filled('subject_id'),   fn($x) => $x->where('subject_id', $r->subject_id))
        ->when($r->filled('difficulty'),   fn($x) => $x->where('difficulty', $r->difficulty))
        ->orderByDesc('id')
        ->paginate(12)
        ->withQueryString();

    return view('exams.edit', [
        'exam'        => $exam->load('subject','classroom','questions.options'),
        'questions'   => $questions,
        'selectedIds' => $selectedIds,
        'subjects'    => Subject::orderBy('name')->get(),
        'classrooms'  => Classroom::orderBy('name')->get(),
        'filters'     => $r->only('subject_id','difficulty'),
    ]);
  }

  // lưu thuộc tính + danh sách câu hỏi đã chọn
  public function update(Request $r, Exam $exam)
  {
    $this->ownerOrAdmin($exam->created_by);

    $data = $r->validate([
        'title'            => 'required|string|max:255',
        'classroom_id'     => 'required|exists:classrooms,id',
        'subject_id'       => 'nullable|exists:subjects,id',
        'duration_minutes' => 'required|integer|min:5',
        'is_public'        => 'sometimes|boolean',
        'question_ids'     => 'nullable|array',
        'question_ids.*'   => 'integer|exists:questions,id',
    ]);

    $exam->update([
        'title'            => $data['title'],
        'classroom_id'     => $data['classroom_id'],
        'subject_id'       => $data['subject_id'] ?? null,
        'duration_minutes' => $data['duration_minutes'],
        'is_public'        => $r->boolean('is_public'),
    ]);

    $ids = collect($data['question_ids'] ?? [])->unique()->values()->all();
    $pivot = [];
    foreach ($ids as $i => $qid) {
        $pivot[$qid] = ['order' => $i + 1, 'points' => 1];
    }
    $exam->questions()->sync($pivot);

    return redirect()->route('exams.edit',$exam)->with('ok', 'Đã lưu đề thi');
  }

  public function destroy(Exam $exam)
  {
      $this->ownerOrAdmin($exam->created_by);
      $exam->questions()->detach();
      $exam->delete();
      return redirect()->route('exams.index',$exam)->with('ok','Đã xoá đề thi');
  }

  public function publish(Exam $exam)
  {
      $this->ownerOrAdmin($exam->created_by);
      $exam->update(['is_public' => true]);
      return redirect()->route('exams.edit',$exam)->with('ok','Đã public đề');
  }

  // tạo câu hỏi ngẫu nhiên theo tỉ lệ
  public function autoGenerate(Request $r, Exam $exam)
  {
    $this->ownerOrAdmin($exam->created_by);

    $data = $r->validate([
        'subject_id' => 'nullable|exists:subjects,id',
        'total'      => 'required|integer|min:1',
        'mix.easy'   => 'required|integer|min:0',
        'mix.med'    => 'required|integer|min:0',
        'mix.hard'   => 'required|integer|min:0',
    ]);

    $total = (int)$data['total'];
    $mix   = $data['mix'];

    $counts = [
        'easy' => (int)round($total * ($mix['easy'] / 100)),
        'med'  => (int)round($total * ($mix['med']  / 100)),
        'hard' => (int)round($total * ($mix['hard'] / 100)),
    ];
    $diff = $total - array_sum($counts);
    if ($diff !== 0) $counts['med'] += $diff;

    $exam->questions()->detach();

    foreach ($counts as $difficulty=>$cnt) {
        if ($cnt <= 0) continue;
        $qids = Question::when($data['subject_id'] ?? null, fn($x)=>$x->where('subject_id',$data['subject_id']))
            ->where('difficulty',$difficulty)
            ->inRandomOrder()->limit($cnt)->pluck('id');

        foreach ($qids as $qid) {
            $exam->questions()->attach($qid, [
                'order'  => $exam->questions()->count() + 1,
                'points' => 1
            ]);
        }
    }

    return back()->with('ok','Đã tạo đề tự động');
  }

  private function ownerOrAdmin($ownerId)
  {
      if (auth()->user()->role === 'admin') return;
      abort_unless($ownerId === auth()->id(), 403);
  }
  public function catalog(Request $r)
    {
        // danh sách đề public cho mọi role đăng nhập
        $classroomId = $r->input('classroom_id') ?? session('classroom_id');
        if ($r->filled('classroom_id')) {
            session(['classroom_id' => $r->classroom_id]);
        }

        $exams = Exam::with('subject','classroom')
            ->where('is_public', true)
            ->when($classroomId, fn($x)=>$x->where('classroom_id', $classroomId))
            ->when($r->filled('subject_id'), fn($x)=>$x->where('subject_id', $r->subject_id))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('exams.catalog', [
            'exams'      => $exams,
            'subjects'   => Subject::orderBy('name')->get(),
            'classrooms' => Classroom::orderBy('name')->get(),
            'filters'    => [
                'classroom_id' => $classroomId,
                'subject_id'   => $r->input('subject_id'),
            ],
        ]);
    }

}

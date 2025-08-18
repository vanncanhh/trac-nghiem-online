<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Exam, Question, Subject};   


class ExamController extends Controller
{
  public function index(Request $r)
  {
      $q = Exam::with('subject')
          ->when(auth()->user()->role==='teacher', fn($x)=>$x->where('created_by',auth()->id()))
          ->when($r->filled('subject_id'), fn($x)=>$x->where('subject_id',$r->subject_id))
          ->latest();

      return view('exams.index', [
          'exams' => $q->paginate(12)->withQueryString(),
          'subjects' => Subject::orderBy('name')->get(),
          'filters' => $r->only('subject_id')
      ]);
  }

  public function create()
  {
      return view('exams.create', ['subjects'=>Subject::orderBy('name')->get()]);
  }

  public function store(Request $r)
  {
      $data = $r->validate([
          'title' => 'required|string|max:255',
          'subject_id' => 'nullable|exists:subjects,id',
          'duration_minutes' => 'required|integer|min:5'
      ]);
      $data['created_by'] = auth()->id();

      $exam = Exam::create($data);
      return redirect()->route('exams.edit',$exam)->with('ok','Đã tạo đề, hãy thêm câu hỏi');
  }

  public function edit(Exam $exam, Request $r)
  {
    $this->ownerOrAdmin($exam->created_by);

    // Lấy danh sách ID câu hỏi đã chọn cho đề
    $selectedIds = $exam->questions()->pluck('questions.id')->toArray(); // hoặc ->pluck('id')->toArray();

    // Danh sách câu hỏi để chọn thêm
    $questions = Question::with('subject')
        ->when(auth()->user()->role === 'teacher', fn($x) => $x->where('created_by', auth()->id()))
        ->when($r->filled('subject_id'), fn($x) => $x->where('subject_id', $r->subject_id))
        ->when($r->filled('difficulty'), fn($x) => $x->where('difficulty', $r->difficulty))
        ->orderByDesc('id')
        ->paginate(12)
        ->withQueryString();

    return view('exams.edit', [
        'exam'       => $exam->load('subject', 'questions.options'),
        'questions'  => $questions,
        'selectedIds'=> $selectedIds,
        'subjects'   => Subject::orderBy('name')->get(),
        'filters'    => $r->only('subject_id','difficulty')
    ]);
  }

  // lưu thuộc tính + danh sách câu hỏi đã chọn
  public function update(Request $r, Exam $exam)
  {
    $this->ownerOrAdmin($exam->created_by);

    // 1) Validate và cập nhật thông tin đề
    $exam->update($r->validate([
        'title'            => 'required|string|max:255',
        'subject_id'       => 'nullable|exists:subjects,id',
        'duration_minutes' => 'required|integer|min:5',
    ]));

    // 2) Lưu danh sách câu hỏi (từ checkbox question_ids[])
    $ids = collect($r->input('question_ids', []))->unique()->values()->all();

    $pivot = [];
    foreach ($ids as $i => $qid) {
        $pivot[$qid] = ['order' => $i + 1, 'points' => 1];
    }
    $exam->questions()->sync($pivot);  // ghi đè thứ tự & điểm mặc định = 1

    return redirect()->route('exams.edit',$exam)->with('ok', 'Đã lưu đề thi');
  }

  public function destroy(Exam $exam)
  {
      $this->ownerOrAdmin($exam->created_by);
      $exam->questions()->detach();
      $exam->delete();
      return redirect()->route('exams.edit',$exam)->with('ok','Đã xoá đề thi');
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
          'total' => 'required|integer|min:1',
          'mix.easy' => 'required|integer|min:0',
          'mix.med'  => 'required|integer|min:0',
          'mix.hard' => 'required|integer|min:0',
      ]);

      $total = (int)$data['total'];
      $mix = $data['mix'];

      $counts = [
          'easy' => (int)round($total * ($mix['easy'] / 100)),
          'med'  => (int)round($total * ($mix['med']  / 100)),
          'hard' => (int)round($total * ($mix['hard'] / 100)),
      ];
      // đảm bảo tổng đúng bằng $total
      $diff = $total - array_sum($counts);
      if ($diff !== 0) $counts['med'] += $diff;

      $exam->questions()->detach();

      foreach ($counts as $difficulty=>$cnt) {
          if ($cnt <= 0) continue;
          $q = Question::when($data['subject_id'] ?? null, fn($x)=>$x->where('subject_id',$data['subject_id']))
              ->where('difficulty',$difficulty)
              ->inRandomOrder()->limit($cnt)->pluck('id');
          foreach ($q as $qid) {
              $exam->questions()->attach($qid, ['order'=>$exam->questions()->count()+1, 'points'=>1]);
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
        $q = Exam::with('subject')
            ->where('is_public', true)
            ->when($r->filled('subject_id'), fn($x)=>$x->where('subject_id',$r->subject_id))
            ->latest();

        return view('exams.catalog', [
            'exams'    => $q->paginate(12)->withQueryString(),
            'subjects' => Subject::orderBy('name')->get(),
            'filters'  => $r->only('subject_id')
        ]);
    }

}

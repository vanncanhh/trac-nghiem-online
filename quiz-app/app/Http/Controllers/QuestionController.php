<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\{Question, Subject, Option, Topic};  

class QuestionController extends Controller
{
    public function index(Request $r)
    {
        $q = Question::with(['subject','topicRef'])
            ->when(auth()->user()->role === 'teacher', fn($x) => $x->where('created_by', auth()->id()))
            ->when($r->filled('subject_id'), fn($x) => $x->where('subject_id', $r->subject_id))
            ->when($r->filled('topic_id'),   fn($x) => $x->where('topic_id',   $r->topic_id))   
            ->when($r->filled('difficulty'), fn($x) => $x->where('difficulty', $r->difficulty))
            ->when($r->filled('kw'),         fn($x) => $x->where('content', 'like', '%'.$r->kw.'%'))
            ->latest();

        return view('questions.index', [
            'qs'       => $q->paginate(12)->withQueryString(),
            'subjects' => Subject::orderBy('name')->get(),
            'topics'   => Topic::orderBy('name')->get(),               
            'filters'  => $r->only('subject_id','topic_id','difficulty','kw')  
        ]);
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $topics   = Topic::orderBy('name')->get();
        return view('questions.create', compact('subjects','topics'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'subject_id'=>'required|exists:subjects,id',
            'topic_id'=>'nullable|exists:topics,id',
            'content'=>'required|string',
            'difficulty'=>'required|in:easy,med,hard',
            'points'=>'required|integer|min:1',
            'source'=>'nullable|string|max:100',
            'image'=>'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'options'=>'required|array|min:2',
            'options.*.text'=>'required|string|max:255',
            'options.*.is_correct'=>'nullable|boolean'
        ]);

        // ít nhất 1 đáp án đúng
        $hasCorrect = collect($data['options'])->contains(fn($o) => !empty($o['is_correct']));
        abort_unless($hasCorrect, 422, 'Ít nhất 1 đáp án phải đúng.');

        $q = Question::create([
            'subject_id'=>$data['subject_id'],
            'topic_id'=>$data['topic_id'] ?? null,
            'created_by'=>auth()->id(),
            'content'=>$data['content'],
            'difficulty'=>$data['difficulty'],
            'points'=>$data['points'],
            'source'=>$data['source'] ?? null,
        ]);
        if ($r->hasFile('image')) {
            $q->image_path = $r->file('image')->store('questions', 'public');
            $q->save();
        }

        foreach ($data['options'] as $op) {
            $q->options()->create([
                'text' => $op['text'],
                'is_correct' => !empty($op['is_correct'])
            ]);
        }

        return redirect()->route('questions.index')->with('ok','Đã tạo câu hỏi');
    }

    public function edit(Question $question)
    {
        $this->ownerOrAdmin($question->created_by);
        $question->load('options');
        $subjects = Subject::orderBy('name')->get();
        $topics   = Topic::orderBy('name')->get();
        return view('questions.edit', compact('question','subjects','topics'));
    }

    public function update(Request $r, Question $question)
    {
        $this->ownerOrAdmin($question->created_by);

        $data = $r->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic_id'   => 'nullable|exists:topics,id',
            'content'    => 'required|string',
            'difficulty' => 'required|in:easy,med,hard',
            'points'     => 'required|integer|min:1',
            'topic'=>'nullable|string|max:100',
            'source'=>'nullable|string|max:100',
            'image'=>'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'options'    => 'required|array|min:2',
            'options.*.text' => 'required|string|max:255',
            'options.*.is_correct' => 'nullable|boolean'
        ]);

        $hasCorrect = collect($data['options'])->contains(fn($o) => !empty($o['is_correct']));
        abort_unless($hasCorrect, 422, 'Ít nhất 1 đáp án phải đúng.');

        $question->update($r->only('subject_id','topic_id','content','difficulty','points','topic','source'));
        if ($r->boolean('remove_image') && $question->image_path) {
            Storage::disk('public')->delete($question->image_path);
            $question->image_path = null;
            $question->save();
        }
        if ($r->hasFile('image')) {
            if ($question->image_path) Storage::disk('public')->delete($question->image_path);
            $question->image_path = $r->file('image')->store('questions','public');
            $question->save();
        }

        // đơn giản: xoá hết options cũ và tạo lại
        $question->options()->delete();
        foreach ($data['options'] as $op) {
            $question->options()->create([
                'text' => $op['text'],
                'is_correct' => !empty($op['is_correct'])
            ]);
        }

        return redirect()->route('questions.index')->with('ok','Đã cập nhật câu hỏi');
    }

    public function destroy(Question $question)
    {
        $this->ownerOrAdmin($question->created_by);
        $question->delete();
        return back()->with('ok','Đã xoá câu hỏi');
    }

    private function ownerOrAdmin($ownerId)
    {
        if (auth()->user()->role === 'admin') return;
        abort_unless($ownerId === auth()->id(), 403);
    }
}

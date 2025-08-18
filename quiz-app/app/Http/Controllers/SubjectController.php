<?php

namespace App\Http\Controllers;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    { 
        $items = Subject::orderBy('name')->paginate(20); 
        return view('subjects.index', compact('items')); 
    }
    public function create()
    { 
        return view('subjects.create'); 
    }
    public function store(Request $r)
    {
        $data = $r->validate(['name'=>'required|string|max:100|unique:subjects,name']);
        Subject::create($data); 
        return redirect()->route('subjects.index')->with('ok','Đã thêm môn');
    }
    public function edit(Subject $subject)
    { 
        return view('subjects.edit', compact('subject')); 
    }
    public function update(Request $r, Subject $subject)
    {
        $data = $r->validate(['name'=>"required|string|max:100|unique:subjects,name,{$subject->id}"]);
        $subject->update($data); 
        return redirect()->route('subjects.index')->with('ok','Đã cập nhật');
    }
    public function destroy(Subject $subject)
    { 
        $subject->delete(); 
        return redirect()->route('subjects.index')->with('ok','Đã xoá'); 
    }
}

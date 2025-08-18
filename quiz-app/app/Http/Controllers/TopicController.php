<?php

namespace App\Http\Controllers;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    { 
        $items=Topic::orderBy('name')->paginate(20); 
        return view('topics.index',compact('items')); 
    }
    public function create()
    { 
        return view('topics.create'); 
    }
    public function store(Request $r)
    { 
        $data=$r->validate(['name'=>'required|unique:topics,name']); 
        Topic::create($data); 
        return redirect()->route('topics.index')->with('ok','Đã thêm chủ đề'); 
    }
    public function edit(Topic $topic)
    { 
        return view('topics.edit',compact('topic')); 
    }
    public function update(Request $r, Topic $topic)
    { 
        $data=$r->validate(['name'=>"required|unique:topics,name,{$topic->id}"]); 
        $topic->update($data); 
        return back()->with('ok','Đã cập nhật'); 
    }
    public function destroy(Topic $topic)
    { 
        $topic->delete(); 
        return back()->with('ok','Đã xoá'); 
    }    
}

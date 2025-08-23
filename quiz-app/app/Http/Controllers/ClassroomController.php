<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(){
        $items = Classroom::orderBy('name')->paginate(20);
        return view('classrooms.index', compact('items'));
    }

    public function create(){
        return view('classrooms.create');
    }

    public function store(Request $r){
        $data = $r->validate([
            'name'=>'required|string|max:100|unique:classrooms,name',
            'note'=>'nullable|string|max:255'
        ]);
        Classroom::create($data);
        return redirect()->route('classrooms.index')->with('ok','Đã thêm lớp');
    }

    public function edit(Classroom $classroom){
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $r, Classroom $classroom){
        $data = $r->validate([
            'name'=>"required|string|max:100|unique:classrooms,name,{$classroom->id}",
            'note'=>'nullable|string|max:255'
        ]);
        $classroom->update($data);
        return redirect()->route('classrooms.index')->with('ok','Đã cập nhật');
    }

    public function destroy(Classroom $classroom){
        // Nếu lớp có exam, sẽ bị ràng buộc FK (RESTRICT hoặc NULL). Bạn có thể đổi logic theo nhu cầu.
        $classroom->delete();
        return redirect()->route('classrooms.index')->with('ok','Đã xoá');
    }
}

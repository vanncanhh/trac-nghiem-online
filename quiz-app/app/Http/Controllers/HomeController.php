<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Exam, Subject, Classroom};

class HomeController extends Controller
{
    public function home(Request $r)
    {
        // Dropdowns
        $classrooms = Classroom::orderBy('name')->get();
        $subjects   = Subject::orderBy('name')->get();

        // Đề public nổi bật để render thẻ (card)
        $featured = Exam::with(['subject','classroom'])
            ->where('is_public', true)
            ->latest()
            ->take(12)
            ->get();

        // Gom thành các "khối" giống ảnh (theo Lớp • Môn)
        $sections = $featured->groupBy(function($e){
            $cls = $e->classroom->name ?? 'Chung';
            $sub = $e->subject->name   ?? 'Tổng hợp';
            return strtoupper("MÔN {$sub} - LỚP {$cls}");
        });

        return view('home.index', compact('classrooms','subjects','sections','featured'));
    }
}

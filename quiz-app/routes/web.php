<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ClassroomController,AuthController,UserController,QuestionController,
    ExamController,AttemptController, SubjectController, TopicController, HomeController};

Route::get('/', [HomeController::class, 'home'])->name('home');

// trang đăng nhập/đăng ký cho guest
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class,'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class,'login']);
    Route::get('/register', [AuthController::class,'showRegister'])->name('register');
    Route::post('/register',[AuthController::class,'register']);
});

// đăng xuất
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth');

// DASHBOARD: chỉ cho admin/teacher
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth','role:admin,teacher'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Admin
    Route::middleware('role:admin')->group(function(){
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Teacher/Admin
    Route::middleware('role:admin,teacher')->group(function(){
        Route::resource('questions', QuestionController::class);
        Route::resource('exams', ExamController::class);
        Route::post('/exams/{exam}/auto-generate',[ExamController::class,'autoGenerate'])->name('exams.auto');
        Route::post('/exams/{exam}/publish',[ExamController::class,'publish'])->name('exams.publish');
        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::resource('topics',   TopicController::class)->except(['show']);
        Route::resource('classrooms', ClassroomController::class)->except(['show']);
    });

    // Student-facing
    Route::get('/catalog',[ExamController::class,'catalog'])->name('exams.catalog');
    Route::get('/exams/{exam}/start',[AttemptController::class,'start'])->name('attempts.start');
    Route::post('/attempts/{attempt}/submit',[AttemptController::class,'submit'])->name('attempts.submit');
    Route::get('/results',[AttemptController::class,'index'])->name('attempts.index');
    Route::get('/results/{attempt}',[AttemptController::class,'show'])->name('attempts.show');
});
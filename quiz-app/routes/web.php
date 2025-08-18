<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,UserController,QuestionController,ExamController,AttemptController, SubjectController, TopicController};

Route::get('/', fn()=>redirect('/dashboard'));
Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'showRegister']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard','dashboard');

    // Quản trị người dùng (admin)
    Route::middleware('role:admin')->group(function(){
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Giáo viên: ngân hàng câu hỏi & đề thi
    Route::middleware('role:admin,teacher')->group(function(){
        Route::resource('questions', QuestionController::class);
        Route::resource('exams', ExamController::class);
        Route::post('/exams/{exam}/auto-generate',[ExamController::class,'autoGenerate'])->name('exams.auto');
        Route::post('/exams/{exam}/publish',[ExamController::class,'publish'])->name('exams.publish');
        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::resource('topics',   TopicController::class)->except(['show']);
    });

    // Danh mục đề public cho học sinh
    Route::get('/catalog',[ExamController::class,'catalog'])->name('exams.catalog');

    // Thi & kết quả
    Route::get('/exams/{exam}/start',[AttemptController::class,'start'])->name('attempts.start');
    Route::post('/attempts/{attempt}/submit',[AttemptController::class,'submit'])->name('attempts.submit');
    Route::get('/results',[AttemptController::class,'index'])->name('attempts.index');
    Route::get('/results/{attempt}',[AttemptController::class,'show'])->name('attempts.show');
});
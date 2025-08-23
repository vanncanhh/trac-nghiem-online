<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = ['title','subject_id','classroom_id','created_by','duration_minutes','is_public'];

    public function subject(){ return $this->belongsTo(Subject::class); }
    public function questions(){
        return $this->belongsToMany(Question::class,'exam_questions')
                    ->withPivot(['order','points'])->withTimestamps();
    }
    public function classroom(){ return $this->belongsTo(Classroom::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;
    protected $fillable=['exam_id','user_id','started_at','submitted_at','score','max_score'];
     protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];
    public function answers(){ return $this->hasMany(AttemptAnswer::class); }
    public function exam(){ return $this->belongsTo(Exam::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    use HasFactory;
    protected $fillable = ['attempt_id','question_id','selected_option_id','is_correct','awarded_points'];

    public function attempt(){ return $this->belongsTo(Attempt::class); }
    public function question(){ return $this->belongsTo(Question::class); }
}

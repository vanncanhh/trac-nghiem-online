<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable=['subject_id','created_by','content','difficulty','points','topic','source','image_path'];
    public function subject(){ return $this->belongsTo(Subject::class); }
    public function topicRef(){ return $this->belongsTo(Topic::class, 'topic_id'); }
    public function options(){ return $this->hasMany(Option::class); }
    public function getImageUrlAttribute(){
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}

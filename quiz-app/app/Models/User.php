<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

   protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password','remember_token'];
    public function examsCreated(){ return $this->hasMany(Exam::class,'created_by'); }
    public function attempts(){ return $this->hasMany(Attempt::class); }
    public function isAdmin(){ return $this->role === 'admin'; }
    public function isTeacher(){ return $this->role === 'teacher'; }
    public function isStudent(){ return $this->role === 'student'; }
}

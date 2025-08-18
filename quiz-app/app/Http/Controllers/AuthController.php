<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Hash;   
use App\Models\User;                  

class AuthController extends Controller
{
    public function showLogin() { 
        return view('auth.login'); 
    }

    public function showRegister() { 
        return view('auth.register'); 
    }

    public function register(Request $r) {
        $data = $r->validate([
          'name'     => 'required',
          'email'    => 'required|email|unique:users',
          'password' => 'required|min:6|confirmed',
          'role'     => 'required|in:admin,teacher,student'
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        Auth::login($user);
        return redirect('/dashboard');
    }

    public function login(Request $r) {
        $cred = $r->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($cred, $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect('/dashboard');
        }
        return back()->withErrors(['email' => 'Sai email/mật khẩu']);
    }

    public function logout(Request $r) {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login');
    }
}

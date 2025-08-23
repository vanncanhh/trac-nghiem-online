<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ---- helper: xác định trang rơi về theo role
    private function roleHome(): string
    {
        $role = auth()->user()->role ?? 'student';
        return in_array($role, ['admin','teacher']) ? route('dashboard') : route('home');
    }

    public function showLogin()
    {
        if (auth()->check()) return redirect($this->roleHome());
        return view('auth.login');
    }

    public function showRegister()
    {
        if (auth()->check()) return redirect($this->roleHome());
        return view('auth.register');
    }

    public function register(Request $r)
    {
        $data = $r->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,teacher,student'
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        Auth::login($user);
        return redirect()->intended($this->roleHome());
    }

    public function login(Request $r)
    {
        $cred = $r->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($cred, $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect()->intended($this->roleHome());
        }

        return back()->withErrors(['email' => 'Sai email/mật khẩu']);
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('home');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\DemoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index', [
            'title' => 'Login',
            'active' => 'login',
        ]);
    }

    public function authenticate(Request $request, DemoAccess $demo)
    {
        if (config('demo.enabled')) {
            $role = $request->validate(['role' => ['nullable', 'string', 'in:superadmin,organization,manager']])['role'] ?? config('demo.default_role');
            $demo->enter($request, $role);

            return redirect()->intended('/dashboard/utama');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard/utama');
        }

        return back()->with('loginError', 'Login Failed!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

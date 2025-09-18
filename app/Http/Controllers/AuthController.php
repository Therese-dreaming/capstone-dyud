<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function createLogin()
    {
        return view('auth.login');
    }

    public function storeLogin(Request $request)
    {
        $credentials = $request->only('id_number', 'password');

        if (Auth::attempt($credentials)) {
            // Update last login timestamp
            $user = Auth::user();
            $user->update(['last_login' => now()]);
            
            // Clear any intended URL to prevent role-based redirect issues
            $request->session()->forget('url.intended');
            
            // Redirect based on user role to appropriate dashboard
            $redirectRoute = match($user->role) {
                'superadmin' => '/dashboard',
                'admin' => '/dashboard', 
                'gsu' => '/gsu/assets',
                'purchasing' => '/purchasing/assets',
                'user' => '/dashboard',
                default => '/dashboard'
            };
            
            return redirect($redirectRoute)->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return redirect('login')->withErrors([
            'id_number' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('id_number'));
    }

    public function createRegister()
    {
        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'id_number' => $request->id_number,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user', // Default role for new registrations
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}

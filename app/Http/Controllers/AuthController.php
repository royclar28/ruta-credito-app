<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = User::with('role', 'agency')->where('is_active', true)->get();

        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Switcher instantáneo de perfil de usuario (para facilitar pruebas y demostraciones operativas).
     */
    public function switchUser(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);
        Auth::login($user);
        $request->session()->regenerate();

        return back()->with('success', "Perfil cambiado a: {$user->name} ({$user->role?->name})");
    }
}

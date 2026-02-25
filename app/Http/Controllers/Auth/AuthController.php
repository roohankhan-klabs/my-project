<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($request->expectsJson()) {
                return response()->json([
                    'user' => $user,
                    'is_admin' => (bool) $user->is_admin,
                ]);
            }

            if ($user->is_admin) {
                return redirect()->to('/nova/resources/users');
            }

            return redirect()->route('dashboard');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        return redirect()
            ->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid credentials');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'user' => $user,
                'is_admin' => (bool) $user->is_admin,
            ], 201);
        }

        if ($user->is_admin) {
            return redirect()->to('/nova/resources/users')->with('success', 'Registration successful');
        }

        return redirect()->route('dashboard')->with('success', 'Registration successful');
    }

    public function userLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out successfully.']);
        }

        return redirect()->route('signin');
    }
}

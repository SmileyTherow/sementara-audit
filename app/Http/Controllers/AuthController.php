<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function account(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'current_password' => [
                    'required',
                    'current_password:web',
                ],
                'password' => [
                    'required',
                    'confirmed',
                    'string',
                    'min:8',
                    'max:100',
                ],
            ]);

            $request->user()->forceFill([
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
            ])->save();

            $request->session()->regenerate();

            return back()->with(
                'success',
                'Akun berhasil diperbarui.'
            );
        }

        return view('admin.account');
    }
}

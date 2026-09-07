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
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($data + ['is_active' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai.']);
        }
        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function account(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate(['name' => 'required|string|max:150', 'current_password' => 'required|current_password', 'password' => 'required|confirmed|min:10|max:100']);
            $request->user()->update(['name' => $data['name'], 'password' => Hash::make($data['password'])]);
            $request->session()->regenerate();

            return back()->with('success', 'Akun berhasil diperbarui.');
        }

        return view('admin.account');
    }
}

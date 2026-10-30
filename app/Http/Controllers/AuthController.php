<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function home(): RedirectResponse
    {
        return redirect()->route(Auth::check() ? 'dashboard' : 'login');
    }

    public function showLogin(): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang benar.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi belum sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('dashboard') : view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:guru,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang benar.',
            'email.unique' => 'Email ini sudah digunakan. Silakan masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi harus terdiri dari sedikitnya 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi dengan benar.',
        ]);

        $guru = Guru::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'kata_sandi' => $data['password'],
            'peran' => 'guru',
        ]);

        Auth::login($guru);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun guru berhasil dibuat. Selamat datang di KawanAjar!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bapak/Ibu berhasil keluar.');
    }
}

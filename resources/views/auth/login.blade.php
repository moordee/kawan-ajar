@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <h1 class="h3 fw-bold">Selamat datang, Bapak/Ibu Guru</h1>
    <p class="text-muted mb-4">Masuk untuk melanjutkan menyiapkan pembelajaran.</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label required-label" for="email">Alamat email</label>
            <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label required-label" for="password">Kata sandi</label>
            <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
            <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit">Masuk</button>
    </form>
    <p class="text-center mt-4 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai guru</a></p>
    @if (app()->environment('local'))
        <div class="alert alert-light border mt-4 mb-0 small">
            <strong>Akun contoh:</strong> demo@kawanajar.id<br>
            <strong>Kata sandi:</strong> Kawanajar#Demo2026
        </div>
    @endif
@endsection

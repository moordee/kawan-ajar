@extends('layouts.auth')

@section('title', 'Daftar akun guru')

@section('content')
    <h1 class="h3 fw-bold">Buat akun guru</h1>
    <p class="text-muted mb-4">Isi data berikut untuk mulai menggunakan KawanAjar.</p>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label required-label" for="nama">Nama lengkap</label>
            <input class="form-control" id="nama" name="nama" value="{{ old('nama') }}" autocomplete="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label required-label" for="email">Alamat email</label>
            <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label required-label" for="password">Kata sandi</label>
            <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" required>
            <div class="form-text">Gunakan sedikitnya 8 karakter.</div>
        </div>
        <div class="mb-4">
            <label class="form-label required-label" for="password_confirmation">Ulangi kata sandi</label>
            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit">Buat akun</button>
    </form>
    <p class="text-center mt-4 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
@endsection

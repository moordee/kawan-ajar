<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — KawanAjar</title>
    <link rel="stylesheet" href="{{ asset('vendor/sb-admin/styles.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/kawanajar.css') }}">
</head>
<body class="auth-page d-flex align-items-center py-4">
    <main class="container">
        <div class="text-center mb-3">
            <a class="auth-brand text-decoration-none" href="{{ route('home') }}"><i class="bi bi-book-half me-2"></i>KawanAjar</a>
            <p class="mb-0 mt-1">Teman guru menyiapkan pembelajaran</p>
        </div>
        <div class="card auth-card shadow mx-auto">
            <div class="card-body p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
        <p class="small text-center mt-3 mb-0">KawanAjar membantu Bapak/Ibu menyiapkan materi dan penilaian dengan lebih mudah.</p>
    </main>
</body>
</html>

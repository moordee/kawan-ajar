<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — KawanAjar</title>
    <link rel="stylesheet" href="{{ asset('vendor/sb-admin/styles.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/kawanajar.css') }}">
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <a class="navbar-brand ps-3" href="{{ route('dashboard') }}">KawanAjar</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 ms-2" id="sidebarToggle" type="button" aria-label="Buka atau tutup menu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="ms-auto me-3 d-flex align-items-center gap-3">
            <span class="d-none d-md-inline text-white-50 small">Halo, {{ Auth::user()->nama }}</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i>Akun
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text fw-semibold">{{ Auth::user()->nama }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Ruang Guru</div>
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-house-door"></i></span>Beranda
                        </a>
                        <div class="sb-sidenav-menu-heading">Bahan Ajar</div>
                        <a class="nav-link {{ request()->routeIs('materi.*') ? 'active' : '' }}" href="{{ route('materi.index') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-journal-bookmark"></i></span>Lemari Materi
                        </a>
                        <a class="nav-link {{ request()->routeIs('bank-soal.*') ? 'active' : '' }}" href="{{ route('bank-soal.index') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-question-circle"></i></span>Bank Soal
                        </a>
                        <div class="sb-sidenav-menu-heading">Penilaian</div>
                        <a class="nav-link {{ request()->routeIs('ulangan.*') ? 'active' : '' }}" href="{{ route('ulangan.index') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-clipboard-check"></i></span>Ulangan Harian
                        </a>
                        <a class="nav-link {{ request()->routeIs('ujian-besar.*') ? 'active' : '' }}" href="{{ route('ujian-besar.index') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-clipboard-data"></i></span>UTS / SAS
                        </a>
                        <a class="nav-link {{ request()->routeIs('hasil.*') ? 'active' : '' }}" href="{{ route('hasil.index') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-bar-chart"></i></span>Hasil Belajar
                        </a>
                        <div class="sb-sidenav-menu-heading">Data Sekolah</div>
                        <a class="nav-link {{ request()->routeIs('sekolah.kelas.*') ? 'active' : '' }}" href="{{ route('sekolah.kelas.index') }}">
                            <span class="sb-nav-link-icon"><i class="bi bi-people"></i></span>Kelas & Siswa
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small text-white-50">Masuk sebagai</div>
                    {{ Auth::user()->nama }}
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-3 px-lg-4 pb-4">
                @if (session('success'))
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="status">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mt-3" role="alert">
                        <strong>Mohon periksa kembali:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
            <footer class="py-3 px-4 mt-auto border-top bg-white small text-muted">
                KawanAjar · Teman guru menyiapkan pembelajaran dan penilaian.
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/kawanajar.js') }}"></script>
</body>
</html>

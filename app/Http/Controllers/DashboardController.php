<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MateriPelajaran;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\UjianBesar;
use App\Models\Ulangan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $guruId = auth()->id();

        return view('dashboard', [
            'jumlahMateri' => MateriPelajaran::where('guru_id', $guruId)->count(),
            'jumlahSoal' => Soal::count(),
            'jumlahUlangan' => Ulangan::where('guru_id', $guruId)->count(),
            'jumlahUjian' => UjianBesar::where('guru_id', $guruId)->count(),
            'jumlahKelas' => Kelas::where('status_aktif', true)->count(),
            'jumlahSiswa' => Siswa::count(),
            'materiTerbaru' => MateriPelajaran::with(['kelas', 'mapel'])->where('guru_id', $guruId)->latest('tanggal_dibuat')->take(5)->get(),
            'ulanganTerbaru' => Ulangan::with(['kelas', 'mapel'])->where('guru_id', $guruId)->latest('tanggal_dibuat')->take(4)->get(),
        ]);
    }
}

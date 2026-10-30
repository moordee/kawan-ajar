<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UjianBesarController;
use App\Http\Controllers\UlanganController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'home'])->name('home');
Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
Route::post('/masuk', [AuthController::class, 'login'])->name('login.store');
Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('materi', MateriController::class)->except('show');
    Route::get('/bank-soal', [BankSoalController::class, 'index'])->name('bank-soal.index');
    Route::get('/bank-soal/buat', [BankSoalController::class, 'create'])->name('bank-soal.create');
    Route::post('/bank-soal', [BankSoalController::class, 'store'])->name('bank-soal.store');
    Route::get('/bank-soal/{soal}/ubah', [BankSoalController::class, 'edit'])->name('bank-soal.edit');
    Route::put('/bank-soal/{soal}', [BankSoalController::class, 'update'])->name('bank-soal.update');
    Route::delete('/bank-soal/{soal}', [BankSoalController::class, 'destroy'])->name('bank-soal.destroy');
    Route::post('/bank-soal/buat-dengan-ai', [BankSoalController::class, 'generateWithAi'])->name('bank-soal.generate-ai');
    Route::resource('ulangan', UlanganController::class)->except('show');
    Route::get('/ulangan/{ulangan}/hasil', [UlanganController::class, 'results'])->name('ulangan.results');
    Route::post('/ulangan/{ulangan}/hasil', [UlanganController::class, 'storeResult'])->name('ulangan.results.store');
    Route::resource('ujian-besar', UjianBesarController::class)->except('show');
    Route::get('/ujian-besar/{ujian_besar}/hasil', [UjianBesarController::class, 'assessmentResults'])->name('ujian-besar.results');
    Route::post('/ujian-besar/{ujian_besar}/hasil', [UjianBesarController::class, 'storeResult'])->name('ujian-besar.results.store');
    Route::get('/hasil', [UjianBesarController::class, 'results'])->name('hasil.index');
    Route::resource('kelas', SchoolController::class)->except('show')->names('sekolah.kelas')->parameters(['kelas' => 'kelas']);
    Route::post('/kelas/{kelas}/siswa', [SchoolController::class, 'storeStudent'])->name('sekolah.siswa.store');
    Route::post('/kelas/{kelas}/mata-pelajaran', [SchoolController::class, 'storeTeachingAssignment'])->name('sekolah.pengajaran.store');
    Route::delete('/pengajaran/{pengajaran}', [SchoolController::class, 'destroyTeachingAssignment'])->name('sekolah.pengajaran.destroy');
    Route::put('/siswa/{siswa}', [SchoolController::class, 'updateStudent'])->name('sekolah.siswa.update');
    Route::delete('/siswa/{siswa}', [SchoolController::class, 'destroyStudent'])->name('sekolah.siswa.destroy');
});

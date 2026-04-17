<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CpLevelController;
use App\Http\Controllers\CpMataKuliahController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\MataKuliahPilihanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransferSksController;
use App\Http\Controllers\UserController;
use App\Models\CpMataKuliah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->role;

        return match ($role) {
            'admin' => redirect()->route('dashboard'),
            'Mahasiswa' => redirect('/form?step=1'),
            default => redirect()->route('dashboard'),
        };
    }

    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::middleware(['role:Admin'])->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::get('/mahasiswa', [UserController::class, 'index'])->name('mahasiswa.index');
        Route::get('/mahasiswa/{user}/edit', [UserController::class, 'edit'])->name('mahasiswa.edit');
        Route::get('/mahasiswa/create', [UserController::class, 'create'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [UserController::class, 'store'])->name('mahasiswa.store');
        Route::delete('/mahasiswa/{user}', [UserController::class, 'destroy'])->name('mahasiswa.destroy');
        Route::get('/mahasiswa/template', [UserController::class, 'templateDownload'])->name('mahasiswa.template');
        Route::post('/mahasiswa/import', [UserController::class, 'userImport'])->name('mahasiswa.import');
        Route::put('mahasiswa/{user}/update', [UserController::class, 'update'])->name('mahasiswa.update');

        Route::get('/asesor', [UserController::class, 'asesorIndex'])->name('asesor.index');
        Route::get('/asesor/{user}/edit', [UserController::class, 'asesorEdit'])->name('asesor.edit');
        Route::get('/asesor/create', [UserController::class, 'asesorCreate'])->name('asesor.create');
        Route::post('/asesor', [UserController::class, 'asesorStore'])->name('asesor.store');
        Route::delete('/asesor/{user}', [UserController::class, 'asesorDestroy'])->name('asesor.destroy');
        Route::get('/asesor/template', [UserController::class, 'asesorTemplateDownload'])->name('asesor.template');
        Route::post('/asesor/import', [UserController::class, 'asesorImport'])->name('asesor.import');
        Route::put('asesor/{user}/update', [UserController::class, 'asesorUpdate'])->name('asesor.update');


        //JURUSAN 
        Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
        Route::get('/jurusan/create', [JurusanController::class, 'create'])->name('jurusan.create');
        Route::post('/jurusan', [JurusanController::class, 'store'])->name('jurusan.store');
        Route::get('/jurusan/{jurusan}/edit', [JurusanController::class, 'edit'])->name('jurusan.edit');
        Route::delete('/jurusan/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');
        Route::put('jurusan/{jurusan}/update', [JurusanController::class, 'update'])->name('jurusan.update');
        Route::get('/jurusan/template', [JurusanController::class, 'templateDownload'])->name('jurusan.template');
        Route::post('/jurusan/import', [JurusanController::class, 'import'])->name('jurusan.import');
        // Mata Kuliah 

        Route::get('/mk', [MataKuliahController::class, 'index'])->name('mk.index');
        Route::get('/mk/{mk}/edit', [MataKuliahController::class, 'edit'])->name('mk.edit');
        Route::post('/mk', [MataKuliahController::class, 'store'])->name('mk.store');
        Route::delete('/mk/{mk}', [MataKuliahController::class, 'destroy'])->name('mk.destroy');
        Route::put('/mk/{mk}/update', [MataKuliahController::class, 'update'])->name('mk.update');
        Route::get('/mk/cpmk/template/{kode_mk}', [MataKuliahController::class, 'templateCpmk'])->name('mk.cpmk.template');
        Route::post('/mk/cpmk/import', [MataKuliahController::class, 'importCpmk'])->name('mk.cpmk.import');

         Route::delete('/cpmk/{cpmk}', [CpMataKuliahController::class, 'destroy'])->name('cpmk.destroy');
         Route::put('/cpmk/{cpmk}/update', [CpMataKuliahController::class, 'update'])->name('cpmk.update');
         Route::post('/cpmk', [CpMataKuliahController::class, 'store'])->name('cpmk.store');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::middleware(['auth', 'role:Mahasiswa'])->group(function () {
        Route::get('/form', [FormController::class, 'show'])
            ->name('form.step');

        Route::post('/form/step1', [FormController::class, 'storeStep1'])
            ->name('form.step1.store');

        Route::get('/mata-kuliah', [FormController::class, 'getMataKuliah'])
            ->name('form.mata-kuliah');

        Route::post('/attachment/store', [AttachmentController::class, 'store'])->name('attachment.store');
        Route::post('/attachment/store/cv', [AttachmentController::class, 'storeCv'])->name('attachment.store.cv');

        Route::delete('/attachment/delete/{id}', [AttachmentController::class, 'delete'])->name('attachment.delete');


        Route::get('/mata-kuliah-pilihan', [MataKuliahPilihanController::class, 'index'])->name('mk-pilihan.index');

        Route::post('/mata-kuliah-pilihan', [MataKuliahPilihanController::class, 'store'])->name('mk-pilihan.store');
        Route::put('/mata-kuliah-pilihan/{id}', [MataKuliahPilihanController::class, 'update'])->name('mk-pilihan.update');

        Route::delete('/mata-kuliah-pilihan/{id}', [MataKuliahPilihanController::class, 'destroy'])->name('mk-pilihan.destroy');

        Route::post('/cp-level/store', [CpLevelController::class, 'store'])->name('cp-level.store');

        Route::post('/transfer-sks/store', [TransferSksController::class, 'store'])->name('transfer.store');
    });
});


require __DIR__ . '/auth.php';

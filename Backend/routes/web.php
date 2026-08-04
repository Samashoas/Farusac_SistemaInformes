<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'ShowLogin'])->name('login');
Route::get('/auth/google', [AuthController::class, 'redirect'])->name('google.login'); // Va a Google
Route::get('/auth/google/callback', [AuthController::class, 'callback']); // Google nos devuelve aquí
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    Route::middleware('role:docente')->group(function () {
        Route::get('/docente/panel', function () { return view('docente.dashboard'); })->name('docente.dashboard');
    });

    Route::middleware('role:jefe')->group(function () {
        Route::get('/jefe/panel', function () { return view('jefe.dashboard'); })->name('jefe.dashboard');
    });

    Route::middleware('role:administrador')->group(function () {
        Route::get('/admin/panel', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/carga-datos', [AdminController::class, 'cargaDatosView'])->name('admin.carga-datos');
        Route::post('/admin/importar-usuarios', [AdminController::class, 'CargaUsuariosCsv'])->name('admin.importar');
    });
});
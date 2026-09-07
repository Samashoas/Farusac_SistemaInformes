<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocenteController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'ShowLogin'])->name('login');
Route::get('/auth/google', [AuthController::class, 'redirect'])->name('google.login'); // Va a Google
Route::get('/auth/google/callback', [AuthController::class, 'callback']); // Google nos devuelve aquí
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    Route::middleware('role:docente')->group(function () {
        Route::get('/docente/panel', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
        Route::post('/docente/cursos/asignar', [DocenteController::class, 'asignarCurso'])->name('docente.cursos.asignar');
        Route::delete('/docente/cursos/{id}', [DocenteController::class, 'desasignarCurso'])->name('docente.cursos.desasignar');
        Route::get('/docente/informes', [DocenteController::class, 'historialInformes'])->name('docente.informes');
        Route::get('/docente/informes/crear', [DocenteController::class, 'crearInformeView'])->name('docente.informes.crear');
        Route::post('/docente/informes', [DocenteController::class, 'guardarInforme'])->name('docente.informes.guardar');
        Route::get('/docente/informes/{id}/editar', [DocenteController::class, 'editarInformeView'])->name('docente.informes.editar');
        Route::put('/docente/informes/{id}', [DocenteController::class, 'actualizarInforme'])->name('docente.informes.actualizar');
        Route::delete('/docente/informes/{id}', [DocenteController::class, 'eliminarInforme'])->name('docente.informes.eliminar');
        Route::get('/docente/informes/{id}/ver', [DocenteController::class, 'verInforme'])->name('docente.informes.ver');
    });

    Route::middleware('role:jefe')->group(function () {
        Route::get('/jefe/panel', function () { return view('jefe.dashboard'); })->name('jefe.dashboard');
    });

    Route::middleware('role:administrador')->group(function () {
        Route::get('/admin/panel', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/carga-datos', [AdminController::class, 'cargaDatosView'])->name('admin.carga-datos');
        Route::get('/admin/usuarios', [AdminController::class, 'usuariosView'])->name('admin.usuarios');
        Route::get('/admin/cursos', [AdminController::class, 'cursosView'])->name('admin.cursos');
        Route::post('/admin/cursos', [AdminController::class, 'crearCurso'])->name('admin.cursos.crear');
        Route::delete('/admin/cursos/{id}', [AdminController::class, 'eliminarCurso'])->name('admin.cursos.eliminar');
        Route::post('/admin/usuarios', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
        Route::put('/admin/usuarios/{id}', [AdminController::class, 'editarUsuario'])->name('admin.usuarios.editar');
        Route::post('/admin/usuarios/{id}/toggle-status', [AdminController::class, 'toggleEstadoUsuario'])->name('admin.usuarios.toggle-status');
        Route::post('/admin/importar-usuarios', [AdminController::class, 'CargaUsuariosCsv'])->name('admin.importar');
        Route::post('/admin/importar-cursos', [AdminController::class, 'CargaCursosCsv'])->name('admin.importar-cursos');
    });
});
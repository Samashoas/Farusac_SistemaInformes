<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\CoordinadorController;

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
        Route::get('/docente/perfil', [DocenteController::class, 'perfilView'])->name('docente.perfil');
        Route::put('/docente/perfil', [DocenteController::class, 'actualizarPerfil'])->name('docente.perfil.actualizar');
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

    Route::middleware('role:coordinador')->group(function () {
        Route::get('/coordinador/panel', [CoordinadorController::class, 'dashboard'])->name('coordinador.dashboard');
        Route::post('/coordinador/area/seleccionar', [CoordinadorController::class, 'seleccionarArea'])->name('coordinador.area.seleccionar');
        Route::get('/coordinador/perfil', [CoordinadorController::class, 'perfilView'])->name('coordinador.perfil');
        Route::put('/coordinador/perfil', [CoordinadorController::class, 'actualizarPerfil'])->name('coordinador.perfil.actualizar');
        Route::post('/coordinador/cursos/asignar', [CoordinadorController::class, 'asignarCurso'])->name('coordinador.cursos.asignar');
        Route::delete('/coordinador/cursos/{id}', [CoordinadorController::class, 'desasignarCurso'])->name('coordinador.cursos.desasignar');
        Route::get('/coordinador/informes', [CoordinadorController::class, 'historialInformes'])->name('coordinador.informes');
        Route::get('/coordinador/informes/crear', [CoordinadorController::class, 'crearInformeView'])->name('coordinador.informes.crear');
        Route::post('/coordinador/informes', [CoordinadorController::class, 'guardarInforme'])->name('coordinador.informes.guardar');
        Route::get('/coordinador/informes/{id}/editar', [CoordinadorController::class, 'editarInformeView'])->name('coordinador.informes.editar');
        Route::put('/coordinador/informes/{id}', [CoordinadorController::class, 'actualizarInforme'])->name('coordinador.informes.actualizar');
        Route::delete('/coordinador/informes/{id}', [CoordinadorController::class, 'eliminarInforme'])->name('coordinador.informes.eliminar');
        Route::get('/coordinador/informes/{id}/ver', [CoordinadorController::class, 'verInforme'])->name('coordinador.informes.ver');
        Route::get('/coordinador/informes/{id}/detalle-json', [CoordinadorController::class, 'obtenerDetalleInformeDocenteJson'])->name('coordinador.informes.detalle-json');

        // Módulo de Informes de Coordinación de Área (Oficial FARUSAC)
        Route::get('/coordinador/informes-coordinacion/datos-docentes', [CoordinadorController::class, 'obtenerDatosDocentesArea'])->name('coordinador.informes-coordinacion.datos-docentes');
        Route::get('/coordinador/informes-coordinacion/crear', [CoordinadorController::class, 'crearInformeCoordinacionView'])->name('coordinador.informes-coordinacion.crear');
        Route::post('/coordinador/informes-coordinacion', [CoordinadorController::class, 'guardarInformeCoordinacion'])->name('coordinador.informes-coordinacion.guardar');
        Route::get('/coordinador/informes-coordinacion/{id}/editar', [CoordinadorController::class, 'editarInformeCoordinacionView'])->name('coordinador.informes-coordinacion.editar');
        Route::put('/coordinador/informes-coordinacion/{id}', [CoordinadorController::class, 'actualizarInformeCoordinacion'])->name('coordinador.informes-coordinacion.actualizar');
        Route::delete('/coordinador/informes-coordinacion/{id}', [CoordinadorController::class, 'eliminarInformeCoordinacion'])->name('coordinador.informes-coordinacion.eliminar');
        Route::get('/coordinador/informes-coordinacion/{id}/ver', [CoordinadorController::class, 'verInformeCoordinacion'])->name('coordinador.informes-coordinacion.ver');
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
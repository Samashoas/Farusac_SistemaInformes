<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\CoordinadorController;

Route::match(['get', 'post'], '/', function () {
    return redirect()->route('login');
});
Route::match(['get', 'post'], '/login', [AuthController::class, 'showLogin'])->name('login');

// Rutas de autenticación con Rate Limiting (máx 10 peticiones por minuto para prevenir abusos a OAuth)
Route::middleware('throttle:10,1')->group(function () {
    Route::match(['get', 'post'], '/auth/google', [AuthController::class, 'redirect'])->name('google.login'); // Va a Google
    Route::match(['get', 'post'], '/auth/google/callback', [AuthController::class, 'callback']); // Google nos devuelve aquí
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // Rutas compartidas para gestión docente (docentes y coordinadores con cursos a su cargo)
    Route::middleware('role:docente,coordinador,administrador')->group(function () {
        Route::get('/docente/perfil', [DocenteController::class, 'perfilView'])->name('docente.perfil');
        Route::put('/docente/perfil', [DocenteController::class, 'actualizarPerfil'])->name('docente.perfil.actualizar');
        Route::post('/docente/cursos/asignar', [DocenteController::class, 'asignarCurso'])->name('docente.cursos.asignar');
        Route::delete('/docente/cursos/{curso}', [DocenteController::class, 'desasignarCurso'])->name('docente.cursos.desasignar');
        Route::get('/docente/informes', [DocenteController::class, 'historialInformes'])->name('docente.informes');
        Route::get('/docente/informes/crear', [DocenteController::class, 'crearInformeView'])->name('docente.informes.crear');
        Route::post('/docente/informes', [DocenteController::class, 'guardarInforme'])->name('docente.informes.guardar');
        Route::get('/docente/informes/{informe}/editar', [DocenteController::class, 'editarInformeView'])->name('docente.informes.editar');
        Route::put('/docente/informes/{informe}', [DocenteController::class, 'actualizarInforme'])->name('docente.informes.actualizar');
        Route::delete('/docente/informes/{informe}', [DocenteController::class, 'eliminarInforme'])->name('docente.informes.eliminar');
        Route::get('/docente/informes/{informe}/ver', [DocenteController::class, 'verInforme'])->name('docente.informes.ver');

        // Alias para compatibilidad con vistas de coordinador que manejan sus cursos como docente
        Route::get('/coordinador/perfil', [DocenteController::class, 'perfilView'])->name('coordinador.perfil');
        Route::put('/coordinador/perfil', [DocenteController::class, 'actualizarPerfil'])->name('coordinador.perfil.actualizar');
        Route::post('/coordinador/cursos/asignar', [DocenteController::class, 'asignarCurso'])->name('coordinador.cursos.asignar');
        Route::delete('/coordinador/cursos/{curso}', [DocenteController::class, 'desasignarCurso'])->name('coordinador.cursos.desasignar');
        Route::get('/coordinador/informes', [DocenteController::class, 'historialInformes'])->name('coordinador.informes');
        Route::get('/coordinador/informes/crear', [DocenteController::class, 'crearInformeView'])->name('coordinador.informes.crear');
        Route::post('/coordinador/informes', [DocenteController::class, 'guardarInforme'])->name('coordinador.informes.guardar');
        Route::get('/coordinador/informes/{informe}/editar', [DocenteController::class, 'editarInformeView'])->name('coordinador.informes.editar');
        Route::put('/coordinador/informes/{informe}', [DocenteController::class, 'actualizarInforme'])->name('coordinador.informes.actualizar');
        Route::delete('/coordinador/informes/{informe}', [DocenteController::class, 'eliminarInforme'])->name('coordinador.informes.eliminar');
        Route::get('/coordinador/informes/{informe}/ver', [DocenteController::class, 'verInforme'])->name('coordinador.informes.ver');
    });

    // Rutas exclusivas del panel Docente
    Route::middleware('role:docente')->group(function () {
        Route::get('/docente/panel', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
    });

    // Rutas exclusivas de Coordinación académica
    Route::middleware('role:coordinador')->group(function () {
        Route::get('/coordinador/panel', [CoordinadorController::class, 'dashboard'])->name('coordinador.dashboard');
        Route::post('/coordinador/area/seleccionar', [CoordinadorController::class, 'seleccionarArea'])->name('coordinador.area.seleccionar');
        Route::get('/coordinador/informes/{informe}/detalle-json', [CoordinadorController::class, 'obtenerDetalleInformeDocenteJson'])->name('coordinador.informes.detalle-json');

        // Módulo de Informes de Coordinación de Área (Oficial FARUSAC)
        Route::get('/coordinador/informes-coordinacion/datos-docentes', [CoordinadorController::class, 'obtenerDatosDocentesArea'])->name('coordinador.informes-coordinacion.datos-docentes');
        Route::get('/coordinador/informes-coordinacion/crear', [CoordinadorController::class, 'crearInformeCoordinacionView'])->name('coordinador.informes-coordinacion.crear');
        Route::post('/coordinador/informes-coordinacion', [CoordinadorController::class, 'guardarInformeCoordinacion'])->name('coordinador.informes-coordinacion.guardar');
        Route::get('/coordinador/informes-coordinacion/{informeCoordinacion}/editar', [CoordinadorController::class, 'editarInformeCoordinacionView'])->name('coordinador.informes-coordinacion.editar');
        Route::put('/coordinador/informes-coordinacion/{informeCoordinacion}', [CoordinadorController::class, 'actualizarInformeCoordinacion'])->name('coordinador.informes-coordinacion.actualizar');
        Route::delete('/coordinador/informes-coordinacion/{informeCoordinacion}', [CoordinadorController::class, 'eliminarInformeCoordinacion'])->name('coordinador.informes-coordinacion.eliminar');
        Route::get('/coordinador/informes-coordinacion/{informeCoordinacion}/ver', [CoordinadorController::class, 'verInformeCoordinacion'])->name('coordinador.informes-coordinacion.ver');
    });

    // Rutas de Administración
    Route::middleware('role:administrador')->group(function () {
        Route::get('/admin/panel', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/carga-datos', [AdminController::class, 'cargaDatosView'])->name('admin.carga-datos');
        Route::get('/admin/usuarios', [AdminController::class, 'usuariosView'])->name('admin.usuarios');
        Route::get('/admin/cursos', [AdminController::class, 'cursosView'])->name('admin.cursos');
        Route::post('/admin/cursos', [AdminController::class, 'crearCurso'])->name('admin.cursos.crear');
        Route::delete('/admin/cursos/{curso}', [AdminController::class, 'eliminarCurso'])->name('admin.cursos.eliminar');
        Route::post('/admin/usuarios', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
        Route::put('/admin/usuarios/{usuario}', [AdminController::class, 'editarUsuario'])->name('admin.usuarios.editar');
        Route::post('/admin/usuarios/{usuario}/toggle-status', [AdminController::class, 'toggleEstadoUsuario'])->name('admin.usuarios.toggle-status');
        Route::post('/admin/importar-usuarios', [AdminController::class, 'cargaUsuariosCsv'])->name('admin.importar');
        Route::post('/admin/importar-cursos', [AdminController::class, 'cargaCursosCsv'])->name('admin.importar-cursos');
        Route::post('/admin/importar-carga-academica', [AdminController::class, 'cargaAcademicaCsv'])->name('admin.importar-carga-academica');
        Route::post('/admin/carga-datos/academica', [AdminController::class, 'cargaAcademicaCsv'])->name('admin.carga-datos.academica');

        // Módulo de Informes para Administrador
        Route::get('/admin/informes', [AdminController::class, 'informesView'])->name('admin.informes');
        Route::get('/admin/informes/docente/{informe}/ver', [AdminController::class, 'verInformeDocente'])->name('admin.informes.docente.ver');
        Route::get('/admin/informes/docente/{informe}/detalle-json', [AdminController::class, 'obtenerDetalleInformeDocenteJson'])->name('admin.informes.docente.detalle-json');
        Route::delete('/admin/informes/docente/{informe}', [AdminController::class, 'eliminarInformeDocente'])->name('admin.informes.docente.eliminar');
        Route::get('/admin/informes/coordinacion/{informeCoordinacion}/ver', [AdminController::class, 'verInformeCoordinacion'])->name('admin.informes.coordinacion.ver');
        Route::get('/admin/informes/coordinacion/{informeCoordinacion}/detalle-json', [AdminController::class, 'obtenerDetalleInformeCoordinacionJson'])->name('admin.informes.coordinacion.detalle-json');
        Route::delete('/admin/informes/coordinacion/{informeCoordinacion}', [AdminController::class, 'eliminarInformeCoordinacion'])->name('admin.informes.coordinacion.eliminar');

        // Descarga Masiva y Consolidado
        Route::get('/admin/informes/descargar-zip', [AdminController::class, 'descargarInformesZip'])->name('admin.informes.descargar-zip');
        Route::get('/admin/informes/lista-descarga-zip', [AdminController::class, 'obtenerListaDescargaZipJson'])->name('admin.informes.lista-descarga-zip');
        Route::get('/admin/informes/descargar-csv', [AdminController::class, 'descargarInformesCsv'])->name('admin.informes.descargar-csv');
        Route::get('/admin/informes/consolidado-pdf', [AdminController::class, 'verConsolidadoPdf'])->name('admin.informes.consolidado-pdf');
        Route::post('/admin/informes/descargar-seleccionados-zip', [AdminController::class, 'descargarSeleccionadosZip'])->name('admin.informes.descargar-seleccionados-zip');
        Route::post('/admin/informes/lista-seleccionados-zip', [AdminController::class, 'obtenerListaSeleccionadosZipJson'])->name('admin.informes.lista-seleccionados-zip');
        Route::post('/admin/informes/consolidado-seleccionados-pdf', [AdminController::class, 'verConsolidadoSeleccionadosPdf'])->name('admin.informes.consolidado-seleccionados-pdf');
    });
});

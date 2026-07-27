<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get("/", [AuthController::class,"ShowLogin"])->name("login");

Route::post("/login", [AuthController::class,"login"]);

Route::post('logout', [AuthController::class, 'logout']) -> name('logout');

Route::middleware('auth')->group(function(){
    Route::get('/admin/panel', function(){
        return view('admin.dashboard');  
    })->name('admin.dashboard');

    Route::get('/jefe/panel', function(){
        return view('jefe.dashboard');
    })->name('jefe.dashboard');

    Route::get('/docente/panel', function(){
        return view('docente.dashboard');
    })->name('docente.dashboad');
});

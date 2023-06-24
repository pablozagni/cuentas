<?php

use App\Http\Controllers\CuentaController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[HomeController::class,'index'])->name('home');

// Cuentas
Route::get('/cuentas',[CuentaController::class,'index'])->name('cuentas.index');
Route::any('/ui/cuentashijas',[CuentaController::class,'cuentasHijas'])->name('cuentashijas');
Route::any('/ui/cuenta', [CuentaController::class,'cuentasDetalle'])->name('cuentas.detalle');
Route::get('/cuentas/create/{id?}',[CuentaController::class,'create'])->name('cuentas.create');
Route::post('/cuentas/create/{id}',[CuentaController::class,'createPost']);
Route::get('/cuentas/edit/{id?}',[CuentaController::class,'edit'])->name('cuentas.edit');
Route::post('/cuentas/edit/{id}',[CuentaController::class,'editPost']);

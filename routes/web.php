<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers as Controllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controllers\HomeController::class, 'index']);

Route::prefix('/admin')->group(function () {
    
    Route::get('/login', [Controllers\AdminController::class, 'login'])->name('login');
    Route::post('/login', [Controllers\AdminController::class, 'loginAction']);
    Route::get('/register', [Controllers\AdminController::class, 'register']);
    Route::get('/', [Controllers\AdminController::class, 'index']);

});

Route::get('/{slug}', [Controllers\PageController::class, 'index']);

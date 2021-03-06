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
    Route::post('/register', [Controllers\AdminController::class, 'registerAction']);
    Route::get('/logout', [Controllers\AdminController::class, 'logout']);
    Route::get('/', [Controllers\AdminController::class, 'index']);
    Route::get('/{slug}/links', [Controllers\AdminController::class, 'pageLinks']);
    Route::get('/{slug}/design', [Controllers\AdminController::class, 'pageDesign']);
    Route::get('/{slug}/stats', [Controllers\AdminController::class, 'pageStats']);
    Route::get('/linkorder/{linkid}/{pos}/', [Controllers\AdminController::class, 'linkOrderUpdate']);
    Route::get('/{slug}/newlink', [Controllers\AdminController::class, 'newLink']);
    Route::post('/{slug}/newlink', [Controllers\AdminController::class, 'newLinkAction']);
    Route::get('/{slug}/editlink/{linkid}', [Controllers\AdminController::class, 'editLink']);
    Route::post('/{slug}/editlink/{linkid}', [Controllers\AdminController::class, 'editLinkAction']);
    Route::get('/{slug}/dellink/{linkid}', [Controllers\AdminController::class, 'dellink']);
});

Route::get('/{slug}', [Controllers\PageController::class, 'index']);

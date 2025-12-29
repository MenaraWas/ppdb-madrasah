<?php

use App\Http\Controllers\PublicPpdbController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ppdb', [PublicPpdbController::class, 'home'])->name('ppdb.home');
Route::post('/ppdb/initiate', [PublicPpdbController::class, 'initiate'])->name('ppdb.initiate');
Route::get('/ppdb/status/{token}', [PublicPpdbController::class, 'status'])->name('ppdb.status');
Route::post('/ppdb/status/{token}/upload', [PublicPpdbController::class, 'uploadDocument'])
    ->name('ppdb.upload_document');

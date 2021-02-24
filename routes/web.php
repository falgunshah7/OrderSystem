<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

/*Route::get('/', function () {
    return view('order');
});*/

Route::get('/',[OrderController::class,'index'])->name('order.index');
Route::post('/getGrid',[OrderController::class,'getGrid'])->name('getGrid');
Route::post('/store',[OrderController::class,'store'])->name('store');


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashpoardController;

Auth::routes();

Route::get('/',[DashpoardController::class,'index'] );

Route::get('/charts',[DashpoardController::class,'topProductsToday'])->middleware('auth');


Route::get('/tables', function () {
    return view('pages.tables.basic-table');
});




Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashpoardController;
use App\Http\Controllers\orders;

Auth::routes();

Route::get('/',[DashpoardController::class,'index'] );
Route::get('/charts',[DashpoardController::class,'topProductsToday'])->middleware('auth');

Route::get('/orders',[orders::class,'index'])->middleware('auth');
Route::get('/orders/{id}/edit', [Orders::class, 'editorder'])
     ->name('orders.edit');

Route::post('/orders/{id}', [Orders::class, 'update'])
     ->name('orders.update');

route::post('addnewitme/{id}',[orders::class,'addnewitme']); 
Route::get('/ordersdelete/{id}', [Orders::class, 'deleteorder'])->name('orders.delete');

Route::get('/orders/create',[orders::class,'create'])->name('orders.create');
Route::post('/orderstore',[orders::class,'store']);


Route::get('/tables', function () {
    return view('pages.tables.basic-table');
});




Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

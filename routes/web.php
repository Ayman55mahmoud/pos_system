<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/charts', function () {
    return view('pages.charts.chartjs');
});


Route::get('/tables', function () {
    return view('pages.tables.basic-table');
});


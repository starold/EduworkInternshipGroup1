<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vehicles', function () {
    return view('vehicles');
});

Route::get('/vehicles1', function () {
    return view('vehicles1');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});
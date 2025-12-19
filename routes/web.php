<?php

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug', function () {
    $roles = Role::all()->pluck('name', 'id');
    dd($roles);
});

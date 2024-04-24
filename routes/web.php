<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/login',[AdminLoginController::class,'index'])->name('admin.login');

Route::group(['prefix' => 'admin'],function(){

    Route::group(['middleware' => 'admin.guest'],function(){
        Route::get('/login',[AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authinticate',[AdminLoginController::class,'authinticate'])->name('admin.authinticate');

    });

    Route::group(['midddleware' => 'admin.auth'],function(){
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');

    });
});
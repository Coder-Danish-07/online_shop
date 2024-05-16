<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\SubCategoryController;
use Illuminate\Http\Request;

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

        //Category Route 
        Route::get('/category',[CategoryController::class,'index'])->name('category.index');
        Route::get('/category/create',[CategoryController::class,'create'])->name('category.create');
        Route::post('/category',[CategoryController::class,'store'])->name('category.store');
        Route::get('/category/{category}/edit',[CategoryController::class,'edit'])->name('category.edit');
        Route::put('/category/{category}',[CategoryController::class,'update'])->name('category.update');
        Route::delete('/category/{category}',[CategoryController::class,'destroy'])->name('category.delete');

        //Sub Category Route
        Route::get('/sub-category',[SubCategoryController::class,'index'])->name('sub-category.index');
        Route::get('/sub-category/create',[SubCategoryController::class,'create'])->name('sub-category.create');
        Route::post('/sub-category',[SubCategoryController::class,'store'])->name('sub-category.store');

        //temp image route 
        Route::post('/temp-image',[TempImageController::class,'create'])->name('temp_image.create');

        Route::get('/getSlug',function(Request $request){
            $slug = '';
            if(!empty($request->title)){
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);
        })->name('getSlug');
    });
});
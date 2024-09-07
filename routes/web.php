<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductControlller;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');
// Route::get('/admin/login',[AdminLoginController::class,'index'])->name('admin.login');

Route::group(['prefix' => 'account'],function(){

    Route::group(['middleware' => 'guest'],function(){

        Route::get('/register',[AuthController::class,'register'])->name('account.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');
        Route::get('/login',[AuthController::class,'login'])->name('account.login');
        Route::post('/login',[AuthController::class,'authienticate'])->name('account.authienticate');

    });

    Route::group(['middleware' => 'auth'],function(){

        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');

    });

});




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
        Route::get('/sub-category/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-category.edit');
        Route::put('/sub-category/{category}',[SubCategoryController::class,'update'])->name('sub-category.update');
        Route::delete('/sub-category/{category}',[SubCategoryController::class,'destroy'])->name('sub-category.delete');

        //Brand Routes
        Route::get('/brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brands',[BrandController::class,'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit',[BrandController::class,'edit'])->name('brands.edit');
        Route::put('/brands/{brand}',[BrandController::class,'update'])->name('brands.update');
        Route::delete('/brands/{brand}',[BrandController::class,'destroy'])->name('brands.delete');

        //Product Routes
        Route::get('/products',[ProductControlller::class,'index'])->name('products.index');
        Route::get('/products/create',[ProductControlller::class,'create'])->name('products.create');
        Route::post('/products',[ProductControlller::class,'store'])->name('products.store');
        Route::get('/products/{product}/edit',[ProductControlller::class,'edit'])->name('products.edit');
        Route::put('/products/{product}',[ProductControlller::class,'update'])->name('products.update');
        Route::delete('/products/{product}',[ProductControlller::class,'destroy'])->name('products.delete');
        Route::get('/get-products',[ProductControlller::class,'getProducts'])->name('products.getProducts');

        //Product Image Update Route
        Route::post('/product-images/update',[ProductImageController::class,'update'])->name('product-images.update');
        Route::delete('/product-images',[ProductImageController::class,'destory'])->name('product-images.destroy');

        //Product SubCategory Routes
        Route::get('/product-sub-category',[ProductSubCategoryController::class,'index'])->name('product-sub-category.index');
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
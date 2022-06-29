<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserAddressesController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Auth;

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
//Route::get('/', [PagesController::class, 'root'])->name('root');

//Auth::routes(['verify' => true]);
Auth::routes();

Route::redirect('/', '/products')->name('root');
Route::prefix('products')->group(function () {
    Route::get('', [ProductsController::class, 'index'])->name('products.index');
    Route::group(['middleware' => ['auth']], function () {
        Route::post('{product}/favorite', [ProductsController::class, 'favor'])->name('products.favor');
        Route::delete('{product}/favorite', [ProductsController::class, 'disfavor'])->name('products.disfavor');
        Route::get('favorites',[ProductsController::class,'favorites'])->name('products.favorites');
    });
    Route::get('{product}', [ProductsController::class, 'show'])->name('products.show');
});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// auth中间件代表需要登录
Route::group(['middleware' => ['auth']], function () {
    Route::prefix('user_addresses')->group(function () {
        Route::get('', [UserAddressesController::class, 'index'])->name('user_addresses.index');
        Route::get('create', [UserAddressesController::class, 'create'])->name('user_addresses.create');
        Route::post('store', [UserAddressesController::class, 'store'])->name('user_addresses.store');
        Route::get('{user_address}', [UserAddressesController::class, 'edit'])->name('user_addresses.edit');
        Route::put('{user_address}', [UserAddressesController::class, 'update'])->name('user_addresses.update');
        Route::delete('{user_address}', [UserAddressesController::class, 'destroy'])->name('user_addresses.destroy');
    });

});

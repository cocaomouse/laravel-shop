<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserAddressesController;
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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', [PagesController::class, 'root'])->name('root');

//Auth::routes(['verify' => true]);
Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// auth中间件代表需要登录
Route::group(['middleware' => ['auth']], function () {
    Route::prefix('user_addresses')->group(function () {
        Route::get('', [UserAddressesController::class, 'index'])->name('user_addresses.index');
        Route::get('create', [UserAddressesController::class, 'create'])->name('user_addresses.create');
        Route::post('store', [UserAddressesController::class, 'store'])->name('user_addresses.store');
    });

});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserAddressesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\AlipayController;
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

/*Route::get('alipay',function (){
    return app('alipay')->web([
        'out_trade_no' => ''.time(),
        'total_amount' => '0.01',
        'subject' => 'yansongda 测试 - 1'
    ]);
});
Route::get('web', [AlipayController::class, 'web'])->name('pay.web');*/

Route::redirect('/', '/products')->name('root');
Route::prefix('products')->group(function () {
    Route::get('', [ProductsController::class, 'index'])->name('products.index');
    Route::group(['middleware' => ['auth']], function () {
        Route::post('{product}/favorite', [ProductsController::class, 'favor'])->name('products.favor');
        Route::delete('{product}/favorite', [ProductsController::class, 'disfavor'])->name('products.disfavor');
        Route::get('favorites', [ProductsController::class, 'favorites'])->name('products.favorites');
    });
    Route::get('{product}', [ProductsController::class, 'show'])->name('products.show');
});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// auth中间件代表需要登录
Route::group(['middleware' => ['auth']], function () {
    // 购物车
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [CartController::class, 'add'])->name('cart.add');
    Route::delete('cart/{sku}', [CartController::class, 'remove'])->name('cart.remove');
    // 购物车创建订单
    Route::prefix('orders')->group(function () {
        Route::post('', [OrdersController::class, 'store'])->name('orders.store');
        // 订单列表
        Route::get('', [OrdersController::class, 'index'])->name('orders.index');
        // 订单详情
        Route::get('{order}', [OrdersController::class, 'show'])->name('orders.show');
    });
    // 用户地址
    Route::prefix('user_addresses')->group(function () {
        Route::get('', [UserAddressesController::class, 'index'])->name('user_addresses.index');
        Route::get('create', [UserAddressesController::class, 'create'])->name('user_addresses.create');
        Route::post('store', [UserAddressesController::class, 'store'])->name('user_addresses.store');
        Route::get('{user_address}', [UserAddressesController::class, 'edit'])->name('user_addresses.edit');
        Route::put('{user_address}', [UserAddressesController::class, 'update'])->name('user_addresses.update');
        Route::delete('{user_address}', [UserAddressesController::class, 'destroy'])->name('user_addresses.destroy');
    });

});

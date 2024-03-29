<?php

use App\Http\Controllers\AlipayController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponCodesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserAddressesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
        // 确认收货
        Route::post('{order}/received', [OrdersController::class,'received'])->name('orders.received');
        // 用户评价
        Route::get('{order}/review', [OrdersController::class,'review'])->name('orders.review.show');
        Route::post('{order}/review', [OrdersController::class,'sendReview'])->name('orders.review.store');
        // 退款申请
        Route::post('{order}/apply_refund', [OrdersController::class,'applyRefund'])->name('orders.apply_refund');
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
    // 支付
    Route::get('payment/{order}/alipay', [PaymentController::class,'payByAlipay'])->name('payment.alipay');
    // 支付前端回调
    Route::get('payment/alipay/return', [PaymentController::class,'alipayReturn'])->name('payment.alipay.return');
    // 优惠券列表
    Route::get('coupon_codes/{code}', [CouponCodesController::class,'show'])->name('coupon_codes.show');
});
// 支付服务端回调
Route::post('payment/alipay/notify', [PaymentController::class,'alipayNotify'])->name('payment.alipay.notify');

Route::prefix('products')->group(function () {
    Route::get('', [ProductsController::class, 'index'])->name('products.index');
    Route::group(['middleware' => ['auth']], function () {
        Route::post('{product}/favorite', [ProductsController::class, 'favor'])->name('products.favor');
        Route::delete('{product}/favorite', [ProductsController::class, 'disfavor'])->name('products.disfavor');
        Route::get('favorites', [ProductsController::class, 'favorites'])->name('products.favorites');
    });
    Route::get('{product}', [ProductsController::class, 'show'])->name('products.show');
});

// 发送邮件测试
//Route::get('payment/take_email', [PaymentController::class,'takeEmail'])->name('payment.take_email');

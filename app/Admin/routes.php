<?php

use App\Admin\Controllers\CouponCodesController;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\OrdersController;
use App\Admin\Controllers\ProductsController;
use App\Admin\Controllers\UsersController;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    //'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', [HomeController::class, 'index'])->name('home');
    $router->get('users', [UsersController::class, 'index']);
    $router->get('products', [ProductsController::class, 'index']);
    $router->get('products/create', [ProductsController::class, 'create']);
    $router->post('products', [ProductsController::class, 'store']);
    $router->get('products/{id}/edit', [ProductsController::class,'edit']);
    $router->put('products/{id}', [ProductsController::class,'update']);
    $router->get('orders', [OrdersController::class,'index'])->name('admin.orders.index');
    $router->get('orders/{order}', [OrdersController::class,'show'])->name('admin.orders.show');
    $router->post('orders/{order}/ship', [OrdersController::class,'ship'])->name('admin.orders.ship');
    $router->post('orders/{order}/refund', [OrdersController::class,'handleRefund'])->name('admin.orders.handle_refund');
    $router->get('coupon_codes', [CouponCodesController::class,'index']);
    $router->post('coupon_codes', [CouponCodesController::class,'store']);
    $router->get('coupon_codes/create', [CouponCodesController::class,'create']);
    $router->get('coupon_codes/{id}/edit', [CouponCodesController::class,'edit']);
    $router->put('coupon_codes/{id}', [CouponCodesController::class,'update']);
    $router->delete('coupon_codes/{id}', [CouponCodesController::class,'destroy']);
});

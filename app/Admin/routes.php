<?php

use Illuminate\Routing\Router;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\UsersController;
use App\Admin\Controllers\ProductsController;

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
    $router->get('products/{id}/edit',[ProductsController::class,'edit']);
    $router->put('products/{id}',[ProductsController::class,'update']);
});

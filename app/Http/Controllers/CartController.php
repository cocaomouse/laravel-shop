<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;

class CartController extends Controller
{
    public function add(AddCartRequest $request,CartItem $cartItem)
    {
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');

        // 从数据库中查询该商品是否已经在购物车中
        $cart = $user->cartItems()->where('product_sku_id', $skuId)->first();
        if ($cart) {
            // 如果存在则直接叠加商品数量
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $cartItem->amount = $amount;
            $cartItem->user()->associate($user->id);
            $cartItem->productSku()->associate($skuId);
            $cartItem->save();
        }

        return [];
    }
}

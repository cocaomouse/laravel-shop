<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSku;

class ProductsController extends Controller
{
    //
    public function index(Request $request, Product $product)
    {
        $products = $product::query()->where('on_sale', true)->paginate(16);

        return view('products.index', ['products' => $products]);
    }
}

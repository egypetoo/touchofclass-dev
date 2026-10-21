<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->active()
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return ProductResource::make($product->load('category'));
    }
}

<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        // TODO: optional filter /api/products?category=electric
        $categoryFilter = request()->input('category');

        return ProductIndexResource::collection(
            Product::withCategory($categoryFilter)->paginate(10)
        );
    }

    // api/products/sony-tv-XYZ
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}

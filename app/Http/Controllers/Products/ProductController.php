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
        return ProductIndexResource::collection(Product::paginate(10));
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}

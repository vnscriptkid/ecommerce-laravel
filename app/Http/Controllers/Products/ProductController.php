<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Scoping\Scopes\CategoryScope;

class ProductController extends Controller
{
    public function index()
    {
        // TODO: optional filter /api/products?category=electric
        return ProductIndexResource::collection(
            Product::with('variations.stock')->ofScopes($this->scopes())
                ->paginate(10)
        );
    }

    private function scopes()
    {
        return [
            'category' => new CategoryScope()
        ];
    }

    // api/products/sony-tv-XYZ
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}

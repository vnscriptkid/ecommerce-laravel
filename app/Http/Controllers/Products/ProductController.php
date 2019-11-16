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

        $productCollectionResult = null;

        if (!is_null($categoryFilter) && is_string($categoryFilter)) {
            $productCollectionResult = Product::all()->filter(function ($product) use ($categoryFilter) {
                $categoryListOfProduct = $product->categories->map(function ($category) {
                    return $category->name;
                })->toArray();

                if (in_array($categoryFilter, $categoryListOfProduct)) {
                    return true;
                }
                return false;
            });
        } else {
            $productCollectionResult = Product::paginate(10);
        }

        return ProductIndexResource::collection($productCollectionResult);
    }

    // api/products/sony-tv-XYZ
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}

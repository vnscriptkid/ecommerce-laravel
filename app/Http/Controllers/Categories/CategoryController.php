<?php

namespace App\Http\Controllers\Categories;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController
{
    public function index()
    {
        return CategoryResource::collection(
            Category::rootParents()->with('subCategories')->ordered()->get() // 1 level deep
            // go deeper in the tree as you want
            // Category::rootParents()->ordered()->get()
            // Category::rootParents()->with('subCategories.subCategories')->ordered()->get()
        );
    }
}

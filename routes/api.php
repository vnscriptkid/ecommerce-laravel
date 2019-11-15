<?php

use Illuminate\Support\Facades\Route;

Route::resource('categories', 'Categories\CategoryController');
Route::resource('products', 'Products\ProductController');

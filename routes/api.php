<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::resource('categories', 'Categories\CategoryController');

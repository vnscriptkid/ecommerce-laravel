<?php

namespace App\Models;

use App\Models\Traits\IsOrderable;
use App\Models\Traits\IsSelfReference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use IsOrderable, IsSelfReference;

    protected $fillable = ['name', 'order'];

    // relations
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    // many to many
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }
}

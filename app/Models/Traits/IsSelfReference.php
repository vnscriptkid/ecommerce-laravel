<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait IsSelfReference
{
    public function scopeRootParents(Builder $builder)
    {
        return $builder->where('parent_id', null);
    }

    public function scopeNotRootParents(Builder $builder)
    {
        return $builder->where('parent_id', '!=', null);
    }
}

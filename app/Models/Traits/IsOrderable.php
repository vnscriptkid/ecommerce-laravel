<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait IsOrderable
{
    public function scopeOrdered(Builder $builder, string $orderBy = 'asc')
    {
        return $builder->orderBy('order', $orderBy);
    }
}

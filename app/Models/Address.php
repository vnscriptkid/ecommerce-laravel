<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['name', 'address_1', 'city', 'postal_code', 'country_id', 'default'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            if ($address->default) {
                // find all addresses of the same user
                // un-default them all
                $address->user->addresses()->update([
                    'default' => false
                ]);
            }
        });
    }
}

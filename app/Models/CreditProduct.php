<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'is_active',
    ];

    public function subproducts(): HasMany
    {
        return $this->hasMany(CreditSubproduct::class, 'product_id');
    }

    public function creditRequests(): HasMany
    {
        return $this->hasMany(CreditRequest::class, 'product_id');
    }
}

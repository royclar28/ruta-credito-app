<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditSubproduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreditProduct::class, 'product_id');
    }

    public function creditRequests(): HasMany
    {
        return $this->hasMany(CreditRequest::class, 'subproduct_id');
    }
}

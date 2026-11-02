<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'code',
        'name',
        'address',
        'phone',
        'is_active',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function creditRequests(): HasMany
    {
        return $this->hasMany(CreditRequest::class);
    }
}

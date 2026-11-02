<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'document_number',
        'customer_type',
        'business_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'state_id',
        'agency_id',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function creditRequests(): HasMany
    {
        return $this->hasMany(CreditRequest::class);
    }

    public function getFormattedDocumentAttribute(): string
    {
        return $this->document_type . '-' . $this->document_number;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditRequestHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'credit_request_id',
        'from_status_id',
        'to_status_id',
        'user_id',
        'action_performed',
        'observations',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function creditRequest(): BelongsTo
    {
        return $this->belongsTo(CreditRequest::class);
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(CreditStatus::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(CreditStatus::class, 'to_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

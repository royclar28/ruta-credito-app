<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_status_id',
        'to_status_id',
        'allowed_role_id',
        'action_name',
        'action_type',
        'action_color',
        'icon',
        'requires_observation',
        'is_batch_allowed',
    ];

    protected function casts(): array
    {
        return [
            'requires_observation' => 'boolean',
            'is_batch_allowed' => 'boolean',
        ];
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(CreditStatus::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(CreditStatus::class, 'to_status_id');
    }

    public function allowedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'allowed_role_id');
    }
}

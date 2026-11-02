<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'name',
        'stage',
        'badge_color',
        'is_terminal',
    ];

    protected function casts(): array
    {
        return [
            'is_terminal' => 'boolean',
        ];
    }

    public function creditRequests(): HasMany
    {
        return $this->hasMany(CreditRequest::class, 'current_status_id');
    }

    public function outgoingTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_status_id');
    }

    public function incomingTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'to_status_id');
    }
}

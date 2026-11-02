<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'customer_id',
        'product_id',
        'subproduct_id',
        'modality_id',
        'agency_id',
        'current_status_id',
        'assigned_user_id',
        'registered_by_user_id',
        'requested_amount',
        'approved_amount',
        'currency',
        'exchange_rate',
        'amount_uvc',
        'destination',
        'public_entity',
        'request_date',
        'decision_date',
        'observations',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'amount_uvc' => 'decimal:4',
            'public_entity' => 'boolean',
            'is_active' => 'boolean',
            'request_date' => 'date',
            'decision_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreditProduct::class, 'product_id');
    }

    public function subproduct(): BelongsTo
    {
        return $this->belongsTo(CreditSubproduct::class, 'subproduct_id');
    }

    public function modality(): BelongsTo
    {
        return $this->belongsTo(CreditModality::class, 'modality_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(CreditStatus::class, 'current_status_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function registeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CreditRequestHistory::class)->orderBy('created_at', 'desc');
    }

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(CreditLegalDocument::class);
    }

    /**
     * Deduce la Gerencia General responsable (Regla canónica de negocio de Ruta de Crédito)
     */
    public function getGerenciaGeneralAttribute(): string
    {
        $statusName = mb_strtoupper($this->currentStatus?->name ?? '');
        $productName = mb_strtoupper($this->product?->name ?? '');
        $subproductName = mb_strtoupper($this->subproduct?->name ?? '');
        $docType = $this->customer?->document_type;

        if (str_contains($statusName, 'CARTERIZADOS')) {
            return 'Gerencia General de Clientes Carterizados';
        }

        if (
            str_contains($statusName, 'GRANDES EMPRESAS') ||
            str_contains($statusName, 'GOBIERNO') ||
            str_contains($productName, 'GRANDES EMPRESAS')
        ) {
            return 'G.G Negocios Grandes Empresas y Gobierno';
        }

        if (
            str_contains($statusName, 'COMUNAL') ||
            str_contains($statusName, 'MICRO') ||
            str_contains($statusName, 'EMPRENDEDOR') ||
            str_contains($productName, 'MICROCRÉDITO') ||
            str_contains($subproductName, 'CREDIMUJER') ||
            str_contains($subproductName, 'CREDIFUTURO') ||
            str_contains($subproductName, 'CREDIAGRO') ||
            str_contains($subproductName, 'CREDICRECER')
        ) {
            return 'G.G Negocios, Red Comunal, Micro y Emprendedores';
        }

        if (
            str_contains($statusName, 'PYME') ||
            str_contains($statusName, 'CONSUMO') ||
            str_contains($statusName, 'PERSONAS') ||
            str_contains($productName, 'NOMINA') ||
            str_contains($productName, 'CONSUMO') ||
            str_contains($productName, 'HIPOTECARIO') ||
            str_contains($subproductName, 'CREDIPERSONAL')
        ) {
            return 'G.G Personas y PYME';
        }

        if (str_contains($productName, 'COMERCIAL')) {
            if ($docType === 'J' || $docType === 'G') {
                return 'G.G Negocios Grandes Empresas y Gobierno';
            }
            return 'G.G Personas y PYME';
        }

        return 'G.G Personas y PYME';
    }
}

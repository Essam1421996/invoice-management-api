<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Contract extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    protected $fillable = [
        'tenant_id',
        'unit_name',
        'customer_name',
        'rent_amount',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => ContractStatus::class,
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeForTenant(Builder $query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

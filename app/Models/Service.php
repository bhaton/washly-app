<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'item_type',
        'package_type',
        'description',
        'price',
        'express_price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'express_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function isKiloanEligible(): bool
    {
        if ($this->category === 'kiloan') {
            return false; // Skip the main kiloan package options from item list
        }
        $type = $this->item_type ?? 'both';
        return in_array($type, ['kiloan', 'both'], true);
    }

    public function isSatuanEligible(): bool
    {
        if ($this->category === 'kiloan') {
            return false;
        }
        $type = $this->item_type ?? 'both';
        return in_array($type, ['satuan', 'both'], true);
    }

    public function getTypeBadgeAttribute(): array
    {
        $type = $this->item_type ?? 'both';
        return match ($type) {
            'kiloan' => ['label' => 'Pakaian Sehari-hari (Kiloan)', 'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
            'satuan' => ['label' => 'Khusus Satuan', 'bg' => 'bg-amber-100 text-amber-800 border-amber-300'],
            default => ['label' => 'Kiloan & Satuan', 'bg' => 'bg-blue-100 text-blue-800 border-blue-300'],
        };
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

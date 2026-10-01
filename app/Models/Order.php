<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'pickup_driver_id',
        'delivery_driver_id',
        'status',
        'subtotal',
        'shipping_fee',
        'total',
        'pickup_name',
        'pickup_phone',
        'pickup_address',
        'pickup_date',
        'pickup_time',
        'pickup_notes',
        'delivery_name',
        'delivery_phone',
        'delivery_address',
        'delivery_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'pickup_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function pickupDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pickup_driver_id');
    }

    public function deliveryDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_driver_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pickup(): HasOne
    {
        return $this->hasOne(Pickup::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function pickupProof(): HasOne
    {
        return $this->hasOne(PickupProof::class);
    }

    public function deliveryProof(): HasOne
    {
        return $this->hasOne(DeliveryProof::class);
    }
}

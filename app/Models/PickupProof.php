<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_id',
        'order_id',
        'driver_id',
        'image_path',
        'notes',
    ];

    public function pickup(): BelongsTo
    {
        return $this->belongsTo(Pickup::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

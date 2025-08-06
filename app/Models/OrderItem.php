<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->price;
    }

    // 생성 시 자동으로 총 가격 계산
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            $orderItem->calculateTotalPrice();
        });

        static::updating(function ($orderItem) {
            $orderItem->calculateTotalPrice();
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistory extends Model
{
    protected $fillable = [
        'order_id',
        'action',
        'old_status',
        'new_status',
        'reason',
        'changed_by',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // 주문변경 이력 관리
    public static function log(int $orderId, string $action, array $data = []): void
    {
        self::create([
            'order_id' => $orderId,
            'action' => $action,
            'old_status' => $data['old_status'] ?? null,
            'new_status' => $data['new_status'] ?? null,
            'reason' => $data['reason'] ?? null,
            'changed_by' => $data['changed_by'] ?? 'admin',
            'metadata' => $data['metadata'] ?? null
        ]);
    }
}

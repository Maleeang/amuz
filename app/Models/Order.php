<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Order extends Model
{
    use SoftDeletes;
    
    // 모델 이벤트 등록
    protected static function booted()
    {
        static::updating(function ($order) {
            $original = $order->getOriginal();
            $changes = $order->getDirty();
            
            if (!empty($changes)) {
                $changeDetails = [];
                foreach ($changes as $field => $newValue) {
                    $oldValue = $original[$field] ?? null;
                    $changeDetails[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
                
                OrderHistory::log($order->id, 'order_updated', [
                    'reason' => '주문 정보 수정',
                    'changed_by' => 'admin',
                    'metadata' => [
                        'changes' => $changeDetails,
                        'ip_address' => request()->ip() ?? 'system',
                        'user_agent' => request()->userAgent() ?? 'system'
                    ]
                ]);
            }
        });
        
        static::deleting(function ($order) {
            OrderHistory::log($order->id, 'order_deleted', [
                'old_status' => $order->status,
                'reason' => '주문 삭제',
                'changed_by' => 'admin',
                'metadata' => [
                    'ip_address' => request()->ip() ?? 'system',
                    'user_agent' => request()->userAgent() ?? 'system'
                ]
            ]);
        });
        
        static::restored(function ($order) {
            OrderHistory::log($order->id, 'order_restored', [
                'new_status' => $order->status,
                'reason' => '주문 복원',
                'changed_by' => 'admin',
                'metadata' => [
                    'ip_address' => request()->ip() ?? 'system',
                    'user_agent' => request()->userAgent() ?? 'system'
                ]
            ]);
        });
    }
    
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'ordered_at',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at', 'desc');
    }

    public function cancel(): bool
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }

        foreach ($this->orderItems as $item) {
            $item->product->restoreStock($item->quantity);
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
        return true;
    }

    public function calculateTotal(): void
    {
        $total = $this->orderItems->sum('total_price');
        $this->update(['total_amount' => $total]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('ordered_at', '>=', Carbon::parse($startDate));
        }
        if ($endDate) {
            $query->where('ordered_at', '<=', Carbon::parse($endDate)->endOfDay());
        }
        return $query;
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => '대기중',
            self::STATUS_PAID => '결제완료',
            self::STATUS_SHIPPED => '배송중',
            self::STATUS_DELIVERED => '배송완료',
            self::STATUS_CANCELLED => '취소됨',
            default => '알 수 없음'
        };
    }

    public function getContent(string $field): mixed
    {
        return $this->getAttribute($field);
    }
}

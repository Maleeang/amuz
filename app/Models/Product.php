<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'image_path',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean'
    ];

    // 상품이 속한 카테고리들 (다대다)
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
                    ->withTimestamps();
    }

    // 첫 번째 카테고리를 가져오는 헬퍼 메소드 (하위 호환성)
    public function getFirstCategory(): ?Category
    {
        return $this->categories()->first();
    }
    
    // 첫 번째 카테고리 접근자 (하위 호환성)
    public function getFirstCategoryAttribute(): ?Category
    {
        return $this->categories->first();
    }

    // 카테고리 이름들을 문자열로 반환
    public function getCategoryNamesAttribute(): string
    {
        return $this->categories->pluck('name')->join(', ');
    }

    // 이미지 URL 접근자
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    // 포맷된 가격 접근자
    public function getFormattedPriceAttribute(): string
    {
        return '₩' . number_format($this->price);
    }

    // 상품의 주문 아이템들
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // 재고 감소
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    // 재고 복원
    public function restoreStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    // 재고 확인
    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    public function getContent(string $field): mixed
    {
        return $this->getAttribute($field);
    }
}

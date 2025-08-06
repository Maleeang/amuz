<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // 카테고리에 속한 상품들
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories')
                    ->withTimestamps();
    }

    // 활성화된 상품들만
    public function activeProducts(): BelongsToMany
    {
        return $this->products()->where('is_active', true);
    }

    // 활성화된 카테고리만
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getContent(string $field): mixed
    {
        return $this->getAttribute($field);
    }
}

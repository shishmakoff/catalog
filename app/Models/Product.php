<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'in_stock',
        'rating',
    ];

    protected $casts = [
        'in_stock' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'float',
    ];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param Builder $query
     * @param string|null $q
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $q): Builder
    {
        if (! $q) {
            return $query;
        }

        return $query->whereFullText('name', $q);
    }

    /**
     * @param Builder $query
     * @param $from
     * @param $to
     * @return Builder
     */
    public function scopePriceRange(Builder $query, $from, $to): Builder
    {
        if ($from !== null) {
            $query->where('price', '>=', $from);
        }

        if ($to !== null) {
            $query->where('price', '<=', $to);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param int|null $categoryId
     * @return Builder
     */
    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param bool|null $inStock
     * @return Builder
     */
    public function scopeInStock(Builder $query, ?bool $inStock): Builder
    {
        if ($inStock !== null) {
            $query->where('in_stock', $inStock);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param float|null $rating
     * @return Builder
     */
    public function scopeMinRating(Builder $query, ?float $rating): Builder
    {
        if ($rating !== null) {
            $query->where('rating', '>=', $rating);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string|null $sort
     * @return Builder
     */
    public function scopeSortBy(Builder $query, ?string $sort): Builder
    {
        switch ($sort) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'rating_desc':
                return $query->orderBy('rating', 'desc');
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            default:
                return $query->orderBy('id', 'asc');
        }
    }
}

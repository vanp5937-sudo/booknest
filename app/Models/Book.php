<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = ['category_id', 'sku', 'title', 'slug', 'author', 'publisher', 'isbn', 'description', 'price', 'original_price', 'stock', 'image_path', 'is_featured', 'status'];
    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'original_price' => 'decimal:2', 'stock' => 'integer', 'is_featured' => 'boolean'];
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

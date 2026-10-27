<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = ['user_id', 'order_code', 'customer_name', 'email', 'phone', 'address', 'subtotal', 'total', 'status', 'payment_method', 'note'];
    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'total' => 'decimal:2'];
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

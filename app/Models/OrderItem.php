<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrderItem extends Model
{
    use HasFactory;
    
    public function reference()
    {
        return $this->belongsTo(OrderReference::class, 'order_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
{
    return $this->belongsTo(Product::class,"products_id");
}

    protected $casts = [
        'products_id' => 'array', // Convierte automáticamente a array
    ];
    

}

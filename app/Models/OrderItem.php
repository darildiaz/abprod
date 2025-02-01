<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrderItem extends Model
{
    use HasFactory;
    public function model()
    {
        return $this->belongsTo(OrderMold::class, 'model_id');
    }
    public function reference()
    {
        return $this->belongsTo(OrderReference::class, 'reference_id');
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
    return $this->belongsTo(Product::class);
}

    protected $casts = [
        'productsItem' => 'array', // Convierte automáticamente a array
    ];
    public function orderItemProducts()
    {
        return $this->hasMany(Product::class);
    }
public function products()
{
    return $this->belongsToMany(Product::class);
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemProduct extends Model
{
    use HasFactory;
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function reference()
    {
        return $this->belongsTo(OrderReference::class, 'reference_id');
    }
}

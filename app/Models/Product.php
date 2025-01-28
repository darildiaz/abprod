<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productCenters()
    {
        return $this->hasMany(ProductCenter::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function orderReferences()
    {
        return $this->hasMany(OrderReference::class);
    }
    public function orderItems()
        {
            return $this->belongsToMany(OrderItem::class, 'order_item_products')
                ->withPivot('quantity')
                ->withTimestamps();
        }
}

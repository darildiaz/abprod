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
    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function price()
    {
        return $this->hasMany(Price::class);
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
    public function OrderReferenceSummarys()
    {
        return $this->hasMany(OrderReferenceSummary::class);
    }
    public function orderItems()
        {
            return $this->belongsToMany(OrderItem::class, 'order_item_products')
                ->withPivot('quantity')
                ->withTimestamps();
        }
    protected $casts = [
            'tags' => 'array',
        ];
    public function materialLists()
    {
        return $this->hasMany(MaterialList::class);
    }
}

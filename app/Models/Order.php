<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    public function product()
{
    return $this->belongsTo(Product::class);
}
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function classification()
    {
        return $this->belongsTo(QuestionCategory::class, 'classification_id');
    }

    public function questionAnswers()
    {
        return $this->hasMany(OrderQuestionAnswer::class);
    }
 
    public function orderMolds()
    {
        return $this->hasMany(OrderMold::class);
    }

    public function orderReferences()
    {
        return $this->hasMany(OrderReference::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    
}

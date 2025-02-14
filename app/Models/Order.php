<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
  
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
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
    public function productions()
    {
        return $this->hasMany(production::class);
    }

    public function orderReferences()
    {
        return $this->hasMany(OrderReference::class);
    }
    public function orderReferenceSummaries()
    {
        return $this->hasMany(OrderReferenceSummary::class);
    }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function classCenter(): HasMany
    {
        return $this->hasMany(ClassCenter::class);
    }
    
    public function planning(): HasMany
    {
        return $this->hasMany(Planning::class);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Production extends Model
{
    use HasFactory;
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    
    
    // public function product()
    // {
    //     return $this->belongsto(Product::class);
    // }
    
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
    public function details()
    {
        return $this->hasMany(Productiondet::class);
    }
}

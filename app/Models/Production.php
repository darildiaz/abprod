<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;
    public function productionPackage()
    {
        return $this->belongsTo(ProductionPackage::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

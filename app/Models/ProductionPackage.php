<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionPackage extends Model
{
    use HasFactory;
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }
    
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}

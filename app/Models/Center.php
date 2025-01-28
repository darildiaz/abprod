<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;
    public function productCenters()
    {
        return $this->hasMany(ProductCenter::class);
    }

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}

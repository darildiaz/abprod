<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roll extends Model
{
    use HasFactory;
    public function RollProdts()
    {
        return $this->hasMany(RollProdt::class);
    }
    public function RollErrors()
    {
        return $this->hasMany(RollError::class);
    }
}

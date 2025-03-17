<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollError extends Model
{
    use HasFactory;
    public function errorOrder()
    {
        return $this->belongsTo(errorOrder::class, );
    }
}

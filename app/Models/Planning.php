<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

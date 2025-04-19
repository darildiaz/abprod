<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class goaldDet extends Model
{
    use HasFactory;
    public function salesGoal()
    {
        return $this->belongsTo(SalesGoal::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

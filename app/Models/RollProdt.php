<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollProdt extends Model
{
    use HasFactory;
public function roll(){
        return $this->belongsTo(roll::class, 'roll_id');
    }

    
    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reorder extends Model
{
    use HasFactory;
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Productiondet extends Model
{
    use HasFactory;
    public function production()
    {
        return $this->belongsTo(Production::class);
    }
    

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function roll()
    {
        return $this->belongsTo(Roll::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

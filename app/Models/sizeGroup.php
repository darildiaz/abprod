<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sizeGroup extends Model
{
    use HasFactory;
    public function materialList()
    {
        return $this->hasMany(MaterialList::class);
    }
}

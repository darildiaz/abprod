<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryCounts extends Model
{
    use HasFactory;

    protected $table = 'product_category_counts';
    public $timestamps = false;

   
}

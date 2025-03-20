<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategoryCounts extends Model
{
    use HasFactory;

    protected $table = 'product_category_counts';
    public $timestamps = false;
    
    protected $fillable = [
        'production_date',
        'center_id',
        'category_id',
        'total_quantity',
    ];
    
    protected $casts = [
        'production_date' => 'date',
        'total_quantity' => 'integer',
    ];
    
    /**
     * Get the center that this count belongs to.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(ProductionCenter::class, 'center_id');
    }
    
    /**
     * Get the category that this count belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}

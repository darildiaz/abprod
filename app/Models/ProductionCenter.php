<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the production counts for this center.
     */
    public function productionCounts(): HasMany
    {
        return $this->hasMany(ProductCategoryCounts::class, 'center_id');
    }

    /**
     * Get the production errors for this center.
     */
    public function productionErrors(): HasMany
    {
        return $this->hasMany(ProductionError::class, 'center_id');
    }

    /**
     * Get the manager of this center.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
} 
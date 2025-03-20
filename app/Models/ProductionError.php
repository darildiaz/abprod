<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionError extends Model
{
    use HasFactory;

    // Cambiar el nombre de la tabla para que coincida con el real
    protected $table = 'error_orders';

    protected $fillable = [
        'date',
        'center_id',
        'error_type',
        'quantity',
        'description',
        'reported_by',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Get the center that this error belongs to.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(ProductionCenter::class, 'center_id');
    }

    /**
     * Get the user who reported this error.
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
} 
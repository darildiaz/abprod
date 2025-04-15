<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'question_id',
        'answer'
    ];

    protected $attributes = [
        'answer' => '',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

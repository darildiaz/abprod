<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(QuestionCategory::class);
    }
    public function answers()
    {
        return $this->hasMany(OrderQuestionAnswer::class);
    }

}

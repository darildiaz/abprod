<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassCenter extends Model
{
    use HasFactory;
    public function classification()
    {
        return $this->belongsTo(QuestionCategory::class,'category_id');
    }
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}

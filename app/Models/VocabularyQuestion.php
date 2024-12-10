<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VocabularyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question', 
        'options', 
        'correct_answer', 
        'difficulty_level', 
        'type',
        'isanswered'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
    ];
}

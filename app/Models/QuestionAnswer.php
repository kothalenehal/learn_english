<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    // Specify the table name if it's not the plural of the model name
    protected $table = 'question_answers';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'question_id',
        'answer',
        'isanswered'
    ];

    // Relationships (optional but recommended)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vocabularyQuestion()
    {
        return $this->belongsTo(VocabularyQuestion::class, 'question_id');
    }
}
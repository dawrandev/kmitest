<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model
{
    protected $fillable =
    [
        'question_id',
        'language_id',
        'text',
        'image'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

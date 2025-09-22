<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerTranslation extends Model
{
    protected $fillable =
    [
        'answer_id',
        'language_id',
        'text',
    ];

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

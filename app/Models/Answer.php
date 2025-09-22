<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'question_id',
        'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function translations()
    {
        return $this->hasMany(AnswerTranslation::class);
    }

    public function testSessions()
    {
        return $this->belongsToMany(TestSession::class);
    }
}

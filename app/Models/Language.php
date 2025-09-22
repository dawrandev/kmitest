<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code'
    ];

    public function questions()
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function answers()
    {
        return $this->hasMany(AnswerTranslation::class);
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }
}

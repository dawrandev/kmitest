<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        // 
    ];

    public function translations()
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function testSessions()
    {
        return $this->belongsToMany(TestSession::class);
    }
}

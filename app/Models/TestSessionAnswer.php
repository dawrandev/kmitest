<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSessionAnswer extends Model
{
    protected $fillable =
    [
        'test_session_id',
        'question_id',
        'answer_id',
        'is_correct'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function testSession()
    {
        return $this->belongsTo(TestSession::class);
    }
}

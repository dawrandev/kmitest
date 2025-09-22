<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    protected $fillable =
    [
        'student_id',
        'language_id',
        'started_at',
        'finished_at',
        'total_questions',
        'correct_answers',
        'incorrect_answers',
        // 'question_ids',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function sessionAnswers()
    {
        return $this->hasMany(TestSessionAnswer::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'subject_id',
    ];

    public function translations()
    {
        return $this->hasMany(TopicTranslation::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

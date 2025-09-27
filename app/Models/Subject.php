<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'id'
    ];

    public function translations()
    {
        return $this->hasMany(SubjectTranslation::class);
    }
}

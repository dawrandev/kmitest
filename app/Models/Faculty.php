<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable =
    [
        'id'
    ];

    public function translations()
    {
        return $this->hasMany(FacultyTranslation::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}

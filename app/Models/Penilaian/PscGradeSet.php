<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGradeSet extends Model
{
    use HasFactory;

    public function pscGrade()
    {
        return $this->hasMany('App\Models\Penilaian\PscGrade','set_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','status_id');
    }
}

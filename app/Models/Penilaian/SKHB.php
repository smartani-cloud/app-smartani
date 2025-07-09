<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKHB extends Model
{
    use HasFactory;

    protected $table = "skhb";
    protected $guarded = [];

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiSKHB', 'skhb_id');
    }

    public function final()
    {
        return $this->hasMany('App\Models\Penilaian\SKHBFinal', 'skhb_id');
    }

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }
}

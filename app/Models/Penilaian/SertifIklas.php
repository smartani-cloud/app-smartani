<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifIklas extends Model
{
    use HasFactory;

    protected $table = "iklas_certificate";
    protected $guarded = [];

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiSertifIklas', 'iklas_certificate_id');
    }

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran', 'academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }
}

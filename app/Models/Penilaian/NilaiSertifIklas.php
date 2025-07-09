<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSertifIklas extends Model
{
    use HasFactory;

    protected $table = "iklas_certificate_score";
    protected $guarded = [];

    public function sertif()
    {
        return $this->belongsTo('App\Models\Penilaian\SertifIklas', 'iklas_certificate_id');
    }

    public function kompetensi()
    {
        return $this->belongsTo('App\Models\Penilaian\RefIklas', 'iklas_ref_id');
    }
}

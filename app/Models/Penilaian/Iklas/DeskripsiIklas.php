<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskripsiIklas extends Model
{
    use HasFactory;
    protected $table = "rkd_iklas_descs";
    protected $fillable = [
        'class_id',
        'iklas_curriculum_id',
		'employee_id',
		'is_merged',
		'desc'
    ];

    public function kelas()
    {
        return $this->belongsTo('App\Models\Kbm\Kelas', 'class_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KompetensiKategoriIklas', 'iklas_curriculum_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }
}

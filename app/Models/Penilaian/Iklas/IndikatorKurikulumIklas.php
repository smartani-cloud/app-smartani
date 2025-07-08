<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKurikulumIklas extends Model
{
    use HasFactory;
    protected $table = "tas_iklas_curriculum_indicator";
    protected $fillable = [
        'semester_id',
        'level_id',
        'iklas_curriculum_id',
		'number',
		'indicator_id',
		'employee_id'
    ];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }
    
    public function kurikulum()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KompetensiKategoriIklas', 'iklas_curriculum_id');
    }
	
    public function indicator()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\IndikatorIklas', 'indicator_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai', 'employee_id');
    }
}

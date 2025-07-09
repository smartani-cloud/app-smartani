<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsDesc extends Model
{
    use HasFactory;
    protected $table = "rkd_tps_descs";
    protected $fillable = [
        'semester_id',
        'level_id',
		'subject_id',
		'employee_id',
		'code',
		'desc'
    ];
    protected $dates = ['deleted_at'];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }
    
    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\NilaiSumatif', 'tps_desc_id');
    }
}

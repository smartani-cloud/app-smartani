<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KDSetting extends Model
{
    use HasFactory;

    protected $table = "report_kd";
    protected $fillable = [
        'semester_id',
        'level_id',
        'subject_id',
        'employee_id',
        'kd',
        'kd_type_id'
    ];

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

    public function tipe()
    {
        return $this->belongsTo('App\Models\Penilaian\KDType', 'kd_type_id');
    }

    public function scopePengetahuan($query){
        return $query->where('kd_type_id',1);
    }

    public function scopeKeterampilan($query){
        return $query->where('kd_type_id',2);
    }

    public function scopeTpFormatif($query){
        return $query->where('kd_type_id',3);
    }
}

<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;
    protected $table = "subject_schedule";
    protected $fillable = ['day','hour_start','hour_end','subject_id','level_id','class_id','schedule_id','teacher_id','semester_id','description'];

    public function level()
    {
        return $this->belongsTo('App\Models\Level','level_id');
    }

    public function kelas()
    {
        return $this->belongsTo('App\Models\Kbm\Kelas','class_id');
    }

    public function jam()
    {
        return $this->belongsTo('App\Models\Kbm\JamPelajaran','schedule_id');
    }

    public function mapel()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
    }

    public function guru()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','teacher_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

}

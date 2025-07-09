<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPraktekUsp extends Model
{
    use HasFactory;

    protected $table = "practice_usp_score";
    protected $fillable = [
        'student_id',
        'class_id',
        'semester_id',
        'subject_id',
        'score',
        'type'
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa','student_id');
    }

    public function kelas()
    {
        return $this->belongsTo('App\Models\Kbm\Kelas','class_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester','semester_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
    }
}

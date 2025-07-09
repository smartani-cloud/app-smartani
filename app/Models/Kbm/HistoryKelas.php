<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryKelas extends Model
{
    use HasFactory;
    protected $table = "tm_class_history";
    protected $fillable = ['class_id','semester_id','student_id','unit_id','level_id'];

    public function kelas()
    {
        return $this->belongsTo('App\Models\Kbm\Kelas','class_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester','semester_id');
    }

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa','student_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level','level_id');
    }
}

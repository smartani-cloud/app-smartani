<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokMataPelajaran extends Model
{
    use HasFactory;
    protected $table = "tref_group_subjects";
    protected $fillable = ['group_subject_name','unit_id', 'major_id'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function matapelajarans()
    {
        return $this->hasMany('App\Models\Kbm\MataPelajaran','group_subject_id');
    }

    public function jurusan()
    {
        return $this->belongsTo('App\Models\Jurusan','major_id');
    }
}

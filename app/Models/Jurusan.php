<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    
    protected $table = "tref_majors";

    public function kelas()
    {
        return $this->hasMany('App\Models\Kbm\Kelas','major_id');
    }

    public function kelompokmapel()
    {
        return $this->hasMany('App\Models\Kbm\KelompokMataPelajaran');
    }
}

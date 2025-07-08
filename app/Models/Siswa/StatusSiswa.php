<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusSiswa extends Model
{
    use HasFactory;

    protected $table = "tref_student_statuses";

    public function calonSiswa()
    {
        return $this->hasMany('App\Models\Siswa\CalonSiswa','student_status_id');
    }

    public function registerCounter()
    {
        return $this->hasMany('App\Models\Psb\RegisterCounter','student_status_id');
    }
}

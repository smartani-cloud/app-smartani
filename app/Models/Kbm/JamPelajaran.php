<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    use HasFactory;
    protected $table = "tm_schedules";
    protected $fillable = [
        'day',
        'hour_start',
        'hour_end',
        'description',
        'level_id',];

    public function jadwalpelajarans()
    {
        return $this->hasMany('App\Models\Kbm\JadwalPelajaran','schedule_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusEksistensi extends Model
{
    use HasFactory;

    protected $table = "tref_existence_status";

    public function statusBaru()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','join_badge_status_id');
    }
}

<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPresensi extends Model
{
    use HasFactory;

    protected $table = "tref_presence_status";

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\PresensiPelatihan','presence_status_id');
    }
}

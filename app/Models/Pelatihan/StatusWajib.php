<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusWajib extends Model
{
    use HasFactory;

    protected $table = "tref_mandatory_status";

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','mandatory_status_id');
    }
}

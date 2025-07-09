<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PegawaiJabatan extends Pivot
{
    use HasFactory;

    protected $table = "employee_position";

    public $incrementing = true;

    public function pegawaiUnit()
    {
        return $this->belongsTo('App\Models\Rekrutmen\PegawaiUnit','employee_unit_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
}

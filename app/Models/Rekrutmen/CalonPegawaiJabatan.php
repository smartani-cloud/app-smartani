<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CalonPegawaiJabatan extends Pivot
{
    use HasFactory;

    protected $table = "candidate_employee_position";

    public $incrementing = true;

    public function calonPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\CalonPegawai','candidate_employee_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\JabatanUnit','position_id');
    }
}

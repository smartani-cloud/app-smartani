<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CalonPegawaiUnit extends Pivot
{
    use HasFactory;

    protected $table = "candidate_employee_unit";

    public $incrementing = true;

    public function calonPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\CalonPegawai','candidate_employee_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }
}

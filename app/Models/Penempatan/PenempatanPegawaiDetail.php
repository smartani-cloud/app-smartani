<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenempatanPegawaiDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "placement_employee_detail";
    
    protected $dates = ['deleted_at'];

    public function penempatanPegawai()
    {
        return $this->belongsTo('App\Models\Penempatan\PenempatanPegawai','placement_employee_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function accJabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','acc_position_id');
    }

    public function accPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','acc_employee_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','acc_status_id');
    }
}

<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenempatanPegawai extends Model
{
    use HasFactory;
    
    protected $table = "placement_employee";

    public function tahunAjaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function kategori()
    {
        return $this->belongsTo('App\Models\Penempatan\KategoriPenempatan','placement_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusArsip','status_id');
    }

    public function arsip()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiArsip','placement_employee_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','placement_employee_id');
    }

    public function getShowAttribute(){
        if($this->status->status == 'aktif') return $this->detail()->orderBy('created_at','desc')->get();
        else return $this->arsip()->orderBy('created_at','desc')->get();
    }
}

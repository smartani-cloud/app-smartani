<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenempatanPegawaiArsip extends Model
{
    use HasFactory;
    
    protected $table = "placement_employee_archive";

    public function penempatanPegawai()
    {
        return $this->belongsTo('App\Models\Penempatan\PenempatanPegawai','placement_employee_id');
    }
}

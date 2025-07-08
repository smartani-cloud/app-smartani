<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPenempatan extends Model
{
    use HasFactory;

    protected $table = "tref_placement";

    public function penempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawai','placement_id');
    }
}

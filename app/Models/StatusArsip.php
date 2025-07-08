<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusArsip extends Model
{
    use HasFactory;

    protected $table = "tref_archive_status";

    public function penempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawai','status_id');
    }

    public function skbm()
    {
        return $this->hasMany('App\Models\Skbm\Skbm','status_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    
    protected $table = "tref_banks";

    protected $fillable = [
        'code',
        'name',
        'short_name'
    ];

    public function calonSiswas()
    {
        return $this->hasMany('App\Models\Siswa\CalonSiswa','bank_id');
    }
}

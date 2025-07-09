<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    use HasFactory;
    
    protected $table = "tref_religion";

    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa');
    }
}

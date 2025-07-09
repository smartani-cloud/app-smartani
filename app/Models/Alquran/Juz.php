<?php

namespace App\Models\Alquran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Juz extends Model
{
    use HasFactory;

    protected $table = "tref_juz";
    protected $fillable = ['juz'];

    public function rapor()
    {
        return $this->hasMany('App\Models\Penilaian\Surah', 'juz_id');
    }
}

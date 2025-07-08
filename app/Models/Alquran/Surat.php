<?php

namespace App\Models\Alquran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = "tref_surah";
    protected $fillable = ['surah'];

    public function rapor()
    {
        return $this->hasMany('App\Models\Penilaian\Surah', 'surah_id');
    }

    public function getSurahSuratPrefixAttribute()
    {
        return 'Surat '.$this->surah;
    }

    public function getSurahNumberPrefixAttribute()
    {
        return $this->id.'. '.$this->surah;
    }

    public function getSurahNumberSuratPrefixAttribute()
    {
        return $this->id.'. Surat '.$this->surah;
    }
}

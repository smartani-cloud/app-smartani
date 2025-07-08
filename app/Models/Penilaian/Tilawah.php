<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tilawah extends Model
{
    use HasFactory;
    protected $table = "report_tilawah";
    protected $guarded = [];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }

    public function nilaitilawah()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiTilawah', 'tilawah_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiTilawah', 'tilawah_id');
    }
}

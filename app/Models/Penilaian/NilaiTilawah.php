<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiTilawah extends Model
{
    use HasFactory;
    protected $table = "score_tilawah";
    protected $guarded = [];

    public function tilawah()
    {
        return $this->belongsTo('App\Models\Penilaian\Tilawah', 'tilawah_id');
    }

    public function kompetensi()
    {
        return $this->belongsTo('App\Models\Penilaian\TilawahType', 'tilawah_type_id');
    }
}

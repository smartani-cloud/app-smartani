<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TilawahType extends Model
{
    use HasFactory;
    protected $table = "tref_tilawah_type";
    protected $guarded = [];

    public function tilawah()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiTilawah', 'tilawah_type_id');
    }
}

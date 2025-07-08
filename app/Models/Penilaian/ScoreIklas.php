<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreIklas extends Model
{
    use HasFactory;
    protected $table = "score_iklas";
    protected $guarded = [];

    public function iklas()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiIklas', 'iklas_id');
    }

    public function kompetensi()
    {
        return $this->belongsTo('App\Models\Penilaian\RefIklas', 'iklas_ref_id');
    }

    public function rpd()
    {
        return $this->hasMany('App\Models\Penilaian\PredikatDeskripsi', 'predicate', 'predicate');
    }
}

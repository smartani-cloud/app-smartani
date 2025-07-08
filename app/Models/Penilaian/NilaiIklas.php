<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiIklas extends Model
{
    use HasFactory;
    protected $table = "report_iklas";
    protected $guarded = [];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Penilaian\ScoreIklas', 'iklas_id');
    }
}

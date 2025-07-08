<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    use HasFactory;
    protected $table = "report_memorize";
    protected $guarded = [];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiHafalan', 'report_memorize_id');
    }
}

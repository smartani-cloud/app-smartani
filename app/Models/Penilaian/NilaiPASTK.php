<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPASTK extends Model
{
    use HasFactory;

    protected $table = "report_final_kg_score";
    protected $guarded = [];

    public function deskripsi()
    {
        return $this->belongsTo('App\Models\Penilaian\DeskripsiIndikator', 'indicator_description_id');
    }

    public function indikator()
    {
        return $this->belongsTo('App\Models\Penilaian\IndikatorAspek', 'aspect_indicator_id');
    }

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\PasTK', 'report_final_kg_id');
    }
}

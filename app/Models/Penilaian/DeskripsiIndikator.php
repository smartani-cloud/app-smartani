<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskripsiIndikator extends Model
{
    use HasFactory;

    protected $table = "report_indicator_description";
    protected $guarded = [];

    public function indikator()
    {
        return $this->belongsTo('App\Models\Penilaian\IndikatorAspek', 'aspect_indicator_id');
    }
}

<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorIklasDetail extends Model
{
    use HasFactory;

    protected $table = "report_iklas_indicator_detail";
    protected $guarded = [];

    public function ref()
    {
        return $this->belongsTo('App\Models\Penilaian\RefIklas', 'iklas_ref_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Penilaian\IndikatorIklas', 'iklas_indicator_id');
    }
}

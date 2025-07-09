<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PscIndicatorGrader extends Pivot
{
    use HasFactory;

    protected $table = "tm_psc_indicator_grader";
    protected $fillable = ['indicator_id','grader_id'];

    public function indikator()
    {
        return $this->belongsTo('App\Models\Psc\PscIndicator','indicator_id');
    }

    public function penilai()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','grader_id');
    }
}

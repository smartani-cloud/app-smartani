<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorIklas extends Model
{
    use HasFactory;

    protected $table = "report_iklas_indicator";
    protected $guarded = [];

    public function kelas()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorIklasDetail', 'iklas_indicator_id');
    }
}

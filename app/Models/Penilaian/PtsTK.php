<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PtsTK extends Model
{
    use HasFactory;

    protected $table = "report_mid_kg";
    protected $guarded = [];

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiPTSTK', 'report_mid_kg_id');
    }
}

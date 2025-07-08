<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorAspek extends Model
{
    use HasFactory;

    protected $table = "report_aspect_indicator";
    protected $guarded = [];

    public function aspek()
    {
        return $this->belongsTo('App\Models\Penilaian\AspekPerkembangan', 'development_aspect_id');
    }

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiPASTK', 'aspect_indicator_id');
    }

    public function scopeAktif($query){
    	$query->where('is_deleted',0);
    }
}

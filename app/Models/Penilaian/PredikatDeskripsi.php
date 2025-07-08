<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredikatDeskripsi extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "report_predicate_desc";

    public function RpdType()
    {
        return $this->belongsTo('App\Models\Penilaian\RpdType', 'rpd_type_id');
    }
    
    public function descriptions()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\Deskripsi', 'rpd_id');
    }
}

<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkuAspectUnit extends Model
{
    use HasFactory;

    protected $table = "tm_iku_aspect_unit";
    protected $fillable = ['number','academic_year_id','iku_aspect_id','unit_id'];
    
    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function aspek()
    {
        return $this->belongsTo('App\Models\Iku\IkuAspect','iku_aspect_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function indikator()
    {
        return $this->hasMany('App\Models\Iku\IkuIndicator','iku_aspect_unit_id');
    }
}

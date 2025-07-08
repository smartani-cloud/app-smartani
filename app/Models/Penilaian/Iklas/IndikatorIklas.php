<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorIklas extends Model
{
    use HasFactory;
    protected $table = "tm_iklas_indicators";
    protected $fillable = [
        'unit_id',
		'indicator'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }
    
    public function curricula()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\IndikatorKurikulumIklas', 'indicator_id');
    }
}

<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KompetensiIklas extends Model
{
    use HasFactory;
    protected $table = "tm_iklas_competencies";
    protected $fillable = [
        'unit_id',
		'name'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }
    
    public function categories()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\KompetensiKategoriIklas', 'competence_id');
    }
    
    public function predicates()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\NilaiIklas', 'competence_id');
    }
}

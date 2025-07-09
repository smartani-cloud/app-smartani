<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KompetensiKategoriIklas extends Model
{
    use HasFactory;
    protected $table = "tas_iklas_competence_category";
    protected $fillable = [
        'semester_id',
        'unit_id',
		'sort_order',
		'number',
		'category_id',
		'competence_id',
		'employee_id'
    ];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }
    
    public function category()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KategoriIklas', 'category_id');
    }
	
    public function competence()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KompetensiIklas', 'competence_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai', 'employee_id');
    }
    
    public function descs()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\DeskripsiIklas', 'iklas_curriculum_id');
    }

    public function getCompetenceNumberAttribute()
    {
        return $this->category->number.'.'.$this->number;
    }
}

<?php

namespace App\Models\Penilaian\Tk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectiveElement extends Model
{
    use HasFactory;
    protected $table = "rkd_objective_element";
    protected $fillable = [
        'semester_id',
        'level_id',
		'sort_order',
		'number',
		'objective_id',
		'element_id',
		'employee_id'
    ];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }
    
    public function element()
    {
        return $this->belongsTo('App\Models\Penilaian\AspekPerkembangan', 'element_id');
    }
	
    public function objective()
    {
        return $this->belongsTo('App\Models\Penilaian\Tk\Objective', 'objective_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai', 'employee_id');
    }
}

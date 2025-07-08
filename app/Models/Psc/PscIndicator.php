<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PscIndicator extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tm_psc_indicators";
    protected $fillable = [
    	'name',
    	'level',
    	'parent_id',
    	'percentage',
    	'is_fillable',
        'is_static',
        'employee_id',
        'position_id'
    ];
    protected $dates = ['deleted_at'];

    public function parent()
    {
        return $this->belongsTo('App\Models\Psc\PscIndicator','parent_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function childs()
    {
        return $this->hasMany('App\Models\Psc\PscIndicator','parent_id');
    }

    public function target()
    {
        return $this->hasMany('App\Models\Psc\PscIndicatorPosition','indicator_id');
    }

    public function penilai()
    {
        return $this->belongsToMany('App\Models\Penempatan\Jabatan','tm_psc_indicator_grader','indicator_id','grader_id')->withTimestamps();
    }

    public function nilaiDetail()
    {
        return $this->hasMany('App\Models\Psc\PscScoreIndicator','indicator_id');
    }

    public function scopeFillable($query){
        return $query->where('is_fillable',1);
    }

    public function scopeStatic($query){
        return $query->where('is_static',1);
    }

    public function scopeNonstatic($query){
        return $query->where('is_static',0);
    }
}

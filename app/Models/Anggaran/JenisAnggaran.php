<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAnggaran extends Model
{
    use HasFactory;

    protected $table = "budgeting_type";
    protected $fillable = ['name','link','ref_number','is_academic_year'];

    public function anggaran()
    {
        return $this->hasMany('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_type_id');
    }

    public function bbk()
    {
        return $this->hasMany('App\Models\Bbk\Bbk','budgeting_type_id');
    }

    public function getIsKsoAttribute()
    {
    	if(explode(' ',$this->name)[0] == 'APB-KSO') return true;
    	else return false;
    }
}

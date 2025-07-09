<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaRoleMapping extends Model
{
    use HasFactory;

    protected $table = "tref_pa_role_mapping";
    protected $fillable = ['name','desc'];

    public function ikuAspek()
    {
        return $this->hasMany('App\Models\Iku\IkuAspectPosition','pa_role_mapping_id');
    }

    public function psc()
    {
        return $this->hasMany('App\Models\Psc\PscRoleMapping','pa_role_mapping_id');
    }

    public function scopePsc($query){
        return $query->where('id','<=',3);
    }
}

<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkuAspectPosition extends Model
{
    use HasFactory;

    protected $table = "tm_iku_aspect_position";
    protected $fillable = [
    	'iku_aspect_id',
    	'pa_role_mapping_id',
    	'position_id'
    ];

    public function aspek()
    {
        return $this->belongsTo('App\Models\Iku\IkuAspect','iku_aspect_id');
    }

    public function peran()
    {
        return $this->belongsTo('App\Models\Psc\PaRoleMapping','pa_role_mapping_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
}

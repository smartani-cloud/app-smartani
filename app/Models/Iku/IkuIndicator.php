<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IkuIndicator extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tm_iku_indicators";
    protected $fillable = [
    	'iku_aspect_unit_id',
    	'name',
    	'object',
    	'mt',
    	'target',
    	'employee_id',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time'
    ];
    protected $dates = ['deleted_at'];

    public function aspek()
    {
        return $this->belongsTo('App\Models\Iku\IkuAspectUnit','iku_aspect_unit_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accDirekturStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }

    public function nilai()
    {
        return $this->hasMany('App\Models\Iku\IkuAchievementDetail','indicator_id');
    }
}

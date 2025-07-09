<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkuAchievementDetail extends Model
{
    use HasFactory;

    protected $table = "iku_achievement_detail";
    protected $fillable = [
    	'iku_achievement_id',
    	'indicator_id',
    	'is_achieved',
    	'attachment',
    	'link',
    	'note',
    	'employee_id',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time'
    ];

    public function iku()
    {
        return $this->belongsTo('App\Models\Iku\IkuAchievement','iku_achievement_id');
    }

    public function indikator()
    {
        return $this->belongsTo('App\Models\Iku\IkuIndicator','indicator_id');
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
}

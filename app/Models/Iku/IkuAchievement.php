<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkuAchievement extends Model
{
    use HasFactory;

    protected $table = "iku_achievement";
    protected $fillable = [
    	'iku_category_id',
    	'academic_year_id',
    	'unit_id',
    	'status_id',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time'
    ];

    public function kategori()
    {
        return $this->belongsTo('App\Models\Iku\IkuCategory','iku_category_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','active_status_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accDirekturStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Iku\IkuAchievementDetail','iku_achievement_id');
    }
}

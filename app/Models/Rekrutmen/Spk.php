<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Spk extends Model
{
    use HasFactory;

    protected $table = "tm_work_agreement";

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','status_id');
    }

    public function getPeriodIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->period_start)->translatedFormat('j F Y').' s.d. '.Carbon::parse($this->period_end)->translatedFormat('j F Y');
    }

    public function getRemainingPeriodAttribute()
    {
        $period_end = Carbon::parse($this->period_end);
        $now = Carbon::parse(Carbon::now('Asia/Jakarta')->format('Y-m-d'));
        $date = $period_end->diffInDays($now);

        return $period_end->lessThan($now) ? 'Habis' : $date.' hari';
    }

    public function getEmployeeStatusAcronymAttribute()
    {
        $words = explode(" ", $this->employee_status);
        $acronym = "";

        foreach ($words as $w) {
          $acronym .= $w[0];
        }

        return $acronym;
    }
    
    public function scopeAktif($query){
        return $query->where('status_id',1);
    }
}

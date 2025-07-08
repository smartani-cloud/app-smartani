<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = "training";

    public function narasumber()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','speaker_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester','semester_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Pelatihan\StatusWajib','mandatory_status_id');
    }

    public function penyelenggara()
    {
        return $this->belongsTo('App\Models\Unit','organizer_id');
    }

    public function accEdukasi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','education_acc_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','education_acc_status_id');
    }

    public function sasaran()
    {
        return $this->hasMany('App\Models\Pelatihan\SasaranPelatihan','training_id');
    }

    public function presensi()
    {
        return $this->hasMany('App\Models\Pelatihan\PresensiPelatihan','training_id');
    }

    public function getOrganizerAttribute(){
        $organizer = null;
        if($this->penyelenggara) $organizer = $this->penyelenggara->islamic_name ? $this->penyelenggara->islamic_name : $this->penyelenggara->name;
        return $organizer;
    }

    public function getSpeakerAttribute(){
        $name = null;
        if($this->speaker_name) $name = $this->speaker_name;
        elseif($this->narasumber) $name = $this->narasumber->name;
        return $name;
    }

    public function getDateFullIdAttribute(){
        Date::setLocale('id');
        return $this->date ? Date::parse($this->date)->format('l, j F Y') : null;
    }
    
    public function scopeAktif($query){
        return $query->where('active_status_id',1);
    }

    public function scopeSelesai($query){
        return $query->where('active_status_id',2);
    }
}

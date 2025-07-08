<?php

namespace App\Models\Ppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Ppa extends Model
{
    use HasFactory;

    protected $table = "ppa";
    protected $fillable = [
    	'type_id',
		'lppa_id',
    	'date',
        'year',
        'academic_year_id',
        'budgeting_budgeting_type_id',
    	'number',
    	'total_value',
    	'employee_id',
        'pa_acc_id',
        'pa_acc_status_id',
        'pa_acc_time',
    	'finance_acc_id',
    	'finance_acc_status_id',
    	'finance_acc_time',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time',
        'letris_acc_id',
        'letris_acc_status_id',
        'letris_acc_time'
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Ppa\PpaType','type_id');
    }

    public function lppaRef()
    {
        return $this->belongsTo('App\Models\Lppa\Lppa','lppa_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function jenisAnggaranAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_budgeting_type_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function accPa()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','pa_acc_id');
    }

    public function accPaStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','pa_acc_status_id');
    }

    public function accKeuangan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','finance_acc_id');
    }

    public function accKeuanganStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','finance_acc_status_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accDirekturStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }

    public function accLetris()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','letris_acc_id');
    }

    public function accLetrisStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','letris_acc_status_id');
    }

    public function eksklusi()
    {
        return $this->hasOne('App\Models\Ppa\PpaExclude','ppa_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Ppa\PpaDetail','ppa_id');
    }

    public function proposals()
    {
        return $this->hasMany('App\Models\Ppa\PpaProposal','ppa_id');
    }

    public function bbk()
    {
        return $this->hasOne('App\Models\Bbk\BbkDetail','ppa_id');
    }

    public function lppa()
    {
        return $this->hasOne('App\Models\Lppa\Lppa','ppa_id');
    }

    public function getFirstNumberAttribute(){
        return $this->number ? (int)explode('/',$this->number)[0] : null;
    }

    public function getNumberAsNameAttribute(){
        return $this->number ? str_replace('/','-',$this->number) : null;
    }

    public function getNumberOnlyAttribute(){
        return $this->number ? implode('-',explode('/',$this->number,-1)) : null;
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getTotalValueWithSeparatorAttribute()
    {
        return number_format($this->total_value, 0, ',', '.');
    }

    public function scopeNormal($query)
    {
        return $query->where('type_id',1);
    }

    public function scopeProposal($query)
    {
        return $query->where('type_id',2);
    }

    public function scopeDraft($query)
    {
        return $query->where('is_draft',1);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('is_draft',0);
    }
}

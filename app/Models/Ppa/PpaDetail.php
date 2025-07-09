<?php

namespace App\Models\Ppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpaDetail extends Model
{
    use HasFactory;

    protected $table = "ppa_detail";
    protected $fillable = [
    	'ppa_id',
    	'account_id',
    	'note',
    	'value',
        'value_pa',
        'value_fam',
        'value_director',
        'value_president',
        'value_letris',
    	'employee_id',
    	'edited_employee_id',
    	'edited_status_id',
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

    public function ppa()
    {
        return $this->belongsTo('App\Models\Ppa\Ppa','ppa_id');
    }

    public function akun()
    {
        return $this->belongsTo('App\Models\Anggaran\Akun','account_id')->withTrashed();
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function editPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','edited_employee_id');
    }

    public function editStatus()
    {
        return $this->belongsTo('App\Models\StatusAktif','edited_status_id');
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

    public function proposals()
    {
        return $this->hasMany('App\Models\Ppa\PpaProposal','ppa_detail_id');
    }

    public function lppaDetail()
    {
        return $this->hasOne('App\Models\Lppa\LppaDetail','ppa_detail_id');
    }

    public function getNoteAttribute(){
        if($this->ppa->type_id == 2){
            if($this->proposals()->count() > 0){
                $note = '';
                // Use detail descriptions
                // $lastId = $this->proposals()->select('id')->orderBy('id','DESC')->first()->id;
                // foreach($this->proposals as $p){
                //     $note .= implode(', ',$p->details->pluck('desc')->toArray());
                //     if($lastId != $p->id) $note .= ', ';
                // }
                // Use titles
                $note .= implode(', ',$this->proposals()->select('title')->get()->pluck('title')->toArray());
                return $note;
            }
            else return '-';
        }
        else return $this->attributes['note'];
    }

    public function getValueWithSeparatorAttribute()
    {
        return number_format($this->value, 0, ',', '.');
    }

    public function getValuePaWithSeparatorAttribute()
    {
        return number_format($this->value_pa, 0, ',', '.');
    }

    public function getValueFamWithSeparatorAttribute()
    {
        return number_format($this->value_fam, 0, ',', '.');
    }

    public function getValueDirectorWithSeparatorAttribute()
    {
        return number_format($this->value_director, 0, ',', '.');
    }

    public function getValuePresidentWithSeparatorAttribute()
    {
        return number_format($this->value_president, 0, ',', '.');
    }

    public function getValueLetrisWithSeparatorAttribute()
    {
        return number_format($this->value_letris, 0, ',', '.');
    }
}

<?php

namespace App\Models\Ppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class PpaProposal extends Model
{
    use HasFactory;

    protected $table = "ppa_proposal";
    protected $fillable = [
        'ppa_detail_id',
        'date',
        'year',
        'academic_year_id',
        'title',
        'desc',
        'total_value',
        'employee_id',
        'unit_id',
        'position_id',
        'budgeting_id',
        'declined_at'
    ];

    public function ppa()
    {
        return $this->belongsTo('App\Models\Ppa\PpaDetail','ppa_detail_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function anggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\Anggaran','budgeting_id');
    }
    
    
    public function details()
    {
        return $this->hasMany('App\Models\Ppa\PpaProposalDetail','proposal_id');
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return $this->date ? Date::parse($this->date)->format('j F Y') : null;
    }

    public function getTotalValueWithSeparatorAttribute()
    {
        return number_format($this->total_value, 0, ',', '.');
    }

    public function getTotalValueOriAttribute()
    {
        $sum = 0;
        foreach($this->details()->withTrashed()->get() as $d){
            $sum += $d->price_ori*$d->quantity_ori;
        }
        return $sum;
    }

    public function getTotalValueOriWithSeparatorAttribute()
    {
        return number_format($this->totalValueOri, 0, ',', '.');
    }
}

<?php

namespace App\Models\Ppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PpaProposalDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "ppa_proposal_detail";
    protected $fillable = [
    	'proposal_id',
        'desc',
    	'price',
        'quantity',
        'ppa_detail_id',
        'value',
        'price_ori',
        'quantity_ori',
        'price_pa',
        'quantity_pa',
        'price_fam',
        'quantity_fam',
    	'employee_id',
    	'edited_employee_id',
    	'edited_status_id'
    ];
    protected $dates = ['deleted_at'];

    public function proposal()
    {
        return $this->belongsTo('App\Models\Ppa\PpaProposal','proposal_id');
    }

    public function detail()
    {
        return $this->belongsTo('App\Models\Ppa\PpaDetail','ppa_detail_id');
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

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function getValueWithSeparatorAttribute()
    {
        return number_format($this->value, 0, ',', '.');
    }

    public function getPriceOriWithSeparatorAttribute()
    {
        return number_format($this->price_ori, 0, ',', '.');
    }

    public function getQuantityOriWithSeparatorAttribute()
    {
        return number_format($this->quantity_ori, 0, ',', '.');
    }

    public function getValueOriAttribute()
    {
        return $this->price_ori*$this->quantity_ori;
    }

    public function getValueOriWithSeparatorAttribute()
    {
        return number_format($this->valueOri, 0, ',', '.');
    }

    public function getPricePaWithSeparatorAttribute()
    {
        return number_format($this->price_pa, 0, ',', '.');
    }

    public function getQuantityPaWithSeparatorAttribute()
    {
        return number_format($this->quantity_pa, 0, ',', '.');
    }

    public function getValuePaAttribute()
    {
        return $this->price_pa*$this->quantity_pa;
    }

    public function getValuePaWithSeparatorAttribute()
    {
        return number_format($this->valuePa, 0, ',', '.');
    }

    public function getPriceFamWithSeparatorAttribute()
    {
        return number_format($this->price_fam, 0, ',', '.');
    }

    public function getQuantityFamWithSeparatorAttribute()
    {
        return number_format($this->quantity_fam, 0, ',', '.');
    }

    public function getValueFamAttribute()
    {
        return $this->price_fam*$this->quantity_fam;
    }

    public function getValueFamWithSeparatorAttribute()
    {
        return number_format($this->valueFam, 0, ',', '.');
    }
}

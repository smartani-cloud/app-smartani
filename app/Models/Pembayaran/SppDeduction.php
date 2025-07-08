<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppDeduction extends Model
{
    use HasFactory;
    protected $table = "tref_spp_deduction";
    protected $fillable = ['name','employee_status_id','percentage','nominal'];
	
	public function statusPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPegawai','employee_status_id');
    }

    public function getNameAttribute(){
        return $this->statusPegawai ? 'Anak '.$this->statusPegawai->status : $this->attributes['name'];
    }

    public function getPercentageWithSymbolAttribute()
    {
        return ($this->percentage ? $this->percentage : '0').'%';
    }

    public function getNameWithPercentageAttribute()
    {
        return $this->name.' - '.$this->percentageWithSymbol;
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getNameWithNominalAttribute()
    {
        return $this->name.' - '.$this->nominalWithSeparator;
    }

    public function getCategoryAttribute()
    {
        return $this->statusPegawai ? 'civitas' : 'umum';
    }

    public function getIsPercentageAttribute()
    {
        return $this->percentage ? true : false;
    }
}

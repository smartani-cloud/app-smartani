<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsDeduction extends Model
{
    use HasFactory;
    protected $table = "tref_bms_deduction";
    protected $fillable = ['name','percentage','nominal'];

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

    public function getIsPercentageAttribute()
    {
        return $this->percentage ? true : false;
    }
}

<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsNominal extends Model
{
    use HasFactory;
    protected $table = "tref_bms_nominal";
    protected $fillable = [
        'unit_id',
        'bms_type_id',
        'bms_nominal',
    ];
    
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }
    
    public function tipe()
    {
        return $this->belongsTo('App\Models\Pembayaran\TipeBms','bms_type_id');
    }
    
    public function getBmsNominalWithSeparatorAttribute()
    {
        return number_format($this->bms_nominal, 0, ',', '.');
    }

    public function getNameAttribute()
    {
        return 'Nominal '.$this->tipe->bms_type.' '.$this->unit->name;
    }
}

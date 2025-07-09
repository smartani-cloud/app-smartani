<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppNominal extends Model
{
    use HasFactory;
    protected $table = "tref_spp_nominal";
    protected $fillable = [
        'unit_id',
        'spp_nominal',
    ];
    
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }
    
    public function getSppNominalWithSeparatorAttribute()
    {
        return number_format($this->spp_nominal, 0, ',', '.');
    }

    public function getNameAttribute()
    {
        return 'Nominal '.$this->unit->name;
    }
}

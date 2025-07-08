<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeBms extends Model
{
    use HasFactory;
    protected $table = "tref_bms_type";
    protected $fillable = ['bms_type'];
    
    public function bmsSiswa()
    {
        return $this->hasMany('App\Models\Pembayaran\BMS','bms_type_id');
    }
    
    public function bmsCalonSiswa()
    {
        return $this->hasMany('App\Models\Pembayaran\BmsCalonSiswa','bms_type_id');
    }
    
    public function bmsNominal()
    {
        return $this->hasMany('App\Models\Pembayaran\BmsNominal','bms_type_id');
    }   

    public function nominal()
    {
        return $this->hasMany('App\Models\Pembayaran\BmsNominal','bms_type_id');
    }

    public function getBmsTypeWoNumberAttribute()
    {
        return preg_replace('/[0-9]+/', '', str_replace(' ', '', $this->bms_type));
    }
}

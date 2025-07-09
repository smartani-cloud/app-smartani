<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = "tref_region";
    protected $protected = ['code','name'];

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','region_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','region_id');
    }

    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa','region_id');
    }

    public function unit()
    {
        return $this->hasMany('App\Models\Unit','region_id');
    }

    public function getCekTingkatAttribute(){
        if(strlen($this->code) == 2) return "provinsi";
        elseif(strlen($this->code) == 5) return "kabupaten";
        elseif(strlen($this->code) == 8) return "kecamatan";
        elseif(strlen($this->code) == 13) return "desa";
        else return "undefined";
    }

    public function scopeProvinsiName($query){
        if(strlen($this->code) >= 2){
            $provinsi = $query->where('code',substr($this->code,0,2))->first();
            if($provinsi->code == '31')
                return 'DKI Jakarta';
            else return ucwords(strtolower($provinsi->name));
        }
        else return "-";
    }

    public function scopeKabupatenName($query){
        if(strlen($this->code) >= 5){
            $kabupaten = $query->where('code',substr($this->code,0,5))->first();
            return ucwords(strtolower($kabupaten->name));
        }
        else return "-";
    }

    public function scopeKecamatanName($query){
        if(strlen($this->code) >= 8){
            $kecamatan = $query->where('code',substr($this->code,0,8))->first();
            return $kecamatan->name;
        }
        else return "-";
    }

    public function scopeDesaName($query){
        if(strlen($this->code) == 13){
            $desa = $query->where('code',substr($this->code,0,13))->first();
            return $desa->name;
        }
        else return "-";
    }

    public function scopeProvinsi($query){
    	return $query->whereRaw('LENGTH(code) = 2');
    }

    public function scopeKabupaten($query){
    	return $query->whereRaw('LENGTH(code) = 5');
    }

    public function scopeKecamatan($query){
    	return $query->whereRaw('LENGTH(code) = 8');
    }

    public function scopeDesa($query){
    	return $query->whereRaw('LENGTH(code) = 13');
    }

    public function scopeKabupatenFilter($query,$code){
        return $query->where('code','LIKE',substr($code,0,2).'%')->whereRaw('LENGTH(code) = 5');
    }

    public function scopeKecamatanFilter($query,$code){
        return $query->where('code','LIKE',substr($code,0,5).'%')->whereRaw('LENGTH(code) = 8');
    }

    public function scopeDesaFilter($query,$code){
        return $query->where('code','LIKE',substr($code,0,8).'%')->whereRaw('LENGTH(code) = 13');
    }

}

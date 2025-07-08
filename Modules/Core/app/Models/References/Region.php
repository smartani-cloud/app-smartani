<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Region extends Model
{
    use HasFactory;

    protected $table = "tref_region";

    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','region_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','region_id');
    }

    public function students()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Student','region_id') : null;
    }

    public function units()
    {
        return $this->hasMany('Modules\Core\Models\Unit','region_id');
    }

    public function getCekTingkatAttribute(){
        if(strlen($this->code) == 2) return "provinsi";
        elseif(strlen($this->code) == 5) return "kabupaten";
        elseif(strlen($this->code) == 8) return "kecamatan";
        elseif(strlen($this->code) == 13) return "desa";
        else return "undefined";
    }

    public function getProvinceCodeAttribute(){
        return strlen($this->code) >= 2 ? substr($this->code,0,2) : null;
    }

    public function getCityCodeAttribute(){
        return strlen($this->code) >= 5 ? substr($this->code,0,5) : null;
    }

    public function getSubdistrictCodeAttribute(){
        return strlen($this->code) >= 8 ? substr($this->code,0,8) : null;
    }

    public function getVillageCodeAttribute(){
        return strlen($this->code) == 13 ? $this->code : null;
    }

    public function getProvinceNameAttribute(){
        if(strlen($this->code) >= 2){
            $province = self::select('code','name')->where('code',substr($this->code,0,2))->first();
            if($province){
                if($province->code == '31')
                    return 'DKI Jakarta';
                else return ucwords(strtolower($province->name));
            }
            else return null;
        }
        else return null;
    }

    public function getCityNameAttribute(){
        if(strlen($this->code) >= 5){
            $city = self::select('code','name')->where('code',substr($this->code,0,5))->first();
            if($city){
                return ucwords(strtolower($city->name));
            }
            else return null;
        }
        else return null;
    }

    public function getSubdistrictNameAttribute(){
        if(strlen($this->code) >= 8){
            $subdistrict = self::select('code','name')->where('code',substr($this->code,0,8))->first();
            if($subdistrict){
                return ucwords(strtolower($subdistrict->name));
            }
            else return null;
        }
        else return null;
    }

    public function getVillageNameAttribute(){
        if(strlen($this->code) == 13){
            $village = self::select('code','name')->where('code',substr($this->code,0,13))->first();
            if($village){
                return ucwords(strtolower($village->name));
            }
            else return null;
        }
        else return null;
    }

    public function scopeProvinces($query){
    	return $query->whereRaw('LENGTH(code) = 2');
    }

    public function scopeCities($query){
    	return $query->whereRaw('LENGTH(code) = 5');
    }

    public function scopeSubdistricts($query){
    	return $query->whereRaw('LENGTH(code) = 8');
    }

    public function scopeVillages($query){
    	return $query->whereRaw('LENGTH(code) = 13');
    }

    public function scopeCitiesByCode($query,$code){
        return $query->cities()->where('code','LIKE',substr($code,0,2).'%');
    }

    public function scopeSubdistrictsByCode($query,$code){
        return $query->subdistricts()->where('code','LIKE',substr($code,0,5).'%');
    }

    public function scopeVillagesByCode($query,$code){
        return $query->villages()->where('code','LIKE',substr($code,0,8).'%');
    }
}

<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = "tref_position";

    public function unit()
    {
        return $this->belongsToMany('App\Models\Unit','tm_position_unit','position_id', 'unit_id')->withTimestamps();
    }

    public function pegawaiUnit()
    {
        return $this->belongsToMany('App\Models\Rekrutmen\PegawaiUnit','employee_position','position_id', 'employee_unit_id')->withTimestamps();
    }

    public function kategoriPenempatan()
    {
        return $this->belongsTo('App\Models\Penempatan\KategoriPenempatan','placement_id');
    }

    public function kategori()
    {
        return $this->belongsTo('App\Models\Penempatan\KategoriJabatan','category_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','position_id');
    }

    public function penempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','position_id');
    }

    public function accPenempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','acc_position_id');
    }

    public function skbmDetail()
    {
        return $this->hasMany('App\Models\Skbm\SkbmDetail','position_id');
    }

    public function pscRoleMapping()
    {
        return $this->hasMany('App\Models\Psc\PscRoleMapping','target_position_id');
    }

    public function pscRoleTarget()
    {
        return $this->hasMany('App\Models\Psc\PscRoleMapping','position_id');
    }

    public function penilaiPsc()
    {
        return $this->belongsToMany('App\Models\Psc\PscIndicator','tm_psc_indicator_grader','grader_id','indicator_id')->withTimestamps();
    }

    public function ikuAspek()
    {
        return $this->hasMany('App\Models\Iku\IkuAspectPosition','position_id');
    }

    public function budgetUsers()
    {
        return $this->hasMany('App\Models\Anggaran\Anggaran','acc_position_id');
    }

    public function scopeGroup($query){
        if(strlen($this->code) > 3){
            return $query->where('code',substr($this->code,0,strlen($this->code)-3));
        }
        else return null;
    }

    public function scopeAktif($query){
        return $query->where('status_id',1);
    }

    public function scopeGuru($query){
        return $query->where('code','LIKE', '14.%')->where('name','!=','Wali Kelas');
    }

    public function scopePscRoleFilter($query,$role){
        return $query->pscRoleMapping()->select('position_id')->where('pa_role_mapping_id',$role)->with('jabatan');
    }

    // Custom behavior
    public function pscRoleMappingShow($role){
        return implode(', ',$this->pscRoleMapping()->select('position_id')->where('pa_role_mapping_id',$role)->with('jabatan')->get()->pluck('jabatan')->pluck('name')->toArray());
    }

    public function pscRoleMappingCheck($role){
        return $this->pscRoleMapping()->select('position_id')->where('pa_role_mapping_id',$role)->pluck('position_id')->toArray();
    }
}

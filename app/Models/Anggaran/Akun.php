<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Akun extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tm_accounts";
    protected $fillable = [
    	'code',
    	'name',
    	'is_fillable',
    	'is_static',
        'is_autodebit',
        'is_exclusive',
        'account_category_id',
        'sort_order'
    ];
    protected $dates = ['deleted_at'];

    public function kategori()
    {
        return $this->belongsTo('App\Models\Anggaran\KategoriAkun','account_category_id');
    }

    public function anggaran()
    {
        return $this->belongsToMany('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_account','account_id','budgeting_budgeting_type_id')->withTimestamps();
    }

    public function rkat()
    {
        return $this->hasMany('App\Models\Rkat\RkatDetail','account_id');
    }

    public function apby()
    {
        return $this->hasMany('App\Models\Apby\ApbyDetail','account_id');
    }

    public function ppa()
    {
        return $this->hasMany('App\Models\Ppa\PpaDetail','account_id');
    }

    public function getCodeAsNumberAttribute()
    {
        return str_replace('.','',$this->code);
    }

    public function getLevelAttribute()
    {
        return count(explode('.',$this->code));
    }

    public function getParentsCountAttribute()
    {
        $parentCount = count(explode('.',$this->code));
        return $parentCount-1;
    }

    public function getParentCodeAttribute()
    {
        $codeArray = explode('.',$this->code,-1);
        if(count($codeArray) > 1){
            return implode('.',$codeArray);
        }
        else null;
    }

    public function getCodeNameAttribute()
    {
        return str_replace('.','',$this->code).' '.$this->name;
    }
}

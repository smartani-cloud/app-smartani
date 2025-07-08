<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAnggaranAnggaran extends Model
{
    use HasFactory;

    protected $table = "budgeting_budgeting_type";
    protected $fillable = [
    	'number',
    	'budgeting_type_id',
    	'budgeting_id'
    ];

    public function jenis()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaran','budgeting_type_id');
    }

    public function anggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\Anggaran','budgeting_id');
    }

    public function akun()
    {
        return $this->belongsToMany('App\Models\Anggaran\Akun','budgeting_account','budgeting_budgeting_type_id','account_id')->withTimestamps();
    }

    public function tahuns()
    {
        return $this->hasMany('App\Models\Anggaran\JenisAnggaranAnggaranRiwayat','budgeting_budgeting_type_id');
    }

    public function rkat()
    {
        return $this->hasMany('App\Models\Rkat\Rkat','budgeting_budgeting_type_id');
    }

    public function apby()
    {
        return $this->hasMany('App\Models\Apby\Apby','budgeting_budgeting_type_id');
    }

    public function ppa()
    {
        return $this->hasMany('App\Models\Ppa\Ppa','budgeting_budgeting_type_id');
    }

    public function getNameAttribute(){
        return $this->jenis->name.' '.$this->anggaran->name;
    }
}

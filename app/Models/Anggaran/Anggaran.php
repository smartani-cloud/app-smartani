<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasFactory;

    protected $table = "budgeting";
    protected $fillable = [
    	'unit_id',
    	'position_id',
    	'name',
    	'acc_position_id',
    	'upbudgeting_id',
		'category_id'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }
    
    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
    
    public function accJabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','acc_position_id');
    }
    
    public function kategori()
    {
        return $this->belongsTo('App\Models\Anggaran\KategoriAnggaran','category_id');
    }

    public function childs()
    {
        return $this->hasMany('App\Models\Anggaran\Anggaran','upbudgeting_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Anggaran\Anggaran','upbudgeting_id');
    }

    public function jenisAnggaran()
    {
        return $this->hasMany('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_id');
    }

    public function proposals()
    {
        return $this->hasMany('App\Models\Ppa\PpaProposal','proposal_id');
    }

    public function getLinkAttribute(){
        return str_replace(' ','-',strtolower($this->name));
    }
}

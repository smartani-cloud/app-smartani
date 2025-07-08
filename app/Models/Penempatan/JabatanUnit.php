<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class JabatanUnit extends Pivot
{
    use HasFactory;

    protected $table = "tm_position_unit";

    public $incrementing = true;

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function sasaranPelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\SasaranPelatihan','position_id');
    }

    public function getNameAttribute(){
        return $this->jabatan->name.' '.$this->unit->name;
    }
}

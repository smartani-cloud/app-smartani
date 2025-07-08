<?php

namespace App\Models\Bbk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BbkDetail extends Model
{
    use HasFactory;

    protected $table = "bbk_detail";
    protected $fillable = [
    	'bbk_id',
    	'ppa_id',
    	'ppa_value',
    	'employee_id'
    ];

    public function bbk()
    {
        return $this->belongsTo('App\Models\Bbk\Bbk','bbk_id');
    }

    public function ppa()
    {
        return $this->belongsTo('App\Models\Ppa\Ppa','ppa_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function getPpaValueWithSeparatorAttribute()
    {
        return number_format($this->ppa_value, 0, ',', '.');
    }
}

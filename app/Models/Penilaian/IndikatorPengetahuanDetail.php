<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorPengetahuanDetail extends Model
{
    use HasFactory;

    protected $table = "report_knowledge_indicator_detail";
    protected $fillable = [
        'rki_id',
		'indicator',
        'employee_id'
    ];
    protected $guarded = [];
	
    public function indikatorPengetahuan()
    {
        return $this->belongsTo('App\Models\Penilaian\IndikatorPengetahuan', 'rki_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }
}

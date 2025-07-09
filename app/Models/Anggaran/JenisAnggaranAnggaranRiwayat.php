<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAnggaranAnggaranRiwayat extends Model
{
    use HasFactory;

    protected $table = "budgeting_budgeting_type_history";
    protected $fillable = [
        'year',
        'academic_year_id',
    	'budgeting_budgeting_type_id',
        'ppa_active'
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function jenisAnggaranAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_budgeting_type_id');
    }
    
    public function scopePpaActive($query)
    {
        return $query->where('ppa_active',1);
    }
    
    public function scopePpaInactive($query)
    {
        return $query->where('ppa_active',0);
    }
}

<?php

namespace App\Models\Pembayaran;

use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsTermin extends Model
{
    use HasFactory;
    protected $table = "tm_bms_termin";
    protected $fillable = [
        'bms_id',
        'academic_year_id',
        'is_student',
        'nominal',
        'remain',
    ];

    public function bms()
    {
        if($this->is_student == 0){
            return $this->belongsTo(BmsCalonSiswa::class,'bms_id');
        }
        return $this->belongsTo(BMS::class,'bms_id');
    }

    public function bmsCalon()
    {
        return $this->belongsTo(BmsCalonSiswa::class,'bms_id');
    }

    public function bmsSiswa()
    {
        return $this->belongsTo(BMS::class,'bms_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }
    
    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
    
    public function getRemainWithSeparatorAttribute()
    {
        return number_format($this->remain, 0, ',', '.');
    }

    public function getPaidAttribute()
    {
        return $this->nominal-$this->remain;
    }
    
    public function getPaidWithSeparatorAttribute()
    {
        return number_format($this->paid, 0, ',', '.');
    }

    public function getIndexNumberAttribute()
    {
        $index = null;
        $siswa = $this->is_student == 0 ? 'calon' : 'siswa';
        foreach($this->bms->termin()->select('id','academic_year_id')->{$siswa}()->with('tahunPelajaran:id,academic_year_start')->get()->sortBy('tahunPelajaran.academic_year_start') as $key => $t){
            if($this->id == $t->id) $index = $key+1;
        }
        return $index;
    }

    public function scopeYearActive($query){
        $tahunAktif = TahunAjaran::select('id')->aktif()->first();
        if($tahunAktif){
            return $query->where('academic_year_id',$tahunAktif->id);
        }
        else return $query;
    }

    public function scopeCalon($query){
        return $query->where('is_student',0);
    }

    public function scopeSiswa($query){
        return $query->where('is_student',1);
    }
}

<?php

namespace App\Models\Pembayaran;

use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BMS extends Model
{
    use HasFactory;
    protected $table = "tm_bms";
    protected $fillable = [
        'unit_id',
        'student_id',
        'register_nominal',
        'register_paid',
        'bms_nominal',
        'bms_paid',
        'bms_deduction',
        'bms_remain',
        'bms_type_id'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }
    
    public function tipe()
    {
        return $this->belongsTo('App\Models\Pembayaran\TipeBms','bms_type_id');
    }
    
    public function termin()
    {
        return $this->hasMany(BmsTermin::class,'bms_id')->where('is_student',1)->orderBy('academic_year_id','asc');
    }
    
    public function getBmsNominalWithSeparatorAttribute()
    {
        return number_format($this->bms_nominal, 0, ',', '.');
    }
    
    public function getBmsPaidWithSeparatorAttribute()
    {
        return number_format($this->bms_paid, 0, ',', '.');
    }
    
    public function getBmsDeductionWithSeparatorAttribute()
    {
        return number_format($this->bms_deduction, 0, ',', '.');
    }
    
    public function getBmsRemainWithSeparatorAttribute()
    {
        return number_format($this->bms_remain, 0, ',', '.');
    }
    
    public function getTotalBmsNominalCashWithSeparatorAttribute()
    {
        return number_format($this->bms_nominal+$this->bms_deduction, 0, ',', '.');
    }
    
    public function getBmsDeductionPeriodicAttribute()
    {
        if($this->isHaveTermins()){
            $key = $this->getPeriod();
            if($key && $key == 1) return $this->bms_deduction;
        }
        return 0;
    }
    
    public function getBmsDeductionPeriodicWithSeparatorAttribute()
    {
        return number_format($this->bmsDeductionPeriodic, 0, ',', '.');
    }
    
    public function getBmsNominalPeriodicAttribute()
    {
        if($this->isHaveTermins()){
            $key = $this->getPeriod();
            if($key){
                $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                $termin = $this->termin()->select('id','nominal','academic_year_id')->siswa()->where('academic_year_id',$tahunAktif->id)->first();
                $nominal = $termin->nominal;
                if($key == 1) $nominal += $this->register_nominal;
                return $nominal;
            }
        }
        return 0;
    }
    
    public function getBmsNominalPeriodicWithSeparatorAttribute()
    {
        return number_format($this->bmsNominalPeriodic, 0, ',', '.');
    }
    
    public function getBmsPaidPeriodicAttribute()
    {
        if($this->isHaveTermins()){
            $key = $this->getPeriod();
            if($key){
                $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                $termin = $this->termin()->select('id','nominal','academic_year_id')->siswa()->where('academic_year_id',$tahunAktif->id)->first();
                $paid = $termin->paid;
                if($key == 1) $paid += $this->register_paid;
                return $paid;
            }
        }
        return 0;
    }
    
    public function getBmsPaidPeriodicWithSeparatorAttribute()
    {
        return number_format($this->bmsPaidPeriodic, 0, ',', '.');
    }
    
    public function getBmsRemainPeriodicAttribute()
    {
        return $this->getBmsPeriodicValue('remain');
    }
    
    public function getBmsRemainPeriodicWithSeparatorAttribute()
    {
        return number_format($this->bmsRemainPeriodic, 0, ',', '.');
    }
    
    public function getTotalBmsNominalPeriodicAttribute()
    {
        return $this->bmsNominalPeriodic+$this->bmsDeductionPeriodic;
    }
    
    public function getTotalBmsNominalPeriodicWithSeparatorAttribute()
    {
        return number_format($this->totalBmsNominalPeriodic, 0, ',', '.');
    }
    
    public function getTerminActiveAttribute()
    {
        return $this->termin()->siswa()->yearActive()->first();
    }

    public function terminTahun()
    {
        $tahun_now = TahunAjaran::where('is_active',1)->first();
        return $this->hasMany(BmsTermin::class,'bms_id')->where('is_student',1);
    }

    public function isHaveTermins()
    {
        return $this->termin()->siswa()->count() > 0 ? true : false;
    }

    public function getPeriod($tahun = null)
    {
        $tahunAktif = $tahun ? TahunAjaran::select('id')->where('id',$tahun)->first() : TahunAjaran::select('id')->aktif()->first();
        if($tahunAktif && $this->isHaveTermins()){
            $termins = $this->termin()->select('id','academic_year_id')->siswa()->with('tahunPelajaran:id,academic_year_start')->get()->sortBy('tahunPelajaran.academic_year_start');
            $index = null;
            foreach($termins as $key => $t){
                if($tahunAktif->id == $t->academic_year_id) $index = $key+1;
            }
            return $index;
        }
        return null;
    }
    
    public function getBmsPeriodicValue($attribute)
    {
        if($attribute && $this->isHaveTermins()){
            $key = $this->getPeriod();
            if($key){
                $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                $termin = $this->termin()->select('id','nominal','academic_year_id')->siswa()->where('academic_year_id',$tahunAktif->id)->first();
                ${$attribute} = $termin->{$attribute};
                if($key == 1){
                    $registerVar = 'register_'.$attribute;
                    ${$attribute} += $this->{$registerVar};
                }
                return ${$attribute};
            }
        }
        return 0;
    }
}

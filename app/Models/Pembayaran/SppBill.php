<?php

namespace App\Models\Pembayaran;

use App\Models\Siswa\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class SppBill extends Model
{
    use HasFactory;
    protected $table = "tm_spp_bill";
    protected $fillable = [
        'spp_id',
        'unit_id',
        'level_id',
        'student_id',
        'month',
        'year',
        'spp_nominal',
        'deduction_nominal',
        'spp_paid',
        'status',
        'deduction_id'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function spp()
    {
        return $this->belongsTo('App\Models\Pembayaran\Spp', 'spp_id');
    }

    public function potongan()
    {
        return $this->belongsTo('App\Models\Pembayaran\SppDeduction', 'deduction_id');
    }
    
    public function getMonthIdAttribute()
    {
        Date::setLocale('id');
        return Date::createFromFormat('Y-m-d',$this->yearMonth.'-01')->format('F');
    }
    
    public function getYearMonthAttribute()
    {
        return $this->year.'-'.$this->month;
    }
    
    public function getMonthYearIdAttribute()
    {
        Date::setLocale('id');
        return Date::createFromFormat('Y-m',$this->yearMonth)->format('F Y');
    }
    
    public function getSppNominalWithSeparatorAttribute()
    {
        return number_format($this->spp_nominal, 0, ',', '.');
    }
    
    public function getDeductionNominalWithSeparatorAttribute()
    {
        return number_format($this->deduction_nominal, 0, ',', '.');
    }
    
    public function getSppPaidWithSeparatorAttribute()
    {
        return number_format($this->spp_paid, 0, ',', '.');
    }
    
    public function getSppRemainAttribute()
    {
        return $this->spp_nominal-$this->deduction_nominal-$this->spp_paid;
    }
    
    public function getSppRemainWithSeparatorAttribute()
    {
        return number_format($this->sppRemain, 0, ',', '.');
    }
    
    public function getTotalBillAttribute()
    {
        return $this->spp_nominal-($this->spp_paid+$this->deduction_nominal);
    }
    
    public function getTotalBillWithSeparatorAttribute()
    {
        return number_format($this->totalBill, 0, ',', '.');
    }
}

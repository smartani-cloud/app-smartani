<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    use HasFactory;
    protected $table = "tm_spp";
    protected $fillable = [
        'unit_id',
        'student_id',
        'saldo',
        'total',
        'deduction',
        'paid',
        'remain',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }

    public function bills()
    {
        return $this->hasMany('App\Models\Pembayaran\SppBill','spp_id');
    }

    public function getSaldoWithSeparatorAttribute()
    {
        return number_format($this->saldo, 0, ',', '.');
    }

    public function getTotalWithSeparatorAttribute()
    {
        return number_format($this->total, 0, ',', '.');
    }
    
    public function getDeductionWithSeparatorAttribute()
    {
        return number_format($this->deduction, 0, ',', '.');
    }
    
    public function getPaidWithSeparatorAttribute()
    {
        return number_format($this->paid, 0, ',', '.');
    }
    
    public function getRemainWithSeparatorAttribute()
    {
        return number_format($this->remain, 0, ',', '.');
    }
    
    public function getTotalBillAttribute()
    {
        return $this->total-($this->paid+$this->deduction);
    }
    
    public function getTotalBillWithSeparatorAttribute()
    {
        return number_format($this->totalBill, 0, ',', '.');
    }

    public function getThisMonthBillAttribute()
    {
        return $this->bills()->where([
            'month' => date('m'),
            'year' => date('Y'),
        ])->latest()->first();
    }

    public function getLastMonthBillAttribute()
    {
        return $this->bills()->where(function($q){
            $q->where('year','<',date('Y', strtotime("first day of previous month")))->orWhere(function($q){
                $q->where('year',date('Y', strtotime("first day of previous month")))->where('month','<=',date('m', strtotime("first day of previous month")));
            });
        })->latest()->first();
    }

    public function getUntilLastMonthBillAttribute()
    {
        return $this->bills()->where(function($q){
            $q->where('year','<',date('Y', strtotime("first day of previous month")))->orWhere(function($q){
                $q->where('year',date('Y', strtotime("first day of previous month")))->where('month','<=',date('m', strtotime("first day of previous month")));
            });
        })->latest()->get();
    }

    public function getThisMonthNominalAttribute()
    {
        return $this->thisMonthBill ? $this->thisMonthBill->spp_nominal : 0;
    }
    
    public function getThisMonthNominalWithSeparatorAttribute()
    {
        return number_format($this->thisMonthNominal, 0, ',', '.');
    }

    public function getThisMonthDeductionAttribute()
    {
        return $this->thisMonthBill ? $this->thisMonthBill->deduction_nominal : 0;
    }
    
    public function getThisMonthDeductionWithSeparatorAttribute()
    {
        return number_format($this->thisMonthDeduction, 0, ',', '.');
    }

    public function getThisMonthNetBillAttribute()
    {
        return $this->thisMonthBill ? ($this->thisMonthBill->spp_nominal-$this->thisMonthBill->deduction_nominal) : 0;
    }
    
    public function getThisMonthNetBillWithSeparatorAttribute()
    {
        return number_format($this->thisMonthNetBill, 0, ',', '.');
    }

    public function getThisMonthPaidAttribute()
    {
        return $this->thisMonthBill ? $this->thisMonthBill->spp_paid : 0;
    }
    
    public function getThisMonthPaidWithSeparatorAttribute()
    {
        return number_format($this->thisMonthPaid, 0, ',', '.');
    }
}

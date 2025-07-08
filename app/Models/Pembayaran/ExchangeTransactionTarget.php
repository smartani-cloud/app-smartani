<?php

namespace App\Models\Pembayaran;

use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeTransactionTarget extends Model
{
    use HasFactory;
    protected $table = "tm_exchange_transaction_target";
    protected $fillable = [
        'exchange_transaction_id',
        'student_id',
        'is_student',
        'nominal',
        'transaction_type',
    ];


    public function scopeJenisPembayaran()
    {
        if($this->transaction_type == 1){
            return 'BMS';
        }else{
            return 'SPP';
        }
    }

    public function exchangeTransaction()
    {
        return $this->belongsTo(ExchangeTransaction::class,'exchange_transaction_id');
    }

    public function student()
    {
        if($this->is_student == 0){
            return $this->belongsTo(CalonSiswa::class,'student_id');
        }
        return $this->belongsTo(Siswa::class,'student_id');
    }

    public function calon()
    {
        return $this->belongsTo(CalonSiswa::class,'student_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class,'student_id');
    }
    
    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
}

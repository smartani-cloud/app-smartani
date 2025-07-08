<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeTransaction extends Model
{
    use HasFactory;
    protected $table = "tm_exchange_transaction";
    protected $fillable = [
        'transaction_id',
        'origin',
        'nominal',
        'refund',
        'status',
    ];

    public function scopeJenisPembayaran()
    {
        if(in_array($this->origin,[1,3])){
            return 'BMS';
        }else{
            return 'SPP';
        }
    }

    public function transactionOrigin()
    {
        if($this->origin == 1){
            return $this->belongsTo(BmsTransaction::class,'transaction_id');
        }elseif($this->origin == 2){
            return $this->belongsTo(SppTransaction::class,'transaction_id');
        }else{
            return $this->belongsTo(BmsTransactionCalonSiswa::class,'transaction_id');
        }
    }

    public function bmsTransactionOrigin()
    {
        return $this->belongsTo(BmsTransaction::class,'transaction_id');
    }

    public function sppTransactionOrigin()
    {
        return $this->belongsTo(SppTransaction::class,'transaction_id');
    }

    public function bmsCandidateTransactionOrigin()
    {
        return $this->belongsTo(BmsTransactionCalonSiswa::class,'transaction_id');
    }

    public function transactionTarget()
    {
        return $this->hasMany(ExchangeTransactionTarget::class,'exchange_transaction_id');
    }
    
    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
    
    public function getRefundWithSeparatorAttribute()
    {
        return number_format($this->refund, 0, ',', '.');
    }
}

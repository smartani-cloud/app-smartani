<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class SalePayment extends Model
{
    use HasFactory;

    protected $table = "tm_sale_payments";
    protected $fillable = [
    	'sales_id',
        'date',
        'note',
        'value',
        'finance_detail_id',
    ];

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale\Sale','sale_id');
    }

    public function finance()
    {
        return $this->belongsTo('App\Models\Finance\FinanceDetail','finance_detail_id');
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getDatedmYAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('d-m-Y');
    }

    public function getValueWithSeparatorAttribute()
    {
        return number_format($this->value, 0, ',', '.');
    }

    public function getNameAttribute()
    {
        Date::setLocale('id');
        return 'Pembayaran Tgl. '.Date::parse($this->date)->format('j F Y');
    }
}

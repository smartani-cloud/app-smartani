<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class OutcomeMaterialPayment extends Model
{
    use HasFactory;

    protected $table = "outcome_material_payments";
    protected $fillable = [
    	'outcome_material_id',
        'date',
        'note',
        'value',
        'finance_detail_id',
    ];

    public function outcomeMaterial()
    {
        return $this->belongsTo('App\Models\Finance\OutcomeMaterial','outcome_material_id');
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

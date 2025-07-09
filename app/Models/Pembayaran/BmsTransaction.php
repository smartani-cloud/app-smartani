<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class BmsTransaction extends Model
{
    use HasFactory;
    protected $table = "tm_bms_trx";
    protected $fillable = [
        'unit_id',
        'student_id',
        'month',
        'year',
        'nominal',
        'academic_year_id',
        'trx_id',
        'date',
        'exchange_que',
        'created_at',
    ];
    
    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->year.'-'.$this->month.'-'.$this->date)->format('Y-m-d');
    }

    public function getCreatedAtIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->created_at)->format('Y-m-d');
    }
}

<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class BmsTransactionCalonSiswa extends Model
{
    use HasFactory;
    protected $table = "tm_bms_trx_candidate";
    protected $fillable = [
        'unit_id',
        'candidate_student_id',
        'month',
        'year',
        'nominal',
        'academic_year_id',
        'trx_id',
        'exchange_que',
        'date',
    ];
    
    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\CalonSiswa', 'candidate_student_id');
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
}

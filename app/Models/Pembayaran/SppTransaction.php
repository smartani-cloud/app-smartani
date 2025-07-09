<?php

namespace App\Models\Pembayaran;

use App\Models\Siswa\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class SppTransaction extends Model
{
    use HasFactory;
    protected $table = "tm_spp_trx";
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
        return $this->belongsTo(Siswa::class, 'student_id');
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

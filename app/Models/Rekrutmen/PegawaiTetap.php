<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class PegawaiTetap extends Model
{
    use HasFactory;

    protected $table = "tm_permanent_employee";

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function getPromotionDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->promotion_date)->format('j F Y');
    }
}

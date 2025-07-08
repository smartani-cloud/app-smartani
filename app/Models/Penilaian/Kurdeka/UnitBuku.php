<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitBuku extends Model
{
    use HasFactory;
    protected $table = "tas_unit_book";
    protected $fillable = [
        'semester_id',
        'unit_id',
		'book_id',
        'employee_id'
    ];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }
	
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function buku()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\Buku', 'book_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai', 'employee_id');
    }
}

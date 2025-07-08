<?php

namespace App\Models\Siswa;

use App\Models\Kbm\Kelas;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\SppBill;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Siswa extends Model
{
    use HasFactory;
    protected $table = "student_history";
    protected $fillable = [
        'id',
        'unit_id', // add unit
        'reg_number',
        'student_id',
        'student_nis',
        'student_nisn',
        'join_date',
        'semester_id',
        'year_spp',
        'month_spp',
        'level_id',
        'class_id',
        'origin_school',
        'origin_school_address',
        'info_from',    // informasi dari
        'info_name',    // informasi nama
        'position',     // posisi
        'is_lulus',
        'graduate_year',
    ];

    public function identitas()
    {
        return $this->belongsTo('App\Models\Siswa\IdentitasSiswa', 'student_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function tahunLulus()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','graduate_year');
    }

    public function nilaiRapor()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiRapor', 'student_id');
    }

    public function arsip()
    {
        return $this->hasMany('App\Models\Penilaian\Arsip', 'student_id');
    }

    public function rapor()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiRapor', 'student_id');
    }

    public function usp()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiPraktekUsp', 'student_id');
    }

    public function skhb()
    {
        return $this->hasMany('App\Models\Penilaian\SKHB', 'student_id');
    }

    public function spp()
    {
        return $this->hasOne('App\Models\Pembayaran\Spp','student_id');
    }

    public function bms()
    {
        return $this->hasOne(BMS::class,'student_id');
    }

    public function virtualAccount()
    {
        return $this->hasOne('App\Models\Pembayaran\VirtualAccountSiswa','student_id');
    }

    public function sppBill()
    {
        return $this->hasMany(SppBill::class,'student_id');
    }

    public function riwayatKelas()
    {
        return $this->hasMany('App\Models\Kbm\HistoryKelas', 'student_id');
    }

    public function getStudentNisLinkAttribute()
    {
        return str_replace("/","-",$this->student_nis);
    }
    
    public function scopeLuius($query)
    {
        return $query->where('is_lulus',1);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_lulus',0);
    }
}

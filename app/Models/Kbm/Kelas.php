<?php

namespace App\Models\Kbm;

use App\Models\Jurusan;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = "tm_class";
    protected $fillable = ['level_id','class_name_id','unit_id','teacher_id','academic_year_id','status','major_id'];
    // status : 1.Menambah Siswa, 2.Mengajukan Kelas, 3.Diterima, 4.Ditolak
    // jika ditolak, dia bisa menambah atau mengurangi siswa
    // saat mengajukan kelas atau diterima tidak dapat menambahkan siswa

    public function namakelases()
    {
        return $this->belongsTo(NamaKelas::class,'class_name_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class,'major_id');
    }

    public function tahunajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level','level_id');
    }

    public function walikelas()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','teacher_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa','class_id');
    }

    public function jadwal()
    {
        return $this->hasMany('App\Models\Kbm\JadwalPelajaran','class_id');
    }

    public function riwayat()
    {
        return $this->hasMany('App\Models\Kbm\HistoryKelas','class_id');
    }

    public function rapor()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiRapor','class_id');
    }

    public function getLevelNameAttribute()
    {
        return $this->level->level.' '.$this->namakelases->class_name;
    }

    // public function walikelas()
    // {
    //     return $this->belongsTo('App\Models\Pegawai','teacher_id');
    // }
}

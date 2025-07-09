<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiRapor extends Model
{
    use HasFactory;
    protected $table = "report_score";
    protected $guarded = [];

    public function sikap()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiSikap', 'score_id');
    }

    public function sikap_pts()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiSikapPts', 'score_id');
    }

    public function pengetahuan()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiPengetahuan', 'score_id');
    }

    public function keterampilan()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiKeterampilan', 'score_id');
    }

    public function nilaiAkhirKurdeka()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\NilaiAkhir', 'report_score_id');
    }

    public function formatifKualitatif()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\FormatifKualitatif', 'report_score_id');
    }

    public function nilaiIklas()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\NilaiIklas', 'report_score_id');
    }

    public function khatamKurdeka()
    {
        return $this->hasOne('App\Models\Penilaian\Kurdeka\Khatam', 'report_score_id');
    }

    public function khatamQuran()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\KhatamQuran', 'report_score_id');
    }

    public function khatamBuku()
    {
        return $this->hasOne('App\Models\Penilaian\Kurdeka\KhatamBuku', 'report_score_id');
    }

    public function quranKurdeka()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\Quran', 'report_score_id');
    }

    public function hafalanKurdeka()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\Hafalan', 'report_score_id');
    }

    public function deskripsiKurdeka()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\Deskripsi', 'report_score_id');
    }

    public function iklas()
    {
        return $this->hasOne('App\Models\Penilaian\NilaiIklas', 'score_id');
    }

    public function tilawah()
    {
        return $this->hasOne('App\Models\Penilaian\Tilawah', 'score_id');
    }

    public function tahfidz()
    {
        return $this->hasOne('App\Models\Penilaian\Tahfidz', 'score_id');
    }

    public function hafalan()
    {
        return $this->hasOne('App\Models\Penilaian\Hafalan', 'score_id');
    }

    public function kehadiran()
    {
        return $this->hasOne('App\Models\Penilaian\Kehadiran', 'score_id');
    }

    public function pts()
    {
        return $this->hasOne('App\Models\Penilaian\RaporPts', 'score_id');
    }

    public function pts_tk()
    {
        return $this->hasOne('App\Models\Penilaian\PtsTK', 'score_id');
    }

    public function pas()
    {
        return $this->hasOne('App\Models\Penilaian\RaporPas', 'score_id');
    }

    public function pas_tk()
    {
        return $this->hasOne('App\Models\Penilaian\PasTK', 'score_id');
    }

    public function ekstra()
    {
        return $this->hasMany('App\Models\Penilaian\Ekstra', 'score_id');
    }

    public function prestasi()
    {
        return $this->hasMany('App\Models\Penilaian\Prestasi', 'score_id');
    }

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }

    public function kelas()
    {
        return $this->belongsTo('App\Models\Kbm\Kelas', 'class_id');
    }
}

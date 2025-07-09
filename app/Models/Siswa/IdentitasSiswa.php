<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class IdentitasSiswa extends Model
{
    use HasFactory;
    protected $table = "tm_students";
    protected $fillable = [
        'nik',
        'student_name',
        'student_nickname',
        'birth_place',
        'birth_date',
        'gender_id',
        'religion_id',
        'child_of',
        'family_status',
        'address',
        'address_number',
        'rt',
        'rw',
        'region_id',
        'parent_id',
        'sibling_name',
        'sibling_level_id',
    ];

    public function orangtua()
    {
        return $this->belongsTo('App\Models\Siswa\OrangTua', 'parent_id');
    }

    public function jeniskelamin()
    {
        return $this->belongsTo('App\Models\JenisKelamin', 'gender_id');
    }

    public function agama()
    {
        return $this->belongsTo('App\Models\Agama', 'religion_id');
    }

    public function levelsaudara()
    {
        return $this->belongsTo('App\Models\Level', 'sibling_level_id');
    }

    public function wilayah()
    {
        return $this->belongsTo('App\Models\Wilayah', 'region_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'student_id');
    }

    public function getLatestUnitAttribute()
    {
        if($this->siswas()->count() > 0){
            $siswa = $this->siswas()->select('unit_id')->latest()->first();
            return $siswa->unit_id;
        }
        else return null;
    }

    public function getLatestLevelAttribute()
    {
        if($this->siswas()->count() > 0){
            $siswa = $this->siswas()->select('unit_id','class_id','is_lulus','graduate_year')->latest()->first();
            return $siswa->is_lulus == 0 ? ($siswa->kelas ? $siswa->kelas->levelName : null) : 'Lulus'.($siswa->tahunLulus ? ' '.$siswa->tahunLulus->academic_year_end : null);
        }
        else return null;
    }

    public function getBirthDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->birth_date)->format('j F Y');
    }
}

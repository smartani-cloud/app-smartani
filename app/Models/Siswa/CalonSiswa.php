<?php

namespace App\Models\Siswa;

use App\Models\Pembayaran\BmsCalonSiswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DateTime;
use Jenssegers\Date\Date;

class CalonSiswa extends Model
{
    use HasFactory;
    protected $table = "tm_candidate_students";
    protected $fillable = [
        'id',
        'unit_id', // add unit
        'student_status_id',
        'reg_number',
        'student_id',
        'student_nis',
        'student_nisn',
        'student_name',
        'student_nickname', // add nama panggilan
        'nik',
        'birth_place',
        'birth_date',
        'gender_id',
        'religion_id',
        'child_of',
        'family_status',
        'join_date',
        'academic_year_id',
        'semester_id',
        'level_id',
        'address',
        'address_number',
        'rt',
        'rw',
        'region_id',
        'parent_id',
        'origin_school',
        'origin_school_address',
        'sibling_name',
        'sibling_level_id',
        'info_from',    // informasi dari
        'info_name',    // informasi nama
        'position',     // posisi

        'status_id',
        'last_status_id',
        'bank_id',
        'account_number',
        'account_holder',
        'reject_reason',     // posisi
        'link',
        'interview_date',
        'interview_time',
        'observation_date',
        'observation_time',
        'observation_link',
        'acc_status_id',
        'acc_time',
    ];

    public function statusSiswa()
    {
        return $this->belongsTo('App\Models\Siswa\StatusSiswa','student_status_id');
    }

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

    public function tahunAjaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran', 'academic_year_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function levelsaudara()
    {
        return $this->belongsTo('App\Models\Level', 'sibling_level_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function wilayah()
    {
        return $this->belongsTo('App\Models\Wilayah', 'region_id');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id');
    }

    public function accEdukasi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','education_acc_id');
    }

    public function getBirthDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->birth_date)->format('j F Y');
    }

    public function virtualAccount()
    {
        return $this->hasOne('App\Models\Pembayaran\VirtualAccountCalonSiswa','candidate_student_id');
    }

    public function bms()
    {
        return $this->hasOne(BmsCalonSiswa::class,'candidate_student_id');
    }

    public function getIsBirthDateValidAttribute()
    {
        $d = DateTime::createFromFormat('Y-m-d', $this->birth_date);
        return $d && $d->format('Y-m-d') === $this->birth_date;
    }

    public function getInterviewDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->interview_date)->format('j F Y');
    }

    public function getPaymentDeadlineDateAttribute()
    {
        $deadline = '-';
        if($this->acc_time){
            $acc_time = Date::parse($this->acc_time);
            $deadline = $acc_time->addDays(7);
        }
        return $deadline;
    }

    public function getPaymentDeadlineDateIdAttribute()
    {
        Date::setLocale('id');
        if($this->paymentDeadlineDate != '-'){
            return Date::parse($this->paymentDeadlineDate)->format('j F Y');
        }
        else{
            return $this->paymentDeadlineDate;
        }
    }
}

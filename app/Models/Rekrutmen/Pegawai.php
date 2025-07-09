<<<<<<< HEAD
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Helpers\PhoneHelper;

use File;
use Carbon\Carbon;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = "tm_employees";

    public function jenisKelamin()
    {
        return $this->belongsTo('App\Models\JenisKelamin','gender_id');
    }

    public function statusPernikahan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPernikahan','marriage_status_id');
    }

    public function alamat()
    {
        return $this->belongsTo('App\Models\Wilayah','region_id');
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo('App\Models\Rekrutmen\PendidikanTerakhir','recent_education_id');
    }

    public function latarBidangStudi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\LatarBidangStudi','academic_background_id');
    }

    public function universitas()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Universitas','university_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function statusPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPegawai','employee_status_id');
    }

    public function statusBaru()
    {
        return $this->belongsTo('App\Models\StatusAktif','join_badge_status_id');
    }

    public function statusPhk()
    {
        return $this->belongsTo('App\Models\StatusAktif','disjoin_badge_status_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','active_status_id');
    }

    public function units()
    {
        return $this->hasMany('App\Models\Rekrutmen\PegawaiUnit','employee_id');
    }

    public function spk()
    {
        return $this->hasMany('App\Models\Rekrutmen\Spk','employee_id');
    }

    public function tetap()
    {
        return $this->hasOne('App\Models\Rekrutmen\PegawaiTetap','employee_id');
    }

    public function evaluasi()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','employee_id');
    }

    public function penempatan()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','employee_id');
    }

    public function narasumber()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','speaker_id');
    }

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\PresensiPelatihan','employee_id');
    }

    public function accCalonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','education_acc_id');
    }

    public function accPenempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','acc_employee_id');
    }

    public function accEvaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','education_acc_id');
    }

    public function skbm()
    {
        return $this->hasMany('App\Models\Skbm\Skbm','principle_id');
    }

    public function skbmDetail()
    {
        return $this->hasMany('App\Models\Skbm\SkbmDetail','employee_id');
    }

    public function accPelatihanPegawai()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','education_acc_id');
    }

    public function phk()
    {
        return $this->hasOne('App\Models\Phk\Phk','employee_id');
    }

    public function accPhk()
    {
        return $this->hasMany('App\Models\Phk\Phk','director_acc_id');
    }

    // Psc

    public function pscScore()
    {
        return $this->hasMany('App\Models\Psc\PscScore','employee_id');
    }

    // Kependidikan

    public function kelas()
    {
        return $this->hasMany('App\Models\Kbm\Kelas','teacher_id');
    }

    public function jadwalPelajarans()
    {
        return $this->hasMany('App\Models\Kbm\JadwalPelajaran','teacher_id');
    }

    public function predikat()
    {
        return $this->hasMany('App\Models\Penilaian\PredikatDeskripsi','employee_id');
    }

    public function login()
    {
        return $this->hasOne('App\Models\LoginUser','user_id')->where('role_id','!=',36);
    }

    public function getTitleNameAttribute()
    {
        return ($this->gender_id == 1 ? 'Bapak ' : 'Ibu ').$this->name;
    }

    public function getBirthDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->birth_date)->format('j F Y');
    }

    public function getAgeAttribute()
    {
        return $this->ageOriginal.' tahun';
    }

    public function getAgeOriginalAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getPhotoPathAttribute()
    {
        if($this->photo) return 'img/photo/pegawai/'.$this->photo;
        else return null;
    }

    public function getShowPhotoAttribute(){
        return File::exists($this->photoPath) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getRegionCodeAttribute()
    {
        return $this->alamat->code;
    }

    public function getYearsOfServiceAttribute()
    {
        $join = Carbon::parse($this->join_date);
        if(!$this->disjoin_date){
            $today = Carbon::now('Asia/Jakarta');
            $interval = $join->diffInDays($today);
            if($interval < $today->diffInDays(Carbon::now('Asia/Jakarta')->subMonth())){
                return intval($interval).' hari';
            }
            elseif($interval < $today->diffInDays(Carbon::now('Asia/Jakarta')->subYear())){
                $interval = $join->diffInMonths($today);
                return intval($interval).' bulan';
            }
            else{
                $interval = $join->diffInYears($today);
                return intval($interval).' tahun';
            }
        }
        else{
            $disjoin = Carbon::parse($this->disjoin_date);
            $interval = $join->diffInDays($disjoin);
            if($interval < $disjoin->diffInDays(Carbon::parse($this->disjoin_date)->subMonth())){
                return intval($interval).' hari';
            }
            elseif($interval < $disjoin->diffInDays(Carbon::parse($this->disjoin_date)->subYear())){
                $interval = $join->diffInMonths($disjoin);
                return intval($interval).' bulan';
            }
            else{
                $interval = $join->diffInYears($disjoin);
                return intval($interval).' tahun';
            }
        }
    }

    public function getPhoneNumberIdAttribute()
    {
        $phone_number = $this->phone_number[0] == '0' ? '+62'.substr($this->phone_number, 1) : $this->phone_number;
        return $phone_number;
    }
    
    public function getPhoneNumberWithDashIdAttribute()
    {
        return PhoneHelper::addDashesId((string)$this->phone_number,'mobile','0');
    }

    public function getJoinDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->join_date)->format('j F Y');
    }

    public function getRemainingPeriodAttribute(){
        if($this->spk()->aktif()->count() > 0){
            $spk = $this->spk()->select('id','employee_id','period_end')->aktif()->latest()->first();
            if($spk){
                return $spk->remainingPeriod;
            }
            return null;
        }
        else return null;
    }
    
    public function scopeAktif($query)
    {
        return $query->where('active_status_id',1);
    }

    public function scopePtt($query)
    {
        return $query->whereIn('employee_status_id',[3,4,5]);
    }

    public function scopeGuru($query)
    {
        return $query->whereHas('jabatan', function (Builder $query){
            $query->guru();
        });
    }
}
=======
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Helpers\PhoneHelper;

use File;
use Carbon\Carbon;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = "tm_employees";

    public function jenisKelamin()
    {
        return $this->belongsTo('App\Models\JenisKelamin','gender_id');
    }

    public function statusPernikahan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPernikahan','marriage_status_id');
    }

    public function alamat()
    {
        return $this->belongsTo('App\Models\Wilayah','region_id');
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo('App\Models\Rekrutmen\PendidikanTerakhir','recent_education_id');
    }

    public function latarBidangStudi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\LatarBidangStudi','academic_background_id');
    }

    public function universitas()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Universitas','university_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function statusPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPegawai','employee_status_id');
    }

    public function statusBaru()
    {
        return $this->belongsTo('App\Models\StatusAktif','join_badge_status_id');
    }

    public function statusPhk()
    {
        return $this->belongsTo('App\Models\StatusAktif','disjoin_badge_status_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','active_status_id');
    }

    public function units()
    {
        return $this->hasMany('App\Models\Rekrutmen\PegawaiUnit','employee_id');
    }

    public function spk()
    {
        return $this->hasMany('App\Models\Rekrutmen\Spk','employee_id');
    }

    public function tetap()
    {
        return $this->hasOne('App\Models\Rekrutmen\PegawaiTetap','employee_id');
    }

    public function evaluasi()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','employee_id');
    }

    public function penempatan()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','employee_id');
    }

    public function narasumber()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','speaker_id');
    }

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\PresensiPelatihan','employee_id');
    }

    public function accCalonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','education_acc_id');
    }

    public function accPenempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','acc_employee_id');
    }

    public function accEvaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','education_acc_id');
    }

    public function skbm()
    {
        return $this->hasMany('App\Models\Skbm\Skbm','principle_id');
    }

    public function skbmDetail()
    {
        return $this->hasMany('App\Models\Skbm\SkbmDetail','employee_id');
    }

    public function accPelatihanPegawai()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','education_acc_id');
    }

    public function phk()
    {
        return $this->hasOne('App\Models\Phk\Phk','employee_id');
    }

    public function accPhk()
    {
        return $this->hasMany('App\Models\Phk\Phk','director_acc_id');
    }

    // Psc

    public function pscScore()
    {
        return $this->hasMany('App\Models\Psc\PscScore','employee_id');
    }

    // Kependidikan

    public function kelas()
    {
        return $this->hasMany('App\Models\Kbm\Kelas','teacher_id');
    }

    public function jadwalPelajarans()
    {
        return $this->hasMany('App\Models\Kbm\JadwalPelajaran','teacher_id');
    }

    public function predikat()
    {
        return $this->hasMany('App\Models\Penilaian\PredikatDeskripsi','employee_id');
    }

    public function login()
    {
        return $this->hasOne('App\Models\LoginUser','user_id')->where('role_id','!=',36);
    }

    public function getTitleNameAttribute()
    {
        return ($this->gender_id == 1 ? 'Bapak ' : 'Ibu ').$this->name;
    }

    public function getBirthDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->birth_date)->format('j F Y');
    }

    public function getAgeAttribute()
    {
        return $this->ageOriginal.' tahun';
    }

    public function getAgeOriginalAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getPhotoPathAttribute()
    {
        if($this->photo) return 'img/photo/pegawai/'.$this->photo;
        else return null;
    }

    public function getShowPhotoAttribute(){
        return File::exists($this->photoPath) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getRegionCodeAttribute()
    {
        return $this->alamat->code;
    }

    public function getYearsOfServiceAttribute()
    {
        $join = Carbon::parse($this->join_date);
        if(!$this->disjoin_date){
            $today = Carbon::now('Asia/Jakarta');
            $interval = $join->diffInDays($today);
            if($interval < $today->diffInDays(Carbon::now('Asia/Jakarta')->subMonth())){
                return intval($interval).' hari';
            }
            elseif($interval < $today->diffInDays(Carbon::now('Asia/Jakarta')->subYear())){
                $interval = $join->diffInMonths($today);
                return intval($interval).' bulan';
            }
            else{
                $interval = $join->diffInYears($today);
                return intval($interval).' tahun';
            }
        }
        else{
            $disjoin = Carbon::parse($this->disjoin_date);
            $interval = $join->diffInDays($disjoin);
            if($interval < $disjoin->diffInDays(Carbon::parse($this->disjoin_date)->subMonth())){
                return intval($interval).' hari';
            }
            elseif($interval < $disjoin->diffInDays(Carbon::parse($this->disjoin_date)->subYear())){
                $interval = $join->diffInMonths($disjoin);
                return intval($interval).' bulan';
            }
            else{
                $interval = $join->diffInYears($disjoin);
                return intval($interval).' tahun';
            }
        }
    }

    public function getPhoneNumberIdAttribute()
    {
        $phone_number = $this->phone_number[0] == '0' ? '+62'.substr($this->phone_number, 1) : $this->phone_number;
        return $phone_number;
    }
    
    public function getPhoneNumberWithDashIdAttribute()
    {
        return PhoneHelper::addDashesId((string)$this->phone_number,'mobile','0');
    }

    public function getJoinDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->join_date)->format('j F Y');
    }

    public function getRemainingPeriodAttribute(){
        if($this->spk()->aktif()->count() > 0){
            $spk = $this->spk()->select('id','employee_id','period_end')->aktif()->latest()->first();
            if($spk){
                return $spk->remainingPeriod;
            }
            return null;
        }
        else return null;
    }
    
    public function scopeAktif($query)
    {
        return $query->where('active_status_id',1);
    }

    public function scopePtt($query)
    {
        return $query->whereIn('employee_status_id',[3,4,5]);
    }

    public function scopeGuru($query)
    {
        return $query->whereHas('jabatan', function (Builder $query){
            $query->guru();
        });
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

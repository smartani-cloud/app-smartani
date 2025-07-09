<<<<<<< HEAD
<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;
    protected $table = "tm_parents";
    protected $fillable = [
        'employee_id',
        'father_name',
        'father_nik',
        'father_email',
        'father_phone',
        'father_job',
        'father_position', //jabatan ayah
        'father_job_address', //alamat kantor ayah
        'father_phone_office',
        'father_salary', //gaji ayah

        'mother_name',
        'mother_nik',
        'mother_email',
        'mother_phone',
        'mother_job',
        'mother_position', //jabatan ibu
        'mother_job_address', //alamat kantor ibu
        'mother_phone_office',
        'mother_salary', //gaji ibu

        'parent_address',
        'parent_phone_number',

        'guardian_name',
        'guardian_nik',
        'guardian_address',
        'guardian_email',
        'guardian_phone_number',
        'guardian_job',
        'guardian_position', //jabatan wali
        'guardian_job_address', //alamat kantor wali
        'guardian_phone_office',
        'guardian_salary', //gaji wali
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function siswas()
    {
        return $this->hasMany(IdentitasSiswa::class,'parent_id','id');
    }

    public function calonSiswa()
    {
        return $this->hasMany(CalonSiswa::class,'parent_id');
    }
    
    public function user()
    {
        return $this->hasOne('App\Models\LoginUser','user_id')->where('role_id',36);
    }

    public function users()
    {
        return $this->hasMany('App\Models\LoginUser','user_id');
    }

    public function getNameAttribute()
    {
        return ($this->father_name ? $this->father_name.($this->mother_name ? '/' : null) : null).($this->mother_name ? $this->mother_name.($this->guardian_name ? '/' : null) : null).($this->guardian_name ? $this->guardian_name : null);
    }

    // Unused
    public function getLoginUserAttribute()
    {
        return $this->users()->where('role_id',36)->first();
    }

    public function getChildrensCountAttribute()
    {
        return $this->calonSiswa()->count()+$this->siswas()->count();
    }

    public function getChildrensAttribute()
    {
        $datas = null;
        $calons = $this->calonSiswa()->count() > 0 ? $this->calonSiswa()->select('student_name')->get(): null;
        $siswas = $this->siswas()->count() > 0 ? $this->siswas()->select('student_name')->get() : null;
        if($calons && $siswas){
            $datas = $siswas->concat($calons);
        }
        elseif($siswas){
            $datas = $siswas;
        }
        else{
            $datas = $calons;
        }

        return $datas ? implode('; ',$datas->sortBy('student_name')->pluck('student_name')->unique()->toArray()) : null;
    }

    public function getStudentsWithClassAttribute()
    {
        $datas = null;
        $siswas = $this->siswas()->count() > 0 ? $this->siswas()->select('id','student_name')->orderBy('birth_date','desc')->get() : null;
        if($siswas){
            foreach($siswas as $siswa){
                $datas[] = $siswa->student_name.($siswa->latestLevel ? ' - Kelas '.$siswa->latestLevel : null);
            }
        }

        return $datas ? implode('; ',$datas) : null;
    }
}
=======
<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;
    protected $table = "tm_parents";
    protected $fillable = [
        'employee_id',
        'father_name',
        'father_nik',
        'father_email',
        'father_phone',
        'father_job',
        'father_position', //jabatan ayah
        'father_job_address', //alamat kantor ayah
        'father_phone_office',
        'father_salary', //gaji ayah

        'mother_name',
        'mother_nik',
        'mother_email',
        'mother_phone',
        'mother_job',
        'mother_position', //jabatan ibu
        'mother_job_address', //alamat kantor ibu
        'mother_phone_office',
        'mother_salary', //gaji ibu

        'parent_address',
        'parent_phone_number',

        'guardian_name',
        'guardian_nik',
        'guardian_address',
        'guardian_email',
        'guardian_phone_number',
        'guardian_job',
        'guardian_position', //jabatan wali
        'guardian_job_address', //alamat kantor wali
        'guardian_phone_office',
        'guardian_salary', //gaji wali
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function siswas()
    {
        return $this->hasMany(IdentitasSiswa::class,'parent_id','id');
    }

    public function calonSiswa()
    {
        return $this->hasMany(CalonSiswa::class,'parent_id');
    }
    
    public function user()
    {
        return $this->hasOne('App\Models\LoginUser','user_id')->where('role_id',36);
    }

    public function users()
    {
        return $this->hasMany('App\Models\LoginUser','user_id');
    }

    public function getNameAttribute()
    {
        return ($this->father_name ? $this->father_name.($this->mother_name ? '/' : null) : null).($this->mother_name ? $this->mother_name.($this->guardian_name ? '/' : null) : null).($this->guardian_name ? $this->guardian_name : null);
    }

    // Unused
    public function getLoginUserAttribute()
    {
        return $this->users()->where('role_id',36)->first();
    }

    public function getChildrensCountAttribute()
    {
        return $this->calonSiswa()->count()+$this->siswas()->count();
    }

    public function getChildrensAttribute()
    {
        $datas = null;
        $calons = $this->calonSiswa()->count() > 0 ? $this->calonSiswa()->select('student_name')->get(): null;
        $siswas = $this->siswas()->count() > 0 ? $this->siswas()->select('student_name')->get() : null;
        if($calons && $siswas){
            $datas = $siswas->concat($calons);
        }
        elseif($siswas){
            $datas = $siswas;
        }
        else{
            $datas = $calons;
        }

        return $datas ? implode('; ',$datas->sortBy('student_name')->pluck('student_name')->unique()->toArray()) : null;
    }

    public function getStudentsWithClassAttribute()
    {
        $datas = null;
        $siswas = $this->siswas()->count() > 0 ? $this->siswas()->select('id','student_name')->orderBy('birth_date','desc')->get() : null;
        if($siswas){
            foreach($siswas as $siswa){
                $datas[] = $siswa->student_name.($siswa->latestLevel ? ' - Kelas '.$siswa->latestLevel : null);
            }
        }

        return $datas ? implode('; ',$datas) : null;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

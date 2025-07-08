<?php

namespace App\Models;

use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\OrangTua;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LoginUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "login_user";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'user_id',
        'role_id',
        'active_status_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function orangtua()
    {
        return $this->belongsTo(OrangTua::class,'user_id');
    }

    public function ortu()
    {
        return $this->hasOne('App\Models\Siswa\OrangTua','user_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class,'user_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }

    public function notifikasi()
    {
        return $this->hasMany('App\Models\Notifikasi','user_id');
    }
    
    public function scopeAktif($query)
    {
        return $query->where('active_status_id',1);
    }
    
    public function scopeOrangTua($query)
    {
        return $query->where('role_id',36);
    }
    
    public function scopePegawai($query)
    {
        return $query->where('role_id','!=',36);
    }
}

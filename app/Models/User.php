<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Modules\Access\Contracts\RoleRelationResolver;

// Deletable
use App\Models\Rekrutmen\Pegawai;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'user_id',
        'role_id',
        'status_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return app(RoleRelationResolver::class)->resolve($this);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class,'user_id');
    }
    
    public function profiles()
    {
        return $this->hasMany('App\Models\UserProfile','user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany('App\Models\Notifikasi','user_id');
    }
    
    public function scopeAktif($query)
    {
        return $query->where('status_id',1);
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

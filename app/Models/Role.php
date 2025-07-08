<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $table = "tref_user_roles";

    protected $fillable = [
        'code',
        'name',
        'desc',
        'role_group_id'
    ];

    public function group()
    {
        return $this->belongsTo('App\Models\RoleGroup','role_group_id');
    }

    public function jabatan()
    {
        return $this->hasOne('App\Models\Penempatan\Jabatan','role_id');
    }

    public function loginUsers()
    {
        return $this->hasMany('App\Models\LoginUser','role_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User','role_id');
    }

    public function rights()
    {
        return $this->hasMany('App\Models\AccessRights\AccessRight','role_id');
    }
}

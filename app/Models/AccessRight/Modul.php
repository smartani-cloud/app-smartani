<?php

namespace App\Models\AccessRight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    use HasFactory;

    protected $table = "tm_moduls";

    protected $fillable = ['name','subsystem_id'];

    public function subsystem()
    {
        return $this->hasOne('App\Models\AccessRight\Subsystem','subsystem_id');
    }

    public function modulOperations()
    {
        return $this->hasMany('App\Models\AccessRight\ModulOperation','modul_id');
    }
}

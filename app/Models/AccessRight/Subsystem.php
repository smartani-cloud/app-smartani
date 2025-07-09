<?php

namespace App\Models\AccessRight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsystem extends Model
{
    use HasFactory;

    protected $table = "tm_subsystems";

    protected $fillable = ['name','desc'];

    public function moduls()
    {
        return $this->hasMany('App\Models\AccessRight\Modul','subsystem_id');
    }
}

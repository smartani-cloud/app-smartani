<?php

namespace App\Models\AccessRight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRight extends Model
{
    use HasFactory;

    protected $table = "tm_access_rights";

    protected $fillable = ['modul_operation_id','role_id'];

    public function modulOperation()
    {
        return $this->belongsTo('App\Models\AccessRight\ModulOperation','modul_operation_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }
}

<?php

namespace App\Models\AccessRight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulOperation extends Model
{
    use HasFactory;

    protected $table = "tas_modul_operations";

    protected $fillable = ['modul_id','operation_id'];

    public function modul()
    {
        return $this->belongsTo('App\Models\AccessRight\Modul','modul_id');
    }

    public function operation()
    {
        return $this->belongsTo('App\Models\AccessRight\Operation','operation_id');
    }

    public function rights()
    {
        return $this->hasMany('App\Models\AccessRight\AccessRight','modul_operation_id');
    }
}

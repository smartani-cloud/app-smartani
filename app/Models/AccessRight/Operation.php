<?php

namespace App\Models\AccessRight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $table = "tref_operations";

    protected $fillable = ['name','desc'];

    public function modulOperations()
    {
        return $this->hasMany('App\Models\AccessRight\ModulOperation','operation_id');
    }
}

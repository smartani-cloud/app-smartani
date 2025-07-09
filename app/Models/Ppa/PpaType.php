<?php

namespace App\Models\Ppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpaType extends Model
{
    use HasFactory;

    protected $table = "tref_ppa_type";
    protected $fillable = ['name'];

    public function ppas()
    {
        return $this->hasMany('App\Models\Ppa\Ppa','ppa_type_id');
    }
}

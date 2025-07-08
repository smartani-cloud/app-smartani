<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscValidator extends Model
{
    use HasFactory;

    protected $table = "psc_validator";
    protected $fillable = [
    	'position_desc',
    	'validator_name'
    ];

    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','validator_id');
    }
}

<?php

namespace App\Models\Ppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpaExclude extends Model
{
    use HasFactory;

    protected $table = "ppa_exclude";
    protected $fillable = [
    	'ppa_id'
    ];

    public function ppa()
    {
        return $this->belongsTo('App\Models\Ppa\Ppa','ppa_id');
    }
}

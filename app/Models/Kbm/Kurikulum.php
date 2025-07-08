<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = "tref_curricula";
    protected $fillable = ['name'];

    public function levels()
    {
        return $this->hasMany('App\Models\Kbm\TingkatKurikulum','curriculum_id');
    }
}

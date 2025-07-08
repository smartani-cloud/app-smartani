<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SasaranPelatihan extends Model
{
    use HasFactory;

    protected $table = "training_target";

    public function pelatihan()
    {
        return $this->belongsTo('App\Models\Pelatihan\Pelatihan','training_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\JabatanUnit','position_id');
    }
}

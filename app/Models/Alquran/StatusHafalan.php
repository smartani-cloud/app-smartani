<?php

namespace App\Models\Alquran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusHafalan extends Model
{
    use HasFactory;

    protected $table = "tref_memorize_status";
    protected $fillable = ['status'];

    public function rapor()
    {
        return $this->hasMany('App\Models\Penilaian\Surah', 'status_id');
    }
}

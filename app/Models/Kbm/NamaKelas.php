<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NamaKelas extends Model
{
    use HasFactory;
    protected $table = "tm_class_name";
    protected $fillable = ['class_name','unit_id'];

    public function kelases()
    {
        return $this->hasMany('App\Models\Kbm\Kelas','class_name_id');
    }
}

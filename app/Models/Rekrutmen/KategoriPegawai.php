<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPegawai extends Model
{
    use HasFactory;

    protected $table = "tref_employee_category";

    public function statuses()
    {
        return $this->hasMany('App\Models\Rekrutmen\StatusPegawai','category_id');
    }
}

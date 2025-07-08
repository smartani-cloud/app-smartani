<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAnggaran extends Model
{
    use HasFactory;

    protected $table = "budgeting_category";
    protected $fillable = ['name'];

    public function anggarans()
    {
        return $this->hasMany('App\Models\Anggaran\Anggaran','category_id');
    }
}

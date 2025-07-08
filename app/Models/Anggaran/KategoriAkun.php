<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAkun extends Model
{
    use HasFactory;

    protected $table = "tref_account_category";
    protected $fillable = ['name'];

    public function parent()
    {
        return $this->belongsTo('App\Models\Anggaran\KategoriAkun','upcategory');
    }

    public function akun()
    {
        return $this->hasMany('App\Models\Anggaran\Akun','account_category_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Anggaran\KategoriAkun','upcategory');
    }
}

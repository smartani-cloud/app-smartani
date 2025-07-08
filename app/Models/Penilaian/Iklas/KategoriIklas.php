<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriIklas extends Model
{
    use HasFactory;
    protected $table = "tref_iklas_categories";
    protected $fillable = [
		'number',
		'name'
    ];
    
    public function competencies()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\KompetensiKategoriIklas', 'category_id');
    }
}

<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    protected $table = "tref_books";
    protected $fillable = [
		'title',
		'total_pages'
    ];
    
    public function units()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\UnitBuku', 'book_id');
    }
    
    public function khatam()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\KhatamBuku', 'book_id');
    }
    
    public function getTitleWithPagesAttribute()
    {
        return $this->title.($this->total_pages ? ' [Hal: '.$this->total_pages.']' : null);
    }
}

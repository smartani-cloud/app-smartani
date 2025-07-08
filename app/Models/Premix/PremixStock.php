<?php

namespace App\Models\Premix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremixStock extends Model
{
    use HasFactory;

    protected $table = "tm_premix_stocks";
    protected $fillable = [
        'premix_id',
    	'year',
    	'quantity',
        'used',
    ];

    public function Premix()
    {
        return $this->belongsTo('App\Models\Premix\Premix','premix_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function getUsedWithSeparatorAttribute()
    {
        return number_format($this->used, 0, ',', '.');
    }
}

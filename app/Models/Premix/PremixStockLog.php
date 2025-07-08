<?php

namespace App\Models\Premix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremixStockLog extends Model
{
    use HasFactory;

    protected $table = "tm_premix_stock_logs";
    protected $fillable = [
        'premix_id',
    	'quantity'
    ];

    public function premix()
    {
        return $this->belongsTo('App\Models\Premix\Premix','premix_id');
    }

    public function details()
    {
        return $this->hasMany('App\Models\Premix\PremixStockLogDetail','premix_stock_log_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }
}

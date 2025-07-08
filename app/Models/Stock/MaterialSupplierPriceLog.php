<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class MaterialSupplierPriceLog extends Model
{
    use HasFactory;

    protected $table = "tm_material_supplier_price_logs";
    protected $fillable = [
        'materiaL_supplier_id',
    	'price',
        'is_active'
    ];

    public function materialSupplier()
    {
        return $this->belongsTo('App\Models\Stock\MaterialSupplier','material_supplier_id');
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getCreatedAtIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->created_at)->format('j F Y H.i');
    }

    public function getCreatedAtIdShortAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->created_at)->format('j M Y H.i');
    }

    public function scopeActive($query){
        return $query->where('is_active',1);
    }

    public function scopeInactive($query){
        return $query->where(function($q){
            $q->where('is_active',0)->orWhereNull('is_active');
        });
    }
}

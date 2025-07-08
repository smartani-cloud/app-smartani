<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = "tref_suppliers";
    protected $fillable = ['name','unit_id'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function materials()
    {
        return $this->hasMany('App\Models\Stock\MaterialSupplier','supplier_id');
    }

    public function getNameUnitAttribute()
    {
        return $this->name.($this->unit ? ' - '.$this->unit->name : null);
    }
}

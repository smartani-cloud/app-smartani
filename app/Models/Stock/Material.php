<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = "tm_materials";
    protected $fillable = ['name','unit_id'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function suppliers()
    {
        return $this->hasMany('App\Models\Stock\MaterialSupplier','material_id');
    }

    public function getNameUnitAttribute()
    {
        return $this->name.($this->unit ? ' - '.$this->unit->name : null);
    }
}

<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoF extends Model
{
    use HasFactory;

    protected $table = "tref_sofs";
    protected $fillable = ['name','unit_id','is_hidden'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function balance()
    {
        return $this->hasOne('App\Models\Finance\Balance','sof_id');
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project\ProjectSof','sof_id');
    }	

    public function getNameWithBalanceAttribute()
    {
        return $this->name.($this->balance ? ' ['.$this->balance->balanceWithSeparator.']' : null);
    }
    
    public function scopeShown($query)
    {
        return $query->where('is_hidden',0);
    }
    
    public function scopeHidden($query)
    {
        return $query->where('is_hidden',1);
    }
}

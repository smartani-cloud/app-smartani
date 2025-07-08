<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Outcome extends Model
{
    use HasFactory;

    protected $table = "tm_outcomes";
    protected $fillable = [
        'project_id',
    	'date',
        'unit_id',
        'type_id',
        'material_amount',
        'material_percentage',
        'operational_amount',
        'operational_percentage',
        'status_id',
        'acc_user_id',
        'acc_status',
        'acc_time',
        'user_id'
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Finance\OutcomeType','type_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Finance\OutcomeStatus','status_id');
    }

    public function accUser()
    {
        return $this->belongsTo('App\Models\User','acc_user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function materials()
    {
        return $this->hasMany('App\Models\Finance\OutcomeMaterial','outcome_id');
    }

    public function operationals()
    {
        return $this->hasMany('App\Models\Finance\OutcomeOperational','outcome_id');
    }

    public function sofs()
    {
        return $this->hasMany('App\Models\Finance\OutcomeSoF','outcome_id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\Finance\OutcomeFile','outcome_id');
    }

    public function getMaterialWithSeparatorAttribute()
    {
        return number_format($this->material_amount, 0, ',', '.');
    }

    public function getOperationalWithSeparatorAttribute()
    {
        return number_format($this->operational_amount, 0, ',', '.');
    }

    public function getTotalAttribute()
    {
        return $this->material_amount+$this->operational_amount;
    }

    public function getTotalWithSeparatorAttribute()
    {
        return number_format($this->total, 0, ',', '.');
    }

    public function getNameAttribute()
    {
        return Date::parse($this->date)->format('Ymj').$this->id;
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getAccTimeIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->acc_time)->format('j F Y');
    }
    
    public function scopeAccepted($query)
    {
        return $query->where('acc_status',1);
    }
}

<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Project extends Model
{
    use HasFactory;

    protected $table = "tm_projects";
    protected $fillable = [
    	'unit_id',
    	'month',
        'year',
        'sales_projection_nominal',
        'sales_projection_percentage',
        'cogs_nominal',
        'cogs_percentage',
        'operational_nominal',
        'operational_percentage',
        'gross_profit_nominal',
        'gross_profit_percentage',
        'profit_nominal',
        'profit_percentage',
        'sas_nominal',
        'sas_percentage',
        'net_profit_nominal',
        'net_profit_percentage',
        'status_id',
        'acc_user_id',
        'acc_status',
        'acc_time'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Project\ProjectStatus','status_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','acc_user_id');
    }

    public function cogs()
    {
        return $this->hasMany('App\Models\Project\ProjectCogs','project_id');
    }

    public function operationals()
    {
        return $this->hasMany('App\Models\Project\ProjectOperational','project_id');
    }

    public function productCogs()
    {
        return $this->hasMany('App\Models\Project\ProjectProductCogs','project_id');
    }

    public function productSalesTypes()
    {
        return $this->hasMany('App\Models\Project\ProjectProductSalesType','project_id');
    }

    public function sofs()
    {
        return $this->hasMany('App\Models\Project\ProjectSoF','project_id');
    }

    public function financeDetails()
    {
        return $this->hasMany('App\Models\Finance\FinanceDetail','project_id');
    }

    public function getSalesProjectionWithSeparatorAttribute()
    {
        return number_format($this->sales_projection_nominal, 0, ',', '.');
    }

    public function getCogsWithSeparatorAttribute()
    {
        return number_format($this->cogs_nominal, 0, ',', '.');
    }

    public function getOperationalWithSeparatorAttribute()
    {
        return number_format($this->operational_nominal, 0, ',', '.');
    }

    public function getGrossProfitWithSeparatorAttribute()
    {
        return number_format($this->gross_profit_nominal, 0, ',', '.');
    }

    public function getProfitWithSeparatorAttribute()
    {
        return number_format($this->profit_nominal, 0, ',', '.');
    }

    public function getSasWithSeparatorAttribute()
    {
        return number_format($this->sas_nominal, 0, ',', '.');
    }

    public function getNetProfitWithSeparatorAttribute()
    {
        return number_format($this->net_profit_nominal, 0, ',', '.');
    }

    public function getAccTimeIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->acc_time)->format('j F Y');
    }

    public function getNameAttribute()
    {
        Date::setLocale('id');
        return 'Proposal '.$this->unit->name.' '.Date::parse($this->acc_time)->format('m').' '.$this->year;
    }

    public function getMonthIdAttribute()
    {
        Date::setLocale('id');
        return Date::createFromFormat('!m', $this->month)->format('F');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeAccepted($query)
    {
        return $query->where('acc_status',1);
    }
}

<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class FinanceDetail extends Model
{
    use HasFactory;

    protected $table = "finance_details";
    protected $fillable = [
    	'finance_id',
        'unit_id',
        'type_id',
        'report_type_id',
    	'date',
    	'desc',
        'amount',
        'project_id'
    ];

    public function finance()
    {
        return $this->belongsTo('App\Models\Finance\Finance','finance_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Finance\FinanceType','type_id');
    }

    public function reportType()
    {
        return $this->belongsTo('App\Models\Finance\ReportType','report_type_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getAmountWithSeparatorAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }
    
    public function scopeIncome($query)
    {
        return $query->where('type_id',2);
    }
    
    public function scopeOutcome($query)
    {
        return $query->whereIn('type_id',[1,3]);
    }
    
    public function scopeTax($query)
    {
        return $query->where('type_id',3);
    }
    
    public function scopeFactual($query)
    {
        return $query->whereIn('report_type_id',[1,2]);
    }
    
    public function scopeRealtime($query)
    {
        return $query->whereIn('report_type_id',[1,3]);
    }
}

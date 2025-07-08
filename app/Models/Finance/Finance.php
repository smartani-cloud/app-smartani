<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\Models\Finance\FinanceUnit;
use App\Models\Unit;

class Finance extends Model
{
    use HasFactory;

    protected $table = "tm_finances";
    protected $fillable = [
    	'month',
    	'year',
        'total_nominal_income',
        'total_nominal_outcome',
        'total_nominal_income_real',
        'total_nominal_outcome_real'
    ];

    public function details()
    {
        return $this->hasMany('App\Models\Finance\FinanceDetail','finance_id');
    }

    public function outcomes()
    {
        return $this->hasMany('App\Models\Finance\FinanceOutcome','finance_id');
    }

    public function units()
    {
        return $this->hasMany('App\Models\Finance\FinanceUnit','finance_id');
    }

    public function getMonthNameAttribute()
    {
        Carbon::setLocale('id');
        $date = Carbon::createFromFormat('!m', $this->month);
        return $date->format('F');
    }

    public function getIncomeWithSeparatorAttribute()
    {
        return number_format($this->total_nominal_income, 0, ',', '.');
    }

    public function getOutcomeWithSeparatorAttribute()
    {
        return number_format($this->total_nominal_outcome, 0, ',', '.');
    }

    public function getIncomeRealWithSeparatorAttribute()
    {
        return number_format($this->total_nominal_income_real, 0, ',', '.');
    }

    public function getOutcomeRealWithSeparatorAttribute()
    {
        return number_format($this->total_nominal_outcome_real, 0, ',', '.');
    }

    public static function getFinance()
    {
        $month = Carbon::now('Asia/Jakarta')->format('m');
        $year = Carbon::now('Asia/Jakarta')->format('Y');
        $finance = self::where([
            'month' => $month,
            'year' => $year
        ])->first();

        if(!$finance){
            $finance = new self();
            $finance->month = $month;
            $finance->year = $year;
            $finance->save();

            $finance->fresh();
        }

        return $finance;
    }

    public static function getFinances()
    {
        $month = Carbon::now('Asia/Jakarta')->format('m');
        $year = Carbon::now('Asia/Jakarta')->format('Y');
        $finance = self::where([
            'month' => $month,
            'year' => $year
        ])->first();

        if(!$finance){
            $finance = new self();
            $finance->month = $month;
            $finance->year = $year;
            $finance->save();
        }

        $finances = self::where([
            'month' => $month,
            'year' => $year
        ])->get();

        return $finances;
    }

    public static function getFinanceUnit($unit = null)
    {
        if($unit) $unit = Unit::where('id',$unit)->first();

        if($unit){
            $finance = self::getFinance();

            $financeUnit = $finance->units()->where('unit_id',$unit->id);

            if($financeUnit->count() < 1){
                $item = new FinanceUnit();
                $item->unit_id = $unit->id;
                $finance->units()->save($item);
            }

            $financeUnit = $financeUnit->first();

            return $financeUnit;
        }
        else return null;
    }
}

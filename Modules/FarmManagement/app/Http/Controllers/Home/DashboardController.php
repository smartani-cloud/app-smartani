<?php

namespace Modules\FarmManagement\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models\Finance\Finance;
use App\Models\Finance\FinanceDetail;
use App\Models\Finance\FinanceUnit;

use Modules\AgricultureMonitor\Models\Sensor;
use Modules\AgricultureMonitor\Models\SensorReading;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = 'farmmanagement';
        $this->template = 'home.';
        $this->active = 'Beranda';
        $this->route = 'dashboard';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $module = $this->module;
        $active = $this->active;
        $route = $this->route;
        
        $sensors = Sensor::has('datas')->get();

        return view($this->module.'::'.$this->template.$route.'-index', compact('module','active','route','sensors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public static function getFinance()
    {
        $month = Carbon::now('Asia/Jakarta')->format('m');
        $year = Carbon::now('Asia/Jakarta')->format('y');
        $finance = Finance::where([
            'month' => $month,
            'year' => $year
        ])->first();

        if(!$finance){
            $finance = new Finance();
            $finance->month = $month;
            $finance->year = $year;
            $finance->save();

            $finance->fresh();
        }

        return $finance;
    }
}

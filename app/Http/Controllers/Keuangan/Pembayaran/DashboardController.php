<?php

namespace App\Http\Controllers\Keuangan\Pembayaran;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\SppPlan;
use Illuminate\Http\Request;
use stdClass;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('keuangan.pembayaran.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboardBms()
    {

        return view('keuangan.pembayaran.bms.dashboard');
    }

    public function bmsData(Request $request)
    {
        
        $unit_id = $request->unit_id;

        $lists = BmsPlan::where('unit_id', $unit_id)->whereBetween('academic_year_id', [$request->year_start, $request->year_end])->orderBy('academic_year_id', 'desc')->get();
        $total = [];
        $get = [];
        $selisih = [];
        $total_student = [];
        $student_remain = [];
        $label = [];

        $show_total = 0;
        $show_get = 0;
        $show_selisih = 0;
        $show_total_student = 0;
        $show_student_remain = 0;

        foreach($lists as $index => $list){

            if($index < 10){
                array_push($total, $list->total_plan);
                array_push($get, $list->total_get);
                array_push($total_student, $list->total_student);
                array_push($student_remain, $list->student_remain);
                array_push($selisih, $list->total_plan - $list->total_get);
                array_push($label, $list->academicYear->academic_year);

                $show_total += $list->total_plan;
                $show_get += $list->total_get;
                $show_selisih += $list->total_plan - $list->total_get;
                $show_total_student += $list->total_student;
                $show_student_remain += $list->student_remain;
            }

        }

        $data = new stdClass();
        $data->total = array_reverse($total);
        $data->get = array_reverse($get);
        $data->selisih = array_reverse($selisih);
        $data->total_student = array_reverse($total_student);
        $data->student_remain = array_reverse($student_remain);
        $data->label = array_reverse($label);
        
        $data->show_total = number_format($show_total);
        $data->show_get = number_format($show_get);
        $data->show_selisih = number_format($show_selisih);
        $data->show_total_student = number_format($show_total_student);
        $data->show_student_remain = number_format($show_student_remain);

        return response()->json($data);
        
    }

    public function dashboardSpp()
    {

        return view('keuangan.pembayaran.spp.dashboard');
    }

    public function sppData(Request $request)
    {
        $month = getMonthNow();
        $year = getYearNow();
        // return $request;
        $unit_id = $request->unit_id;
        $month = $request->month_end;
        $year = $request->year_end;
        $month_start = $request->month_start;
        $year_start = $request->year_start;

        $total = [];
        $get = [];
        $selisih = [];
        $total_student = [];
        $student_remain = [];
        $label = [];

        $show_total = 0;
        $show_get = 0;
        $show_selisih = 0;
        $show_total_student = 0;
        $show_student_remain = 0;

        while($year >= $year_start){

            while($month > 0){
                if($year > $year_start){
                    $plan = SppPlan::where('month', $month)->where('year',$year)->where('unit_id',$unit_id)->first();
                    
                    if($plan){
                        array_push($total, $plan->total_plan);
                        array_push($get, $plan->total_get);
                        array_push($selisih, $plan->total_plan - $plan->total_get);
                        array_push($total_student, $plan->total_student);
                        array_push($student_remain, $plan->student_remain);
                        array_push($label, $month.'-'.$year);

                        $show_total += $plan->total_plan;
                        $show_get += $plan->total_get;
                        $show_selisih += $plan->total_plan - $plan->total_get;
                        $show_total_student += $plan->total_student;
                        $show_student_remain += $plan->student_remain;
                    }
                }else{
                    if($month >= $month_start){
                        $plan = SppPlan::where('month', $month)->where('year',$year)->where('unit_id',$unit_id)->first();
                        
                        if($plan){
                            array_push($total, $plan->total_plan);
                            array_push($get, $plan->total_get);
                            array_push($total_student, $plan->total_student);
                            array_push($student_remain, $plan->student_remain);
                            array_push($selisih, $plan->total_plan - $plan->total_get);
                            array_push($label, $month.'-'.$year);

                            $show_total += $plan->total_plan;
                            $show_get += $plan->total_get;
                            $show_selisih += $plan->total_plan - $plan->total_get;
                            $show_total_student += $plan->total_student;
                            $show_student_remain += $plan->student_remain;
                        }
                    }
                }
                $month--;
            }

            if($month == 0){
                $month = 12;
                $year -= 1;
            }
        }

        $data = new stdClass();
        $data->total = array_reverse($total);
        $data->get = array_reverse($get);
        $data->selisih = array_reverse($selisih);
        $data->total_student = array_reverse($total_student);
        $data->student_remain = array_reverse($student_remain);
        $data->label = array_reverse($label);
        
        $data->show_total = number_format($show_total);
        $data->show_get = number_format($show_get);
        $data->show_selisih = number_format($show_selisih);
        $data->show_total_student = number_format($show_total_student);
        $data->show_student_remain = number_format($show_student_remain);

        return response()->json($data);
    }

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
}

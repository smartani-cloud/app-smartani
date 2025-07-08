<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Spp\LaporanSppSiswaCollection;
use App\Http\Services\Generator\SppGenerator;
use App\Http\Services\Keuangan\SppDeductionService;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\SppBill;
use App\Models\Pembayaran\SppDeduction;
use App\Models\Pembayaran\SppPlan;
use App\Models\Siswa\Siswa;
use App\Models\Level;

use Session;
use Jenssegers\Date\Date;

class LaporanSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Laporan SPP Siswa';
        $this->route = 'spp.laporan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        
        $year = date("Y");
        $month = date("m");
        if($request->user()->pegawai->unit_id == 5 && $request->unit_id){
            $unit_id = $request->unit_id;
        }else{
            $unit_id = auth()->user()->pegawai->unit_id;
        }
        $unit = 'Semua';
        $plan = SppPlan::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->first();
        $deductions = SppDeduction::orderBy('name')->get();

        // Use Academic Year

        $year = $request->year;
        $years = $academicYears = null;
        $isYear = false;

        $queryData = SppBill::query();

        if($queryData->count() > 0){
            $years = clone $queryData;
            $yearsCount = $years->whereNotNull('year')->count();
            $years = $years->whereNotNull('year')->orderBy('year')->pluck('year')->unique();

            $academicYears = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end')->where('academic_year_start','>=',$years->min())->where('academic_year_end','<=',$years->max())->orderBy('academic_year')->get();
        }

        $tahunPelajaran = TahunAjaran::where('is_active',1)->latest()->take(1)->get();

        if($academicYears && $academicYears->count() > 0){
            $tahunPelajaran = TahunAjaran::where(function($q)use($academicYears){
                $q->where(function($q){
                    $q->where('is_active',1);
                })->orWhere(function($q)use($academicYears){
                    $q->whereIn('id',$academicYears);
                });
            })->orderBy('created_at')->get();
        }

        if(!$isYear){
            if($year){
                $year = str_replace("-","/",$year);
                $year = TahunAjaran::where('academic_year',$year)->first();
            }
            else{
                // Default Value
                $year = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$year) return redirect()->route($this->route.'.index');
        }
        else{
            // Default Value
            if(!$year){
                if($yearsCount > 0){
                    $year = $years->last();
                }
                else{
                    $year = Date::now('Asia/Jakarta')->format('Y');
                }
            }
            else{
                if($yearsCount > 0){
                    if(!in_array($year,$years->toArray())) $year = null;
                }
                else{
                    if($year != Date::now('Asia/Jakarta')->format('Y')) $year = null;
                }
            }
            if(!$year){
                return redirect()->route($this->route.'.index');
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','year','month','unit_id','unit','deductions','years','academicYears','tahunPelajaran','isYear'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGet(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $unit_id = $request->unit_id;
        $level_id = $request->level_id;

        $datas = null;

        $year = str_replace("-","/",$year);
        $year = TahunAjaran::select('id','academic_year_start','academic_year_end')->where('academic_year',$year)->first();
        
        if($year){
            $datas = SppBill::when($level_id, function($q, $level_id){
                return $q->where('level_id', $level_id);
            })
            ->when($month, function($q)use($month,$year){
                return $q->where('month', $month)->when($month >= 7, function($q)use($year){
                    return $q->where('year',$year->academic_year_start);
                }, function($q)use($year){
                    return $q->where('year',$year->academic_year_end);
                });
            }, function($q)use($year){
                 return $q->where(function($q)use($year){
                    $q->where(function($q)use($year){
                        $q->where('year',$year->academic_year_start)->where('month', '>=', 7);
                    })->orWhere(function($q)use($year){
                        $q->where('year',$year->academic_year_end)->where('month', '<=', 6);
                    });
                });
            });

            if($request->user()->pegawai->unit_id == 5){
                $datas = $datas->where('unit_id',$unit_id);
            }else{
                $datas = $datas->where('unit_id',$request->user()->pegawai->unit_id);
            }

            $datas = $datas->get();
        }

        $resource = new LaporanSppSiswaCollection($datas);

        return response()->json([$resource],200);
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
    public function destroy(Request $request,$id)
    {
        $sppBill = SppBill::find($request->id);

        if($sppBill){
            $name = $sppBill->siswa && $sppBill->siswa->identitas ? $sppBill->siswa->identitas->student_name : 'siswa';
            if($sppBill->spp){
                $spp = $sppBill->spp;
                $spp->saldo += $sppBill->spp_paid;
                $spp->total -= $sppBill->spp_nominal;
                $spp->deduction -= $sppBill->deduction_nominal;
                $spp->remain -= $sppBill->sppRemain;
                $spp->paid -= $sppBill->spp_paid;
                $spp->save();
            }
            $sppBill->delete();

            Session::flash('success','Data SPP '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Store newly created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function set(Request $request)
    {
        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';

        if($unit_id == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }

        if($request->user()->pegawai->unit_id == 5 && $request->unit_id){
            $unit_id = $request->unit_id;
        }

        $year = $request->year;
        $month = $request->month;
        $level = $request->level;

        $tahun = str_replace("-","/",$year);
        $tahun = TahunAjaran::select('id','academic_year_start','academic_year_end')->where('academic_year',$tahun)->first();

        $year = $month >= 7 ? $tahun->academic_year_start : $tahun->academic_year_end;

        $plan = SppPlan::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->first();
        $nominal = str_replace('.','',$request->spp);
        
        $students = Siswa::where('level_id',$level)->where('is_lulus',0)->where('year_spp','<=',$year)->get();
        $student_bills = Siswa::where('level_id',$level)->where('is_lulus',0)->where('year_spp','<=',$year)->whereHas('sppBill',function($q)use($request){
            $q->where('level_id', $request->level)->where('month',$request->month)->where('year',$request->year);
        })->count();
        $changeAll = $students && count($students) > 0 && ($student_bills >= count($students)) ? true : false;
        
        // $student_bills = 0;
        // $changeAll = false;
        // if($students && count($students) > 0){
        //     foreach($students as $s){
        //         if($s->sppBill()->where('level_id', $request->level)->where('month',$request->month)->where('year',$request->year)->count() > 0){
        //             $student_bills++;
        //         }
        //     }
        //     $changeAll = $student_bills >= count($students) ? true : false;
        // }

        foreach($students as $index => $student){
            $sppBill = SppBill::where('unit_id', $student->unit_id)->where('month',$month)->where('year',$year)->where('student_id',$student->id)->first();
            // if($index > 2)dd($sppBill);
            if(!$sppBill){
                $selected = false;
                if($student->year_spp < intval($year)){
                    $selected = true;
                    // if($student->id == 1879)dd($student->year_spp,$year);
                }elseif($student->month_spp <= intval($month)){
                    // if($student->id == 1879)dd($student->month_spp,$month);
                    $selected = true;
                }

                if($selected == true){
                    $sppDeduction = null;
                    $deduction = 0;
                    $employeeParent = Siswa::where('id',$student->id)->has('identitas.orangtua.pegawai.statusPegawai')->first();
                    if($employeeParent){
                        $sppDeduction = SppDeduction::whereNotNull('percentage')->where('employee_status_id',$employeeParent->identitas->orangtua->pegawai->employee_status_id)->latest()->first();
                        if($sppDeduction){
                            if($sppDeduction->percentage){
                                $deduction = ($sppDeduction->percentage/100)*$nominal;
                            }
                            else{
                                $deduction = $sppDeduction->nominal;
                            }
                        }
                    }
                
                    $spp_add_bill = SppBill::create([
                        'unit_id' => $student->unit_id,
                        'level_id' => $level,
                        'student_id'  => $student->id,
                        'month' => $month,
                        'year' => $year,
                        'spp_nominal' => $nominal,
                        'deduction_nominal' => $deduction,
                    ]);

                    $spp_student = Spp::where('student_id', $student->id)->first();
                    if(!$spp_student){
                        $spp_student = Spp::create([
                            'unit_id' => $student->unit_id,
                            'student_id' => $student->id,
                            'saldo' => 0,
                            'total' => 0,
                            'deduction' => 0,
                            'remain' => 0,
                            'paid' => 0,
                        ]);
                    }
                    $spp_student->total += $nominal;
                    $spp_student->deduction += $deduction;
                    $plan = SppPlan::where('unit_id',$student->unit_id)->where('month',$month)->where('year',$year)->first();

                    if(!$plan){
                        $plan = SppPlan::create([
                            'unit_id' => $student->unit_id,
                            'month' => $month,
                            'year' => $year,
                            'total_plan' => $nominal-$deduction,
                            'total_student' => 1,
                            'student_remain' => 1,
                        ]);
                    }else{
                        $plan->total_plan += $nominal-$deduction;
                        $plan->total_student += 1;
                        $plan->student_remain += 1;
                        $plan->percent = ($plan->student_remain / $plan->total_student) * 100;
                    }

                    if($spp_student->saldo == 0){
                        $spp_student->remain = $spp_student->remain+$nominal-$deduction;

                        $plan->remain = $plan->remain + $nominal - $deduction;
                    }
                    elseif($spp_student->saldo < ($nominal-$deduction)){
                        $plan->remain = $plan->remain + ($nominal - $deduction - $spp_student->saldo);
                        $plan->total_get = $plan->total_get + $spp_student->saldo;

                        $spp_add_bill->spp_paid = $spp_student->saldo;

                        $spp_student->remain = $nominal-$deduction-$spp_student->saldo;
                        $spp_student->paid += $spp_student->saldo;
                        $spp_student->saldo = 0;
                    }
                    else{
                        $spp_student->saldo -= $nominal+$deduction;
                        $spp_student->paid += $nominal-$deduction;
                        $spp_add_bill->spp_paid = $nominal-$deduction;
                        $spp_add_bill->status = 1;

                        $plan->total_get = $plan->total_get + $nominal - $deduction;
                        $plan->remain = $plan->remain - ($spp_student->saldo-$nominal+$deduction);
                        $plan->student_remain -= 1;
                        $plan->percent = ($plan->student_remain / $plan->total_student) * 100;
                    }
                    $spp_add_bill->spp_id = $spp_student->id;
                    if($sppDeduction)
                        $spp_add_bill->deduction_id = $sppDeduction->id;
                    $spp_add_bill->save();
                    $spp_student->save();
                    $plan->save(); 
                    
                }
            }
            elseif($changeAll){
                $deduction_diff = $deduction = 0;
                $checkBills = false;

                $sppDeduction = null;
                $employeeParent = Siswa::where('id',$student->id)->has('identitas.orangtua.pegawai.statusPegawai')->first();
                if($employeeParent){
                    $sppDeduction = SppDeduction::where('employee_status_id',$employeeParent->identitas->orangtua->pegawai->employee_status_id)->latest()->first();
                    if($sppDeduction){
                        if($sppDeduction->percentage){
                            $deduction = ($sppDeduction->percentage/100)*$nominal;
                        }
                        else{
                            $deduction = $sppDeduction->nominal;
                        }
                        $deduction_diff = $deduction-$sppBill->deduction_nominal;
                        $sppBill->deduction_nominal = $deduction;
                    }
                }
                else{
                    $sppDeduction = $sppBill->potongan;
                    if($sppDeduction){
                        if($sppDeduction->employee_status_id){
                            $sppDeduction = $sppBill->deduction_id = null;
                            $deduction = 0;
                        }
                        elseif($sppDeduction->percentage){
                            $deduction = ($sppDeduction->percentage/100)*$nominal;
                        }
                        else{
                            $deduction = $sppDeduction->nominal;
                        }                        
                        if($nominal - $deduction < 0){
                            $deduction = $nominal;
                        }
                        $deduction_diff = $deduction-$sppBill->deduction_nominal;
                        $sppBill->deduction_nominal = $deduction;
                    }
                }

                $spp_diff = $nominal-$sppBill->spp_nominal;
                $sppBill->spp_nominal = $nominal;

                $spp_student = Spp::where(['student_id' => $sppBill->student_id,'unit_id' => $sppBill->unit_id])->first();
                $spp_student->total += $spp_diff;
                $spp_student->deduction += $deduction_diff;

                $balanceInPaid = $spp_student->bills()->select('spp_paid')->where(function($q)use($sppBill){
                    $q->where('year','>',$sppBill->year)->orWhere(function($q)use($sppBill){
                        $q->where('year',$sppBill->year)->where('month','>',date('m'));
                    });
                })->count('spp_paid');

                $net_diff = $spp_diff-$deduction_diff;

                if($balanceInPaid > 0){
                    if($net_diff - $spp_student->saldo > 0){
                        $spp_student->saldo += $balanceInPaid;
                        $spp_student->remain += $balanceInPaid;
                        $spp_student->paid -= $balanceInPaid;
                        $spp_student->save();
                        $spp_student->fresh();
                        $spp_student->bills()->where(function($q)use($sppBill){
                            $q->where('year','>',$sppBill->year)->orWhere(function($q)use($sppBill){
                                $q->where('year',$sppBill->year)->where('month','>',$sppBill->month);
                            });
                        })->update([
                            'spp_paid' => 0,
                            'status' => 0,
                        ]);
                    }
                }

                $plan = SppPlan::where('unit_id',$student->unit_id)->where('month',$month)->where('year',$year)->first();
                $plan->total_plan += $net_diff;
                if($spp_student->saldo == 0){
                    if($net_diff >= 0){
                        $spp_student->remain += $net_diff;
                        $plan->remain += $net_diff;
                    }
                    else{
                        if($spp_student->remain + $net_diff >= 0){
                            // Tested
                            if($plan->remain + $net_diff < 0){
                                $plan->total_get += $spp_student->remain + $net_diff;
                            }
                            $spp_student->remain += $net_diff;
                            if($sppBill->spp_paid > $sppBill->spp_nominal-$sppBill->deduction_nominal){
                                $spp_student->saldo += $sppBill->spp_paid-($sppBill->spp_nominal-$sppBill->deduction_nominal);
                                $sppBill->spp_paid = $sppBill->spp_nominal-$sppBill->deduction_nominal;
                                $checkBills = true;
                            }
                        }
                        else{
                            // Tested
                            if($spp_student->paid + $spp_student->remain + $net_diff >= 0){
                                $spp_student->saldo -= $spp_student->remain + $net_diff;
                            }
                            else{
                                $spp_student->saldo += $spp_student->paid;
                            }
                            if($sppBill->spp_paid + $spp_student->remain + $net_diff >= 0){
                                $spp_student->paid += $spp_student->remain + $net_diff;
                                $sppBill->spp_paid += $spp_student->remain + $net_diff;
                                if($sppBill->spp_paid > $sppBill->spp_nominal-$sppBill->deduction_nominal){
                                    $spp_student->saldo += $sppBill->spp_paid-($sppBill->spp_nominal-$sppBill->deduction_nominal);
                                    $sppBill->spp_paid = $sppBill->spp_nominal-$sppBill->deduction_nominal;
                                }
                            }
                            else{
                                $spp_student->paid += $spp_student->remain;
                                $sppBill->spp_paid = 0;
                            }
                            $checkBills = true;
                            $remain = $spp_student->remain;
                            if($plan->remain + $net_diff < 0){
                                $plan->total_get += $spp_student->remain + $net_diff;
                            }
                            $spp_student->remain = 0;
                        }

                        if($plan->remain + $net_diff >= 0){
                            $plan->remain += $net_diff;
                        }
                        else{
                            $plan->remain = 0;
                        }
                    }
                }
                elseif($spp_student->saldo < $net_diff){
                    // Tested
                    $plan->remain += $net_diff - $spp_student->saldo;
                    $plan->total_get += $spp_student->saldo;

                    $sppBill->spp_paid += $spp_student->saldo;

                    $spp_student->remain += $net_diff - $spp_student->saldo;
                    $spp_student->paid += $spp_student->saldo;
                    $spp_student->saldo = 0;
                }
                else{
                    $remain = $spp_student->remain + $net_diff - $spp_student->saldo;
                    if($remain >= 0){
                        // Tested
                        if($remain == 0){
                            $spp_student->paid += $spp_student->saldo;
                            $sppBill->spp_paid += $spp_student->saldo;
                            $spp_student->saldo = 0;
                        }
                        else{                            
                            $spp_student->paid += $spp_student->saldo;
                            if($sppBill->spp_paid + $spp_student->saldo > $sppBill->spp_nominal - $sppBill->deduction_nominal){
                                $sppBill->spp_paid = $sppBill->spp_nominal - $sppBill->deduction_nominal;
                            }
                            else{
                                $sppBill->spp_paid += $spp_student->saldo;
                            }
                            $spp_student->saldo -= $net_diff;
                            $checkBills = true;
                        }
                        $spp_student->remain = $remain;
                    }
                    else{
                        // Tested
                        $spp_student->remain = 0;
                        $spp_student->paid += $spp_student->remain + $net_diff;
                        $spp_student->saldo -= $spp_student->remain + $net_diff;
                        $checkBills = true;
                        $sppBill->spp_paid += $spp_student->remain + $net_diff;
                    }

                    if($plan->remain + $net_diff - $spp_student->saldo >= 0){
                        $plan->remain += $net_diff - $spp_student->saldo;
                    }
                    else{
                        $plan->remain = 0;
                    }
                    $plan->total_get += $plan->remain + $net_diff - $spp_student->saldo;
                }

                if($sppBill->sppRemain <= 0 && $sppBill->status == 0){
                    $plan->student_remain -= 1;
                    $sppBill->status = 1;
                }
                elseif($sppBill->sppRemain > 0 && $sppBill->status == 1){
                    $plan->student_remain += 1;
                    $sppBill->status = 0;
                }
                if($sppDeduction)
                    $sppBill->deduction_id = $sppDeduction->id;
                $sppBill->save();
                $spp_student->save();
                $plan->percent = ($plan->student_remain / $plan->total_student) * 100;
                $plan->save();

                if($checkBills){
                    $sppBill->fresh();
                    $spp_student->fresh();
                    $sisa = SppGenerator::monthlyPaid($sppBill->student_id,$spp_student->saldo);
                    $spp_student->saldo = $sisa;
                    $spp_student->save();
                }
            }
        }

        if($month=='semua' && $level=='semua'){
            $datas = SppBill::where('year',$year)->where('unit_id',$unit_id)->get();
        }else if($level=='semua'){
            $datas = SppBill::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->get();
        }else if($month=='semua'){
            $datas = SppBill::where('year',$year)->where('level_id',$level)->where('unit_id',$unit_id)->get();
        }else{
            $datas = SppBill::where('year',$year)->where('month',$month)->where('level_id',$level)->get();
        }

        return redirect()->route($this->route.'.index')->with('success','Nominal SPP siswa berhasil diatur sekaligus');
    }

    /**
     * Deduct the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deduct(Request $request)
    {
        $sppBill = SppBill::find($request->id);

        if($sppBill && $sppBill->siswa && $request->nominal){
            $student = $sppBill->siswa;
            $name = $student->identitas ? $student->identitas->student_name : 'siswa' ;
            
            $nominal = str_replace('.','',$request->nominal);
            $deduction_diff = $deduction = 0;
            $checkBills = false;

            $sppDeduction = SppDeduction::find($request->potongan);
            if($sppDeduction){
                if($sppDeduction->percentage){
                    $deduction = ($sppDeduction->percentage/100)*$nominal;
                }
                else{
                    $deduction = $sppDeduction->nominal;
                }
                if($nominal - $deduction < 0){
                    $deduction = $nominal;
                }
            }
            else{
                $sppDeduction = $sppBill->deduction_id = null;
                $deduction = 0;
            }

            $deduction_diff = $deduction-$sppBill->deduction_nominal;
            $sppBill->deduction_nominal = $deduction;

            $spp_diff = $nominal-$sppBill->spp_nominal;
            $sppBill->spp_nominal = $nominal;

            $spp_student = Spp::where(['student_id' => $sppBill->student_id,'unit_id' => $sppBill->unit_id])->first();
            $spp_student->total += $spp_diff;
            $spp_student->deduction += $deduction_diff;

            $balanceInPaid = $spp_student->bills()->select('spp_paid')->where(function($q)use($sppBill){
                $q->where('year','>',$sppBill->year)->orWhere(function($q)use($sppBill){
                    $q->where('year',$sppBill->year)->where('month','>',$sppBill->month);
                });
            })->sum('spp_paid');

            $net_diff = $spp_diff-$deduction_diff;

            if($balanceInPaid > 0){
                if($net_diff - $spp_student->saldo > 0){
                    $spp_student->saldo += $balanceInPaid;
                    $spp_student->remain += $balanceInPaid;
                    $spp_student->paid -= $balanceInPaid;
                    $spp_student->save();
                    $spp_student->fresh();
                    $spp_student->bills()->where(function($q)use($sppBill){
                        $q->where('year','>',$sppBill->year)->orWhere(function($q)use($sppBill){
                            $q->where('year',$sppBill->year)->where('month','>',$sppBill->month);
                        });
                    })->update([
                        'spp_paid' => 0,
                        'status' => 0,
                    ]);
                }
            }

            $plan = SppPlan::where('unit_id',$student->unit_id)->where('month',$sppBill->month)->where('year',$sppBill->year)->first();
            if(!$plan){
                $plan = SppPlan::create([
                    'unit_id' => $student->unit_id,
                    'month' => $sppBill->month,
                    'year' => $sppBill->year,
                    'total_plan' => $nominal-$deduction,
                    'total_student' => 1,
                    'student_remain' => 1,
                ]);
            }
            $plan->total_plan += $net_diff;
            if($spp_student->saldo == 0){
                if($net_diff >= 0){
                    // Tested
                    $spp_student->remain += $net_diff;
                    $plan->remain += $net_diff;
                }
                else{
                    if($spp_student->remain + $net_diff >= 0){
                        // Tested
                        if($plan->remain + $net_diff < 0){
                            $plan->total_get += $spp_student->remain + $net_diff;
                        }
                        $spp_student->remain += $net_diff;
                        if($sppBill->spp_paid > $sppBill->spp_nominal-$sppBill->deduction_nominal){
                            $spp_student->saldo += $sppBill->spp_paid-($sppBill->spp_nominal-$sppBill->deduction_nominal);
                            $sppBill->spp_paid = $sppBill->spp_nominal-$sppBill->deduction_nominal;
                            $checkBills = true;
                        }
                    }
                    else{
                        // Tested
                        if($spp_student->paid + $spp_student->remain + $net_diff >= 0){
                            $spp_student->saldo -= $spp_student->remain + $net_diff;
                        }
                        else{
                            $spp_student->saldo += $spp_student->paid;
                        }
                        if($sppBill->spp_paid + $spp_student->remain + $net_diff >= 0){
                            $spp_student->paid += $spp_student->remain + $net_diff;
                            $sppBill->spp_paid += $spp_student->remain + $net_diff;
                            if($sppBill->spp_paid > $sppBill->spp_nominal-$sppBill->deduction_nominal){
                                $spp_student->saldo += $sppBill->spp_paid-($sppBill->spp_nominal-$sppBill->deduction_nominal);
                                $sppBill->spp_paid = $sppBill->spp_nominal-$sppBill->deduction_nominal;
                            }
                        }
                        else{
                            $spp_student->paid += $spp_student->remain;
                            $sppBill->spp_paid = 0;
                        }
                        $checkBills = true;
                        $remain = $spp_student->remain;
                        if($plan->remain + $net_diff < 0){
                            $plan->total_get += $spp_student->remain + $net_diff;
                        }
                        $spp_student->remain = 0;
                    }

                    if($plan->remain + $net_diff >= 0){
                        $plan->remain += $net_diff;
                    }
                    else{
                        $plan->remain = 0;
                    }
                }
            }
            elseif($spp_student->saldo < $net_diff){
                // Tested
                $plan->remain += $net_diff - $spp_student->saldo;
                $plan->total_get += $spp_student->saldo;

                $sppBill->spp_paid += $spp_student->saldo;

                $spp_student->remain += $net_diff - $spp_student->saldo;
                $spp_student->paid += $spp_student->saldo;
                $spp_student->saldo = 0;
            }
            else{
                $remain = $spp_student->remain + $net_diff - $spp_student->saldo;
                if($remain >= 0){
                    // Tested
                    if($remain == 0){
                        $spp_student->paid += $spp_student->saldo;
                        $sppBill->spp_paid += $spp_student->saldo;
                        $spp_student->saldo = 0;
                    }
                    else{
                        $spp_student->paid += $spp_student->saldo;
                        if($sppBill->spp_paid + $spp_student->saldo > $sppBill->spp_nominal - $sppBill->deduction_nominal){
                            $sppBill->spp_paid = $sppBill->spp_nominal - $sppBill->deduction_nominal;
                        }
                        else{
                            $sppBill->spp_paid += $spp_student->saldo;
                        }
                        $spp_student->saldo -= $net_diff;
                        $checkBills = true;
                    }
                    $spp_student->remain = $remain;
                }
                else{
                    // Tested
                    $spp_student->remain = 0;
                    $spp_student->paid += $spp_student->remain + $net_diff;
                    $spp_student->saldo -= $spp_student->remain + $net_diff;
                    $checkBills = true;
                    $sppBill->spp_paid += $spp_student->remain + $net_diff;
                }

                if($plan->remain + $net_diff - $spp_student->saldo >= 0){
                    $plan->remain += $net_diff - $spp_student->saldo;
                }
                else{
                    $plan->remain = 0;
                }
                $plan->total_get += $plan->remain + $net_diff - $spp_student->saldo;
            }

            if($sppBill->sppRemain <= 0 && $sppBill->status == 0){
                $plan->student_remain -= 1;
                $sppBill->status = 1;
            }
            elseif($sppBill->sppRemain > 0 && $sppBill->status == 1){
                $plan->student_remain += 1;
                $sppBill->status = 0;
            }
            if($sppDeduction)
                $sppBill->deduction_id = $sppDeduction->id;
            $sppBill->save();
            $spp_student->save();
            $plan->percent = ($plan->student_remain / $plan->total_student) * 100;
            $plan->save();

            if($checkBills){
                $sppBill->fresh();
                $spp_student->fresh();
                $sisa = SppGenerator::monthlyPaid($sppBill->student_id,$spp_student->saldo);
                $spp_student->saldo = $sisa;
                $spp_student->save();
            }

            //SppDeductionService::create($request);

            return redirect()->back()->with('success','Nominal SPP '.$name.' berhasil diatur');
        }
        else return redirect()->back()->with('danger','Nominal SPP siswa gagal diatur');
    }
}

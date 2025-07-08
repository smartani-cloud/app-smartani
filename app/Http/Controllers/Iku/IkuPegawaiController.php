<?php

namespace App\Http\Controllers\Iku;

use App\Http\Controllers\Controller;

use File;
use Session;
use Jenssegers\Date\Date;

use App\Models\Iku\IkuAchievement;
use App\Models\Iku\IkuAchievementDetail;
use App\Models\Iku\IkuCategory;
use App\Models\Iku\IkuIndicator;
use App\Models\Kbm\TahunAjaran;
use App\Models\Psc\PscGradeSet;
use App\Models\Unit;

use Illuminate\Http\Request;

class IkuPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $unit = null)
    {
        $role = $request->user()->role->name;

        $isTopManagements = in_array($role,['pembinayys','ketuayys','direktur']) ? true : false;

        if($isTopManagements || (!$isTopManagements && in_array($role,['kepsek','etl']))){
            if($tahun){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('iku.pegawai.index');
            $tahunPelajaran = TahunAjaran::orderBy('created_at')->get();

            $datasets = null;

            if(in_array($role,['kepsek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            $unitList = Unit::whereHas('nilaiPsc',function($q)use($tahun){
                $q->where([
                    'academic_year_id' => $tahun->id,
                    'acc_status_id' => 1,
                ])->whereNotNull(['grade_name','acc_employee_id','acc_time']);
            })->get();

            if($unit){
                if(in_array($role,['kepsek'])){
                    $unitAktif = Unit::where('name','LIKE',$unit)->first();
                }
                else{
                    $unitAktif = $unitList->where('name','LIKE',$unit)->first();
                }

                if($unitAktif){
                    $selectedScores = ['A','C+','C'];
                    $selectedScores = collect($selectedScores)->unique()->flatten()->all();

                    $set = PscGradeSet::select('id')->aktif()->latest()->first();

                    $nilai = $unitAktif->nilaiPsc()->whereIn('grade_name',$selectedScores)->where([
                        'academic_year_id' => $tahun->id,
                        'acc_status_id' => 1,
                    ])->whereNotNull(['acc_employee_id','acc_time'])->orderBy('grade_name')->get();

                    $nilaiQuery = $unitAktif->nilaiPsc()->where([
                        'academic_year_id' => $tahun->id,
                        'acc_status_id' => 1,
                    ])->whereNotNull(['acc_employee_id','acc_time']);

                    $num = 12;
                    $skippedNum = ['24','25','26','28','29','30','31','36','37','38','39','41','47'];

                    $items = ['PT','PTT','Struktural'];
                    foreach($items as $i){
                        $dataArr = null;
                        foreach($set->gradeSorted as $p){
                            if(!$dataArr)
                                $dataArr = array();
                            $thisGrade = clone $nilaiQuery;
                            if($i == 'PT')
                                $dataArr[] = $thisGrade->where('grade_name',$p)->whereHas('pegawai.statusPegawai',function($q){
                                    $q->where('code','01');
                                })->count();
                            elseif($i == 'PTT')
                                $dataArr[] = $thisGrade->where('grade_name',$p)->whereHas('pegawai.statusPegawai',function($q){
                                    $q->where('code','LIKE','02%')->whereRaw('LENGTH(code) = 5');
                                })->count();
                            elseif($i == 'Struktural')
                                $dataArr[] = $thisGrade->where('grade_name',$p)->whereHas('pegawai.units',function($q)use($unitAktif){
                                    $q->where('unit_id',$unitAktif->id)->whereHas('jabatans.kategoriPenempatan',function($q){
                                        $q->where('placement','Struktural');
                                    });
                                })->count();
                        }
                        if(!$datasets){
                            $datasets = collect([
                                [   
                                    'label' => $i,
                                    'backgroundColor' => $this->getColor($num),
                                    'data' => $dataArr
                                ]
                            ]);
                        }
                        else{
                            $dataset = collect([
                                [   
                                    'label' => $i,
                                    'backgroundColor' => $this->getColor($num),
                                    'data' => $dataArr
                                ]
                            ]);
                            $datasets = $datasets->concat($dataset);
                        }
                        $num++;
                        while(in_array($num,$skippedNum)){
                            $num++;
                        }
                    }

                    return view('kepegawaian.pa.iku.pegawai_detail', compact('tahun','tahunPelajaran','unitAktif','selectedScores','set','datasets','nilai'));
                }
                else{
                    if(in_array($role,['kepsek','wakasek'])){
                        return redirect()->route('kepegawaian.index');
                    }
                    else return redirect()->route('iku.pegawai.index', ['tahun' => $tahun->academicYearLink]);
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route('iku.pegawai.index',['tahun' => $tahun->academicYearLink, 'unit' => $unit->name]);
                }
            }

            return view('kepegawaian.pa.iku.pegawai_index', compact('tahun','tahunPelajaran','unitList','datasets'));
        }
        else return redirect()->route('kepegawaian.index');
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
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll($id){
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
    
    /**
     * Get RGB colors.
     */
    function getColor($num) {
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return 'rgb('.
            hexdec(substr($hash, 0, 2)).','. // r
            hexdec(substr($hash, 2, 2)).','. // g
            hexdec(substr($hash, 4, 2)).')'; //b
    }
}

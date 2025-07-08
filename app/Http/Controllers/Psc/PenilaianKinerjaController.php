<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;


use App\Http\Controllers\Psc\AspekEvaluasiController;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Psc\PscGradeSet;
use App\Models\Psc\PscIndicator;
use App\Models\Psc\PscIndicatorGrader;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscRoleMapping;
use App\Models\Psc\PscScore;
use App\Models\Psc\PscScoreIndicator;
use App\Models\Psc\PscScoreIndicatorGrader;
use App\Models\Rekrutmen\PegawaiUnit;
use App\Models\Unit;

class PenilaianKinerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $unit = null)
    {
        $role = $request->user()->role->name;

        // Is a validator?
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',2);
        // Is a grader?
        $indicatorGraderQuery = $request->user()->pegawai->jabatan->penilaiPsc();

        // Check targets
        if($targetsQuery->count() > 0 || $indicatorGraderQuery->count() > 0){
            if($tahun){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('psc.penilaian.index');
            $tahunPelajaran = TahunAjaran::where('is_active',1)->orHas('nilaiPsc')->orderBy('created_at')->get();

            // Check available units
            $allUnit = null;

            $isStaticGrader = $indicatorGraderQuery->pluck('parent_id')->unique()->contains(function($value,$key){return in_array($value,[1,4]);});

            if($isStaticGrader) $allUnit = Unit::all();
            else{
                if($indicatorGraderQuery->count() > 0){
                    $targetPositions = null;
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                    if($targetsQuery->count() > 0){
                        $targetPositions = $targetsQuery->pluck('target_position_id');
                        $positions = collect($positions)->concat($targetPositions)->unique()->flatten()->all();
                    }
                }
                elseif($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                }
                if($role == 'kepsek'){
                    $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request){
                        return ($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school);
                    })->all();
                }
                else{
                    $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->all();
                }
                $allUnit = collect($allUnit);
            }

            if($unit){
                $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
                if($unitAktif){
                    if($indicatorGraderQuery->count() > 0)
                        return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                    elseif($targetsQuery->count() > 0)
                       return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                }
                else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]);
            }

            return view('kepegawaian.pa.psc.penilaian_index', compact('tahun','tahunPelajaran','allUnit'));
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

<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;

class LaporanSayaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null)
    {
        $role = $request->user()->role->name;

        $isTopManagements = in_array($role,['pembinayys','ketuayys','direktur']) ? true : false;

        if(!$isTopManagements){
            if($tahun){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('psc.laporan.saya.index');
            $tahunPelajaran = TahunAjaran::where('is_active',1)->orWhereHas('nilaiPsc',function($q)use($request){
                $q->where(['employee_id' => $request->user()->pegawai->id,'acc_status_id' => 1])->whereNotNull(['acc_employee_id','acc_time']);
            })->orderBy('created_at')->get();

            $nilai = $request->user()->pegawai->pscScore()->where([
                'academic_year_id' => $tahun->id,
                'acc_status_id' => 1,
            ])->whereNotNull(['acc_employee_id','acc_time'])->latest()->first();

            return view('kepegawaian.pa.psc.laporan_saya_index', compact('tahun','tahunPelajaran','nilai'));
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

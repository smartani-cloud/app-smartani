<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Kbm\TahunAjaran;
use Illuminate\Http\Request;

use Jenssegers\Date\Date;

class SaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $jenis = null, $tahun = null, $anggaran = null)
    {
        $role = $request->user()->role->name;

        $jenisAnggaran = JenisAnggaran::all();
        $jenisAnggaranCount = null;
        foreach($jenisAnggaran as $j){
            $anggaranCount = $j->anggaran();
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam'])){
                if($request->user()->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('unit_id',$request->user()->pegawai->unit_id);});
                }
            }
            if($j->isKso){
                $anggaranCount = $anggaranCount->whereHas('apby',function($q){$q->where('director_acc_status_id',1);})->count();
            }
            else{
                $anggaranCount = $anggaranCount->whereHas('apby',function($q){$q->where('president_acc_status_id',1);})->count();
            }
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        
        $jenisAktif = $tahunPelajaran = $apby = null;
        $isKso = false;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('saldo.index');
            if($isKso){
                $tahunPelajaran = TahunAjaran::whereHas('apby',function($q)use($jenisAktif){
                    $q->where('director_acc_status_id',1)->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){$q->where('budgeting_type_id',$jenisAktif->id);});
                })->orderBy('created_at')->get();
                
                if($request->tahun){
                    $tahun = str_replace("-","/",$request->tahun);
                    $tahun = $tahunPelajaran->where('academic_year',$tahun)->first();
                }
                else{
                    $tahun = $tahunPelajaran->sortByDesc('created_at')->first();
                }
                if(!$tahun) return redirect()->route('saldo.index');
            }
            else{
                $tahun = $tahun == null ? Date::now('Asia/Jakarta')->format('Y') : $tahun;
            }

            if($jenisAktif){
                if($isKso){
                    $apby = $tahun->apby()->select('id','year','academic_year_id','budgeting_budgeting_type_id','total_balance')->where('director_acc_status_id',1)->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                        $query->where('budgeting_type_id',$jenisAktif->id);
                    })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->aktif()->latest()->get()->sortBy('jenisAnggaranAnggaran.number');
                }
                else{
                    $apby = Apby::select('id','year','academic_year_id','budgeting_budgeting_type_id','total_balance')->where(['year' => $tahun, 'president_acc_status_id' => 1])->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                        $query->where('budgeting_type_id',$jenisAktif->id);
                    })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->aktif()->latest()->get()->sortBy('jenisAnggaranAnggaran.number');
                }

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$apby->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'))->first();
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $apbyAktif = $anggaranAktif->apby()->whereIn('id',$apby->pluck('id'))->first();

                        if($apbyAktif){
                            return view('keuangan.read-only.saldo_detail', compact('jenisAnggaran','jenisAnggaranCount','jenisAktif','tahun','tahunPelajaran','apby','isKso','anggaranAktif','apbyAktif'));
                        }
                        else{
                            if($isKso)
                                return redirect()->route('saldo.index', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink]);
                            else
                                return redirect()->route('saldo.index', ['jenis' => $jenisAktif->link, 'tahun' => $tahun]);
                        }
                    }
                    else{
                        if($isKso)
                            return redirect()->route('saldo.index', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink]);
                        else
                            return redirect()->route('saldo.index', ['jenis' => $jenisAktif->link, 'tahun' => $tahun]);
                    }
                }
            }
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv'])){
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('saldo.index', ['jenis' => $jenisAktif->link]);
            }
            else{
                return redirect()->route('keuangan.index');
            }
        }

        return view('keuangan.read-only.saldo_index', compact('jenisAnggaran','jenisAnggaranCount','jenisAktif','tahun','tahunPelajaran','apby','isKso'));
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
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function show(Apby $apby)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function edit(Apby $apby)
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
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apby $apby)
    {
        //
    }
}

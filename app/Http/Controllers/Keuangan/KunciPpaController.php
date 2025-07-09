<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\AnggaranAkun;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\KategoriAkun;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Rkat\Rkat;
use App\Models\Rkat\RkatDetail;
use App\Models\Kbm\TahunAjaran;
use App\Models\Setting;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;

class KunciPpaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $jenis = null, $tahun = null, $anggaran = null)
    {
        // Override Budget Category
        if(!$jenis) $jenis = 'apby';
        
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAnggaranCount = null;
        foreach($jenisAnggaran as $j){
            $anggaranCount = $j->anggaran();
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
                if($request->user()->pegawai->unit_id == '5'){
                    // if(in_array($role,['etl','ctl'])){
                    //     $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('acc_position_id',$request->user()->pegawai->jabatan->first()->id);});
                    // }
                    // else{
                        $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
                    // }
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('unit_id',$request->user()->pegawai->unit_id);});
                }
            }
            $anggaranCount = $anggaranCount->count();
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        
        $jenisAktif = $kategori = $rkat = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = null;
        $yearsCount = $academicYearsCount = 0;

        $editable = in_array($request->user()->role->name,['am']) ? true : false;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('kunci-ppa.index');

            $queryRkat = Rkat::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });
            
            // if(in_array($role,['etl','ctl'])){
            //     $queryRkat = $queryRkat->whereHas('jenisAnggaranAnggaran.anggaran',function($q)use($request){
            //         $q->where('acc_position_id',$request->user()->pegawai->position_id);
            //     });
            // }

            if($queryRkat->count() > 0){
                $years = clone $queryRkat;
                $yearsCount = $years->whereNotNull('year')->aktif()->count();
                $years = $years->whereNotNull('year')->aktif()->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryRkat;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->aktif()->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->aktif()->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryRkat;
                $latest = $latest->latest()->first();

                if($tahun) $isYear = strlen($tahun) == 4 ? true : false;
                else $isYear = $latest->year ? true : false;
            }
            else{
                $isYear = $jenisAktif->is_academic_year == 1 ? false : true;
            }

            $tahunPelajaran = TahunAjaran::where('is_finance_year',1)->latest()->take(1)->get();

            if($academicYearsCount > 0){
                $tahunPelajaran = TahunAjaran::where(function($q)use($academicYears){
                    $q->where(function($q){
                        $q->where('is_finance_year',1);
                    })->orWhere(function($q)use($academicYears){
                        $q->whereIn('id',$academicYears);
                    });
                })->orderBy('created_at')->get();
            }

            if(!$isYear){
                if($request->tahun){
                    $tahun = str_replace("-","/",$request->tahun);
                    $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                }
                else{
                    $tahun = TahunAjaran::where('is_finance_year',1)->latest()->first();
                }
                if(!$tahun) return redirect()->route('kunci-ppa.index');
            }
            else{
                if(!$tahun){
                    if($yearsCount > 0 && $jenisAktif->is_academic_year == 1){
                        $tahun = $years->last();
                    }
                    else{
                        $tahun = Date::now('Asia/Jakarta')->format('Y');
                    }
                }
                else{
                    if($yearsCount > 0 && $jenisAktif->is_academic_year == 1){
                        if(!in_array($tahun,$years->toArray())) $tahun = null;
                    }
                    else{
                        if($tahun != Date::now('Asia/Jakarta')->format('Y')) $tahun = null;
                    }
                }
                if(!$tahun){
                    return redirect()->route('kunci-ppa.index');
                }
            }

            if($jenisAktif){
                $kategori = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $rkat = clone $queryRkat;
                $rkat = $rkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->get();
            }
            else return redirect()->route('kunci-ppa.index');
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('kunci-ppa.index', ['jenis' => $jenisAktif->link]);
            }
            elseif($anggaranCount < 1){
                return redirect()->route('keuangan.index');
            }
        }

        return view('keuangan.read-only.kunci-ppa_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','yearAttr','isYear','rkat','years','academicYears','editable'));
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
    public function update(Request $request, $jenis, $tahun)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            // Inti Controller
            $anggarans = $jenisAktif->anggaran()->select('id')->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->get();

            if($anggarans && count($anggarans) > 0){
                $successCount = 0;
                foreach($anggarans as $a){
                    $apbyCount = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->count();
                    $history = $a->tahuns()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->first();
                    if($apbyCount > 0 && $history){
                        $inputValue = 'lock-'.$history->id;
                        $history->ppa_active = $request->{$inputValue} == 'on' ? 1 : 0;
                        $history->save();
                        $successCount++;
                    }
                }

                if($successCount > 0)
                    Session::flash('success', 'Kunci PPA '.$jenisAktif->name.' berhasil diperbarui');

                return redirect()->route('kunci-ppa.index');

                //return redirect()->route('kunci-ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else{
                Session::flash('danger', 'Kunci PPA '.$jenisAktif->name.' gagal diperbarui');

                return redirect()->route('kunci-ppa.index');

                //return redirect()->route('kunci-ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
        }

        return redirect()->route('kunci-ppa.index');
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
    
    /**
     * Check whether specified resource from storage has access.
     *
     * @return \Illuminate\Http\Response
     */
    public function hasBudgetingType($jenis, $user)
    {
        $role = $user->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAnggaranCount = null;
        foreach($jenisAnggaran as $j){
            $anggaranCount = $j->anggaran();
            if(!in_array($user->role->name,['pembinayys','ketuayys','direktur','fam','am','akunspv'])){
                if($user->pegawai->unit_id == '5'){
                    // if(in_array($role,['etl','ctl'])){
                    //     $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('acc_position_id',$user->pegawai->jabatan->first()->id);});
                    // }
                    // else{
                        $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('position_id',$user->pegawai->jabatan->group()->first()->id);});
                    // }
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('unit_id',$user->pegawai->unit_id);});
                }
            }
            $anggaranCount = $anggaranCount->count();
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
        
        return $jenisAktif;
    }
}

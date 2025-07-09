<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Bbk\Bbk;
use App\Models\Lppa\Lppa;
use App\Models\Ppa\Ppa;
use App\Models\Rkat\Rkat;
use App\Models\Kbm\TahunAjaran;
use Illuminate\Http\Request;

use Auth;
use Session;
use Jenssegers\Date\Date;

class TahunAnggaranController extends Controller
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
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am'])){
                if($request->user()->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
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
        
        $jenisAktif = $kategori = $apby = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = $checkApby = null;
        $yearsCount = $academicYearsCount = 0;
        $changeYear = $nextYear = false;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('tahun-anggaran.index');

            $queryApby = Apby::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });

            if($queryApby->count() > 0){
                $years = clone $queryApby;
                $yearsCount = $years->whereNotNull('year')->aktif()->count();
                $years = $years->whereNotNull('year')->aktif()->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryApby;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->aktif()->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->aktif()->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryApby;
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
                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                }
                else{
                    // Default Value
                    $tahun = TahunAjaran::where('is_finance_year',1)->latest()->first();
                }
                if(!$tahun) return redirect()->route('tahun-anggaran.index');
            }
            else{
                // Default Value
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
                    return redirect()->route('tahun-anggaran.index');
                }
            }

            if($jenisAktif){
                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apby = clone $queryApby;
                $apby = $apby->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->aktif()->get();

                $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                });
                $anggaranIds = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->pluck('id');
                $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
                $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

                if($isKso){
                    $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbkAcc = clone $checkBbk;
                    $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                    $checkLppa = Lppa::whereHas('ppa',function($query)use($yearAttr,$tahun,$anggaranIds){
                        $query->where([
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'director_acc_status_id' => 1
                        ])->whereIn('budgeting_budgeting_type_id',$anggaranIds);
                    });
                }
                else{
                    $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbkAcc = clone $checkBbk;
                    $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                    $checkLppa = Lppa::whereHas('ppa',function($query)use($yearAttr,$tahun,$anggaranIds){
                        $query->where([
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'director_acc_status_id' => 1
                        ])->whereIn('budgeting_budgeting_type_id',$anggaranIds);
                    });
                }
                $checkApby = $checkApby->aktif();
                $checkPpaAcc = clone $checkPpa;
                $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
                $checkLppaAcc = clone $checkLppa;
                $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);

                if(!$latest->year && !$isYear && $tahun->is_finance_year == 1){
                    $nextYearCheck = TahunAjaran::where('academic_year_start','>',$tahun->academic_year_start)->min('academic_year_start');
                    $nextYear = $nextYearCheck ? true : false;
                    if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count() && (!$latest->year && !$isYear && $tahun->is_finance_year == 1)){
                    //if($checkApby->count() > 0){
                        $changeYear = true;
                    }
                }
            }
        }

        return view('keuangan.read-only.tahun_index', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','yearAttr','isYear','checkApby','apby','years','academicYears','changeYear','nextYear'));
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
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Apby $apby)
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

    /**
     * Lock the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lock(Request $request, $jenis, $tahun)
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
                if(!$tahun) return redirect()->route('tahun-anggaran.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            // Inti Controller
            $checkRkat = Rkat::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $anggaranIds = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');
            $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
            $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            if($isKso){
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($yearAttr,$tahun,$anggaranIds){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$anggaranIds);
                });
            }
            else{
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($yearAttr,$tahun,$anggaranIds){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$anggaranIds);
                });
            }
            $checkRkat = $checkRkat->aktif();
            $checkApby = $checkApby->aktif();
            $checkPpaAcc = clone $checkPpa;
            $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
            $checkLppaAcc = clone $checkLppa;
            $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);

            if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count() && (!$isYear && $tahun->is_finance_year == 1)){
            //if($checkApby->count() > 0 && (!$isYear && $tahun->is_finance_year == 1)){
                $nextYear = TahunAjaran::where('academic_year_start','>',$tahun->academic_year_start)->min('academic_year_start');
                if($nextYear){
                    $checkRkat->update(['is_final' => 1]);
                    $checkApby->update(['is_final' => 1]);

                    $tahun->update(['is_finance_year' => 0]);
                    TahunAjaran::where('academic_year_start',$nextYear)->update(['is_finance_year' => 1]);

                    Session::flash('success', 'Tutup tahun '.$jenisAktif->name.' berhasil dilakukan');

                    return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
            }
            else{
                Session::flash('danger', 'Tutup tahun '.$jenisAktif->name.' gagal dilakukan, pastikan tidak ada PPA, PPB, maupun RPPA yang belum selesai prosesnya');

                return redirect()->route('tahun-anggaran.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
        }

        return redirect()->route('tahun-anggaran.index');
    }
}

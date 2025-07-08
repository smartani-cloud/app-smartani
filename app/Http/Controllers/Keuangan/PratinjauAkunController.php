<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\JenisAnggaranAnggaranRiwayat;
use App\Models\Anggaran\KategoriAkun;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Kbm\TahunAjaran;
use Illuminate\Http\Request;

use Jenssegers\Date\Date;

class PratinjauAkunController extends Controller
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
        
        $jenisAktif = $kategori = $history = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = null;
        $yearsCount = $academicYearsCount = 0;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('keuangan.pratinjau-akun.index');

            $queryHistory = JenisAnggaranAnggaranRiwayat::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });
            
            // if(in_array($role,['etl','ctl'])){
            //     $queryHistory = $queryHistory->whereHas('jenisAnggaranAnggaran.anggaran',function($q)use($request){
            //         $q->where('acc_position_id',$request->user()->pegawai->position_id);
            //     });
            // }

            if($queryHistory->count() > 0){
                $years = clone $queryHistory;
                $yearsCount = $years->whereNotNull('year')->count();
                $years = $years->whereNotNull('year')->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryHistory;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryHistory;
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
                if(!$tahun) return redirect()->route('keuangan.pratinjau-akun.index');
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
                    return redirect()->route('keuangan.pratinjau-akun.index');
                }
            }

            if($jenisAktif){
                $kategori = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $history = clone $queryHistory;
                $history = $history->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->get();

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                        $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                            $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                        });
                    });

                    if($request->user()->pegawai->unit_id == '5'){
                        $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->first();
                    }
                    else{
                        $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->where('unit_id',$request->user()->pegawai->unit_id)->first();
                    }
                    // if(in_array($role,['etl','ctl'])){
                    //     $anggaranAktif = $anggaranAktif->where('acc_position_id',$request->user()->pegawai->position_id);
                    //     $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->where('acc_position_id',$request->user()->pegawai->position_id)->first();
                    // }
                    // elseif(in_array($request->user()->pegawai->position_id,[20,25,34,37,48,50,53,57])){
                    if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','am','akunspv']) && !$checkAnggaran)){
                        $anggaranAktif = $anggaranAktif->whereIn('id',$history->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'));
                    }
                    $anggaranAktif = $anggaranAktif->first();

                    if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']) && !$checkAnggaran){
                        $anggaranAktif = null;
                    }

                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->has('akun')->first();
                        $historyAktif = $anggaranAktif ? $anggaranAktif->tahuns()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->first() : null;

                        if($historyAktif){
                            // Inti controller

                            $kategori = KategoriAkun::all();

                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;
                            $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);

                            return view('keuangan.read-only.pratinjau-akun_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','history','years','academicYears','anggaranAktif','historyAktif','kategori'));
                        }
                        else return redirect()->route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
                if(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
                    $anggaranAktif = $checkAnggaran = null;
                    if($request->user()->pegawai->unit_id == '5'){
                        $anggaranAktif = Anggaran::where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->whereIn('id',$history->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'))->first();
                        $checkAnggaran = Anggaran::where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->first();
                    }
                    else{
                        $anggaranAktif = Anggaran::where('unit_id',$request->user()->pegawai->unit_id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->whereIn('id',$historyAktif->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'))->first();
                        $checkAnggaran = Anggaran::where('unit_id',$request->user()->pegawai->unit_id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->first();
                    }
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            })->first();
                        $historyAktif = !$isYear ? $anggaranAktif->tahuns()->where('academic_year_id',$tahun->id)->first() : $anggaranAktif->tahuns()->where('year',$tahun)->first();

                        if(!$historyAktif){
                            $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        }
                        return redirect()->route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                    elseif($checkAnggaran){
                        $anggaranAktif = $checkAnggaran->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            })->first();
                        $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        return redirect()->route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
            }

            else return redirect()->route('keuangan.pratinjau-akun.index');
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link]);
            }
            elseif($anggaranCount < 1){
                return redirect()->route('keuangan.index');
            }
        }

        return view('keuangan.read-only.pratinjau-akun_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','yearAttr','isYear','history','years','academicYears'));
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

    /**
     * Check the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkRole($anggaran,$role){
        // Sesuai penempatan
        // $rolesCollection = collect([
        //     ['unit' => 1, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 2, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 3, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 4, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 5, 'position' => 18, 'roles' => ['etl','etm']],
        //     ['unit' => 5, 'position' => 23, 'roles' => ['ctl','ctm']],
        //     ['unit' => 5, 'position' => 28, 'roles' => ['am','fam','faspv','fas']],
        //     ['unit' => 5, 'position' => 32, 'roles' => ['am','aspv']],
        //     ['unit' => 5, 'position' => 36, 'roles' => ['am','ftm','ftspv']],
        //     ['unit' => 5, 'position' => 48, 'roles' => ['etl','ekim','ekipv']],
        //     ['unit' => 5, 'position' => 50, 'roles' => ['etl','sdmm','sdmspv']],
        //     ['unit' => 5, 'position' => 53, 'roles' => ['ctl','layspv']],
        //     ['unit' => 5, 'position' => 57, 'roles' => ['am','akunspv']]
        // ]);

        // Temp
        $rolesCollection = collect([
            ['name' => 'TKIT', 'unit' => 1, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'SDIT', 'unit' => 2, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'SMPIT', 'unit' => 3, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'SMAIT', 'unit' => 4, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'Education Team', 'unit' => 5, 'position' => 18, 'roles' => ['etl','etm']],
            ['name' => 'Customer Team', 'unit' => 5, 'position' => 23, 'roles' => ['ctl','ctm']],
            ['name' => 'Finance and Accounting', 'unit' => 5, 'position' => 28, 'roles' => ['am','fam','faspv','fas']],
            ['name' => 'Administration, Legal, and IT', 'unit' => 5, 'position' => 32, 'roles' => ['am','aspv']],
            ['name' => 'Facilities', 'unit' => 5, 'position' => 36, 'roles' => ['am','ftm','ftspv','fts']],
            ['name' => 'Divisi Edukasi', 'unit' => 5, 'position' => 18, 'roles' => ['etl']],
            ['name' => 'Divisi Layanan', 'unit' => 5, 'position' => 23, 'roles' => ['ctl']],
            ['name' => 'Divisi Umum', 'unit' => 5, 'position' => 32, 'roles' => ['am','akunspv']]
        ]);

        if($anggaran->unit_id == 5){
            $roles = $rolesCollection->where('name',$anggaran->name)->where('unit',$anggaran->unit_id)->where('position',$anggaran->position_id)->first();
        }
        else{
            $roles = $rolesCollection->where('unit',$anggaran->unit_id)->first();
        }

        if($roles && in_array($role,$roles['roles'])) return true;
        else return false;
    }
}

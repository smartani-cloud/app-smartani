<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Lppa\Lppa;
use App\Models\Ppa\Ppa;
use App\Models\Ppa\PpaDetail;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

use Auth;
use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LppaController extends Controller
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
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
                if($request->user()->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('unit_id',$request->user()->pegawai->unit_id);});
                }
            }
            $accessibleAnggaranCount = $anggaranCount->count();
            $anggaranCount = $anggaranCount->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        
        $jenisAktif = $kategori = $allPpa = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = null;
        $yearsCount = $academicYearsCount = 0;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif){
                if($jenisAnggaranCount->where('id',1)->first()['anggaranCount'] < 1){
                    return view('keuangan.read-only.lppa_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','yearAttr','allPpa','years','academicYears'));
                }
                else return redirect()->route('lppa.index');
            }

            $queryPpa = Ppa::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->where('finance_acc_status_id',1)->has('lppa');
            
            // if(in_array($role,['etl','ctl'])){
            //     $queryPpa = $queryPpa->whereHas('jenisAnggaranAnggaran.anggaran',function($q)use($request){
            //         $q->where('acc_position_id',$request->user()->pegawai->position_id);
            //     });
            // }

            if($queryPpa->count() > 0){
                $years = clone $queryPpa;
                $yearsCount = $years->whereNotNull('year')->count();
                $years = $years->whereNotNull('year')->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryPpa;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryPpa;
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
                if(!$tahun) return redirect()->route('lppa.index');
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
                    return redirect()->route('lppa.index');
                }
            }

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            $anggaranCount = $jenisAktif ? $jenisAktif->anggaran()->whereHas('ppa',function($q){$q->where('finance_acc_status_id',1)->has('lppa');})->count() : 0;

            if($anggaranCount > 0){
                $kategori = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();
                $allPpa = clone $queryPpa;
                $allPpa = $allPpa->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->get();

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                        $ppaAcc = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereNotNull('finance_acc_status_id');

                        if($ppaAcc->count() > 0 || ($ppaAcc->count() < 1 && !in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv']))){
                            $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'];
                            $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                            if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                                $lppa = Lppa::whereHas('ppa',function($query)use($anggaranAktif,$yearAttr,$tahun){
                                    $query->where([
                                        'budgeting_budgeting_type_id' => $anggaranAktif->id,
                                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                                        'finance_acc_status_id' => 1
                                    ]);
                                })->get();

                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                // if(in_array($role,['direktur','fam','faspv']))
                                //     $folder = $role;
                                // elseif($isPa) $folder = 'pa';
                                // elseif($isAnggotaPa) $folder = 'anggota-pa';
                                // else $folder = 'read-only';
                                $folder = 'read-only';

                                return view('keuangan.'.$folder.'.lppa_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','allPpa','anggaranAktif','ppaAcc','lppa','years','academicYears','isAnggotaPa','isKso'));
                            }
                        }
                        else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
                if(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
                    $anggaranAktif = null;
                    if($request->user()->pegawai->unit_id == '5'){
                        $anggaranAktif = Anggaran::where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
                    }
                    else{
                        $anggaranAktif = Anggaran::where('unit_id',$request->user()->pegawai->unit_id)->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
                    }
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $ppaAcc = !$isYear ? $anggaranAktif->ppa()->where('academic_year_id',$tahun->id)->whereNotNull('finance_acc_status_id') : $anggaranAktif->ppa()->where('year',$tahun)->whereNotNull('finance_acc_status_id');

                        if($ppaAcc->count() < 1){
                            $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        }
                        return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
            }

            else return redirect()->route('lppa.index');
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link]);
            }
            elseif($accessibleAnggaranCount < 1){
                return redirect()->route('keuangan.index');
            }
        }

        // if($jenis && $isKso)
        //     return view('keuangan.read-only.lppa_kso_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','yearAttr','allPpa','years','academicYears'));
        // else
            return view('keuangan.read-only.lppa_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','yearAttr','allPpa','years','academicYears'));
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
     * @param  \App\Models\Lppa\Lppa  $lppa
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        $anggaranCount = $jenisAktif ? $jenisAktif->anggaran()->whereHas('ppa',function($q){$q->where('finance_acc_status_id',1)->has('lppa');})->count() : 0;

        if($anggaranCount > 0){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->where('finance_acc_status_id',1)->has('lppa')->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $ppaAcc = $anggaranAktif->ppa()->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1]);

                if($ppaAcc->count() > 0){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif();

                        $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                        $lppaAktif = Lppa::where('number','LIKE',$nomor.'/%')->whereHas('ppa',function($query)use($anggaranAktif,$yearAttr,$tahun){
                            $query->where([
                                'budgeting_budgeting_type_id' => $anggaranAktif->id,
                                $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                                'finance_acc_status_id' => 1
                            ]);
                        })->first();

                        if($lppaAktif){
                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                            $editable = $lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count()) ? true : false;

                            $notifikasi = Notifikasi::where(['id' => $request->notif_id,'user_id' => $request->user()->id])->first();

                            if($notifikasi){
                                $notifikasi->update(['is_active' => 0]);
                            }

                            if(in_array($role,['am']))
                                $folder = $role;
                            elseif(in_array($role,['faspv'])) $folder = 'fam';
                            elseif(in_array($role,['fam','fas'])) $folder = 'read-only';
                            elseif($isPa) $folder = 'pa';
                            elseif($isAnggotaPa) $folder = 'anggota-pa';
                            else $folder = 'read-only';

                            return view('keuangan.'.$folder.'.lppa_show', compact('jenisAnggaran','jenisAktif','tahun','isYear','anggaranAktif','apbyAktif','lppaAktif','isPa','isAnggotaPa','isKso','editable'));
                        }
                        else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
                else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('lppa.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lppa\Lppa  $lppa
     * @return \Illuminate\Http\Response
     */
    public function edit(Lppa $lppa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lppa\Lppa  $lppa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        $anggaranCount = $jenisAktif ? $jenisAktif->anggaran()->whereHas('ppa',function($q){$q->where('finance_acc_status_id',1)->has('lppa');})->count() : 0;

        if($anggaranCount > 0){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->where('finance_acc_status_id',1)->has('lppa')->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $ppaAcc = $anggaranAktif->ppa()->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1]);

                if($ppaAcc->count() > 0){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $lppaAktif = null;

                        $lppaAktif = Lppa::where('number','LIKE',$nomor.'/%')->whereHas('ppa',function($query)use($anggaranAktif,$yearAttr,$tahun){
                            $query->where([
                                'budgeting_budgeting_type_id' => $anggaranAktif->id,
                                $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                                'finance_acc_status_id' => 1
                            ]);
                        })->first();

                        if($lppaAktif){
                            if($lppaAktif->detail()->count() > 0){
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;
                                if(in_array($role,['faspv'])){
                                    // Inti function
                                    $lppaAktifDetail = $lppaAktif->detail();

                                    if($lppaAktif->finance_acc_status_id != 1 && $lppaAktifDetail->count() > 0){
                                        $successCount = 0;

                                        $editable = $lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count()) ? true : false;

                                        if($isAnggotaPa || (!$isAnggotaPa && $editable)){
                                            $lppaAktifDetailClone = clone $lppaAktifDetail;
                                            foreach($lppaAktifDetailClone->get() as $detail){
                                                if($detail->ppaDetail->value == 0){
                                                    $detail->value = 0;
                                                    $detail->receipt_status_id = 2;
                                                }
                                                else{
                                                    $inputName = 'value-'.$detail->id;
                                                    $checkName = 'receipt-'.$detail->id;
                                                    $requestValue = (int)str_replace('.','',$request->{$inputName});
                                                    if(isset($detail->value) && ($detail->employee_id != $request->user()->pegawai->id) && $detail->value != $requestValue){
                                                        $detail->edited_employee_id = $request->user()->pegawai->id;
                                                        $detail->edited_status_id = 1;
                                                    }
                                                    $detail->value = $requestValue;
                                                    $detail->receipt_status_id = $request->{$checkName} == 'on' ? 1 : 2;
                                                }
                                                if(!isset($detail->employee_id)){
                                                    $detail->employee_id = $request->user()->pegawai->id;
                                                }
                                                if(!isset($detail->acc_employee_id)){
                                                    $detail->acc_employee_id = $request->user()->pegawai->id;
                                                }
                                                $detail->acc_status_id = 1;
                                                if(!isset($detail->acc_time)){
                                                    $detail->acc_time = Date::now('Asia/Jakarta');
                                                }
                                                $detail->save();
                                                $successCount++;
                                            }

                                            $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                                            $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                                            if(isset($request->validate) && $request->validate == 'validate' && $apbyAktif){
                                                $difference_total_value = $lppaAktif->ppa->bbk->ppa_value-$lppaAktifDetail->sum('value');
                                                // Accept LPPA
                                                $lppaAktif->update([
                                                    'difference_total_value' => $difference_total_value,
                                                    'finance_acc_id' => $request->user()->pegawai->id,
                                                    'finance_acc_status_id' => 1,
                                                    'finance_acc_time' => Date::now('Asia/Jakarta')
                                                ]);

                                                // Check if there are surplus or minus differences
                                                $surplus_difference = 0;
                                                $minus_difference = 0;
                                                foreach($lppaAktif->detail as $l){
                                                    if((($l->ppaDetail->value)-$l->value) > 0){
                                                        $surplus_difference++;
                                                    }
                                                    elseif((($l->ppaDetail->value)-$l->value) < 0){
                                                        $minus_difference++;
                                                    }
                                                }

                                                if($surplus_difference > 0){
                                                    // Add APBY
                                                    $this->addApby($lppaAktif);
                                                }
                                                if($minus_difference > 0){
                                                    // Generate PPA
                                                    $this->generatePpa($lppaAktif);
                                                }
                                            }
                                        }

                                        if($successCount > 0){
                                            if(isset($request->validate) && $request->validate == 'validate' && $apbyAktif)
                                                Session::flash('success','RPPA berhasil disimpan dan disetujui');
                                            else
                                                Session::flash('success','Perubahan data RPPA berhasil disimpan');
                                        }
                                    }
                                }
                                elseif($isPa){
                                    // Inti function
                                    $lppaAktifDetail = $lppaAktif->detail();

                                    if($lppaAktif->finance_acc_status_id != 1 && $lppaAktifDetail->count() > 0){
                                        $successCount = 0;

                                        $lppaAktifDetailClone = clone $lppaAktifDetail;
                                        foreach($lppaAktifDetailClone->get() as $detail){
                                            if($detail->ppaDetail->value == 0){
                                                $detail->value = 0;
                                                $detail->receipt_status_id = 2;
                                            }
                                            else{
                                                $inputName = 'value-'.$detail->id;
                                                $checkName = 'receipt-'.$detail->id;
                                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                                if(isset($detail->value) && ($detail->employee_id != $request->user()->pegawai->id) && $detail->value != $requestValue){
                                                    $detail->edited_employee_id = $request->user()->pegawai->id;
                                                    $detail->edited_status_id = 1;
                                                }
                                                $detail->value = $requestValue;
                                                $detail->receipt_status_id = $request->{$checkName} == 'on' ? 1 : 2;
                                            }
                                            if(!isset($detail->employee_id)){
                                                $detail->employee_id = $request->user()->pegawai->id;
                                            }
                                            if(isset($request->validate) && $request->validate == 'validate'){
                                                $detail->acc_employee_id = $request->user()->pegawai->id;
                                                $detail->acc_status_id = 1;
                                                $detail->acc_time = Date::now('Asia/Jakarta');
                                            }
                                            $detail->save();

                                            $lppaAktif->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                                            $lppaAktif->save();

                                            $successCount++;
                                        }

                                        if($successCount > 0){
                                            $lppaAktif->update(['date' => Date::now('Asia/Jakarta')->format('Y-m-d')]);
                                            Session::flash('success','Perubahan data RPPA berhasil disimpan');

                                            if(isset($request->validate) && $request->validate == 'validate'){
                                                $akunspv = Jabatan::where('code','22.14')->first();

                                                if($akunspv){
                                                    $user = $akunspv->role->loginUsers()->aktif();
                                                    if($user->count() > 0){
                                                        foreach($user->get() as $u){
                                                            Notifikasi::create([
                                                                'user_id' => $u->id,
                                                                'desc' => $request->user()->pegawai->jabatan->name.' telah melaporkan RPPA',
                                                                'link' => route('lppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]),
                                                                'is_active' => 1
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                elseif($isAnggotaPa){
                                    // Inti function
                                    $lppaAktifDetail = $lppaAktif->detail()->where(function($query){
                                        $query->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');
                                    });

                                    if($lppaAktif->finance_acc_status_id != 1 && $lppaAktifDetail->count() > 0){
                                        $successCount = 0;

                                        $lppaAktifDetailClone = clone $lppaAktifDetail;
                                        foreach($lppaAktifDetailClone->get() as $detail){
                                            if($detail->ppaDetail->value == 0){
                                                $detail->value = 0;
                                                $detail->receipt_status_id = 2;
                                            }
                                            else{
                                                $inputName = 'value-'.$detail->id;
                                                $checkName = 'receipt-'.$detail->id;
                                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                                if(isset($detail->value) && ($detail->employee_id != $request->user()->pegawai->id) && $detail->value != $requestValue){
                                                    $detail->edited_employee_id = $request->user()->pegawai->id;
                                                    $detail->edited_status_id = 1;
                                                }
                                                $detail->value = $requestValue;
                                                $detail->receipt_status_id = $request->{$checkName} == 'on' ? 1 : 2;
                                            }
                                            if(!isset($detail->employee_id)){
                                                $detail->employee_id = $request->user()->pegawai->id;
                                            }
                                            $detail->save();
                                            $successCount++;
                                        }

                                        if($successCount > 0){
                                            Session::flash('success','Perubahan data RPPA berhasil disimpan');
                                        }
                                    }
                                }

                                return redirect()->route('lppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]);
                            }
                        }
                        else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
                else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('lppa.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lppa\Lppa  $lppa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lppa $lppa)
    {
        //
    }

    /**
     * Accept all resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        $anggaranCount = $jenisAktif ? $jenisAktif->anggaran()->whereHas('ppa',function($q){$q->where('finance_acc_status_id',1)->has('lppa');})->count() : 0;

        if($anggaranCount > 0){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->where('finance_acc_status_id',1)->has('lppa')->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $ppaAcc = $anggaranAktif->ppa()->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1]);

                if($ppaAcc->count() > 0){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                        $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                        $lppaAktif = Lppa::where('number','LIKE',$nomor.'/%')->whereHas('ppa',function($query)use($anggaranAktif,$yearAttr,$tahun){
                            $query->where([
                                'budgeting_budgeting_type_id' => $anggaranAktif->id,
                                $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                                'finance_acc_status_id' => 1
                            ]);
                        })->first();

                        if($apbyAktif && $lppaAktif){
                            if($lppaAktif->detail()->count() > 0 && ($lppaAktif->detail()->where('acc_status_id',1)->count() >= $lppaAktif->detail()->count())){
                                
                                $difference_total_value = $lppaAktif->ppa->bbk->ppa_value-$lppaAktif->detail()->sum('value');
                                // Accept LPPA
                                $lppaAktif->update([
                                    'difference_total_value' => $difference_total_value,
                                    'finance_acc_id' => $request->user()->pegawai->id,
                                    'finance_acc_status_id' => 1,
                                    'finance_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                // Check if there are surplus or minus differences
                                $surplus_difference = 0;
                                $minus_difference = 0;
                                foreach($lppaAktif->detail as $l){
                                    if((($l->ppaDetail->value)-$l->value) > 0){
                                        $surplus_difference++;
                                    }
                                    elseif((($l->ppaDetail->value)-$l->value) < 0){
                                        $minus_difference++;
                                    }
                                }

                                if($surplus_difference > 0){
                                    // Add APBY
                                    $this->addApby($lppaAktif);
                                }
                                if($minus_difference > 0){
                                    // Generate PPA
                                    $this->generatePpa($lppaAktif);
                                }

                                Session::flash('success','RPPA berhasil disetujui');

                                return redirect()->route('lppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]);
                            }
                        }
                        else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
                else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('lppa.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $jenis, $tahun, $anggaran, $nomor){
        // Pre-defined formats
        $FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-';
        $FORMAT_CURRENCY_IDR = 'Rp#,##0_-';
        $FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)';

        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        $anggaranCount = $jenisAktif ? $jenisAktif->anggaran()->whereHas('ppa',function($q){$q->where('finance_acc_status_id',1)->has('lppa');})->count() : 0;

        if($anggaranCount > 0){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->where('finance_acc_status_id',1)->has('lppa')->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $ppaAcc = $anggaranAktif->ppa()->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1]);

                if($ppaAcc->count() > 0){
                    // Inti function
                    $lppaAktif = Lppa::where('number','LIKE',$nomor.'/%')->whereHas('ppa',function($query)use($anggaranAktif,$yearAttr,$tahun){
                        $query->where([
                            'budgeting_budgeting_type_id' => $anggaranAktif->id,
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'finance_acc_status_id' => 1
                        ]);
                    })->first();

                    if($lppaAktif && $lppaAktif->detail()->count() > 0){
                        // Inti function
                        $spreadsheet = new Spreadsheet();

                        $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
                        ->setLastModifiedBy($request->user()->pegawai->name)
                        ->setTitle("Data Laporan Pertanggungjawaban Penggunaan Anggaran MUDA Nomor ".$lppaAktif->number)
                        ->setSubject("Laporan Pertanggungjawaban Penggunaan Anggaran MUDA Nomor ".$lppaAktif->number)
                        ->setDescription("Rekapitulasi Data Laporan Pertanggungjawaban Penggunaan Anggaran MUDA Nomor ".$lppaAktif->number)
                        ->setKeywords("Laporan, Realisasi, Pertanggungjawaban, Penggunaan, Anggaran, LPPA, RPPA, MUDA");

                        $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('B1', 'LAPORAN PERTANGGUNGJAWABAN PENGGUNAAN ANGGARAN')
                        ->setCellValue('B2', 'YAYASAN MUDA INCOMSO')
                        ->setCellValue('A4', 'No. LPPA')
                        ->setCellValue('C4', ':')
                        ->setCellValue('D4', $lppaAktif->number ? $lppaAktif->number : '-')
                        ->setCellValue('A5', 'PA')
                        ->setCellValue('C5', ':')
                        ->setCellValue('D5', $lppaAktif->ppa->budgeting_budgeting_type_id ? $lppaAktif->ppa->jenisAnggaranAnggaran->anggaran->accJabatan->name : '-')
                        ->setCellValue('A6', 'No. PPA')
                        ->setCellValue('C6', ':')
                        ->setCellValue('D6', $lppaAktif->ppa->number ? $lppaAktif->ppa->number : '-')
                        ->setCellValue('A8', 'Tanggal Pencairan')
                        ->setCellValue('C8', ':')
                        ->setCellValue('D8', $lppaAktif->ppa->bbk->bbk->president_acc_time ? $lppaAktif->ppa->bbk->bbk->presidentAccDateId : $lppaAktif->ppa->bbk->bbk->directorAccDateId)
                        ->setCellValue('A9', 'Tanggal Pelaporan')
                        ->setCellValue('C9', ':')
                        ->setCellValue('D9', $lppaAktif->date ? $lppaAktif->dateId : '-')
                        ->setCellValue('A11', 'No.')
                        ->setCellValue('B11', 'Keterangan')
                        ->setCellValue('E11', 'Pencairan')
                        ->setCellValue('F11', 'Realisasi')
                        ->setCellValue('G11', 'Selisih')
                        ->setCellValue('I11', 'Bukti Transaksi');

                        // Logo
                        $logo = new Drawing;
                        $logo->setName('Logo MUDA');
                        $logo->setDescription('Logo MUDA');
                        $logo->setPath('./img/logo/logo-vertical.png');
                        $logo->setHeight(76);
                        $logo->setOffsetX(9);
                        $logo->setOffsetY(2);
                        $logo->setWorksheet($spreadsheet->getActiveSheet());
                        $logo->setCoordinates('A1');

                        $spreadsheet->getActiveSheet()->mergeCells('A1:A2');

                        // Merge Info
                        $spreadsheet->getActiveSheet()->mergeCells('A4:B4');
                        $spreadsheet->getActiveSheet()->mergeCells('A5:B5');
                        $spreadsheet->getActiveSheet()->mergeCells('A6:B6');
                        $spreadsheet->getActiveSheet()->mergeCells('A8:B8');
                        $spreadsheet->getActiveSheet()->mergeCells('A9:B9');

                        // Merge Table Head
                        $spreadsheet->getActiveSheet()->mergeCells('B11:D11');
                        $spreadsheet->getActiveSheet()->mergeCells('G11:H11');

                        $i = 1;
                        $kolom = $first_kolom = 12;
                        $max_kolom = $lppaAktif->detail()->count()+$kolom-1;
                        foreach($lppaAktif->detail as $l){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('A'.$kolom, $i++)
                            ->setCellValue('B'.$kolom, $l->ppaDetail->note)
                            ->setCellValue('E'.$kolom, $l->ppaDetail->value)
                            ->setCellValue('F'.$kolom, $l->value)
                            ->setCellValue('G'.$kolom, ($l->ppaDetail->value)-$l->value)
                            ->setCellValue('I'.$kolom, ucwords($l->buktiStatus->status));
                            $spreadsheet->getActiveSheet()->mergeCells('B'.$kolom.':D'.$kolom);
                            $spreadsheet->getActiveSheet()->mergeCells('G'.$kolom.':H'.$kolom);

                            $kolom++;
                        }

                        // Total Row
                        $spreadsheet->getActiveSheet()
                        ->setCellValue('A'.$kolom, 'Total')
                        ->setCellValue('E'.$kolom, '=SUM(E'.$first_kolom.':E'.$max_kolom.')')
                        ->setCellValue('F'.$kolom, '=SUM(F'.$first_kolom.':F'.$max_kolom.')')
                        ->setCellValue('G'.$kolom, '=SUM(G'.$first_kolom.':G'.$max_kolom.')');
                        $totalRow = $kolom;

                        $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':D'.$kolom);
                        $spreadsheet->getActiveSheet()->mergeCells('G'.$kolom.':H'.$kolom);

                        $kolom += 2;

                        $spreadsheet->getActiveSheet()->setTitle($lppaAktif->numberOnly);

                        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(7);
                        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(1);
                        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(27);
                        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
                        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
                        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(30);

                        $styleArray = [
                            'font' => [
                                'size' => 16,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('B1:B2')->applyFromArray($styleArray);

                        $styleArray = [
                            'font' => [
                                'size' => 12
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('A4:D6')->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('A8:D9')->applyFromArray($styleArray);

                        $styleArray = [
                            'font' => [
                                'size' => 12,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                            ],
                        ];

                        // Table Head
                        $spreadsheet->getActiveSheet()->getStyle('A11:I11')->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('A12:A'.$max_kolom)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $spreadsheet->getActiveSheet()->getStyle('E12:G'.($max_kolom+1))->getNumberFormat()
                        ->setFormatCode($FORMAT_CURRENCY_IDR);
                        $spreadsheet->getActiveSheet()->getStyle('E12:G'.($max_kolom+1))->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $spreadsheet->getActiveSheet()->getStyle('I12:I'.($max_kolom))->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $styleArray = [
                            'font' => [
                                'size' => 12,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('A'.($max_kolom+1))->applyFromArray($styleArray);

                        $styleArray = [
                            'font' => [
                                'size' => 12
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                            ],
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('A12:I'.($max_kolom+1))->applyFromArray($styleArray);

                        $hasSelisih = $lppaAktif && $lppaAktif->ppa->bbk->ppa_value && $lppaAktif->detail()->whereNotNull('employee_id')->count() > 0 ? true: false;
                        if($hasSelisih){
                            $selisihLebih = 0;
                            $selisihKurang = 0;
                            foreach($lppaAktif->detail as $l){
                                $selisih = ($l->ppaDetail->value)-$l->value;
                                if($selisih > 0) $selisihLebih += $selisih;
                                elseif($selisih < 0) $selisihKurang += $selisih;
                            }
                        }

                        $selisih = $lppaAktif->ppa->bbk->ppa_value-($lppaAktif->detail()->sum('value'));

                        $spreadsheet->getActiveSheet()
                        ->setCellValue('A'.$kolom, 'Selisih')
                        ->setCellValue('C'.$kolom, ':')
                        ->setCellValue('D'.$kolom++, 'Rp '.($selisih > 0 ? '+' : null).$lppaAktif->differenceTotalValueWithSeparator);

                        if($lppaAktif->finance_acc_status_id == 1 && $selisihLebih > 0){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('A'.$kolom, 'Lebih')
                            ->setCellValue('C'.$kolom, ':')
                            ->setCellValue('D'.$kolom++, 'Transfer ke Rekening BNI Syariah 448 448 4321');
                        }

                        if($lppaAktif->finance_acc_status_id == 1 && ($selisihKurang < 0 || $lppaAktif->difference_total_value < 0)){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('A'.$kolom, 'Kurang')
                            ->setCellValue('C'.$kolom, ':')
                            ->setCellValue('D'.$kolom++, 'Diajukan PPA dengan No '.$lppaAktif->ppaKurang->number);
                        }

                        $kolom ++;

                        // Finance Signature Row
                        $spreadsheet->getActiveSheet()->setCellValue('H'.$kolom, 'Manajer Keuangan');
                        $spreadsheet->getActiveSheet()->mergeCells('H'.$kolom.':I'.$kolom);

                        $styleArray = [
                            'font' => [
                                'size' => 11,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ]
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('H'.$kolom.':I'.$kolom)->applyFromArray($styleArray);

                        $kolom ++;

                        $styleArray = [
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'left' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                                'right' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ]
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('H'.$kolom.':I'.$kolom)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->mergeCells('H'.$kolom.':I'.$kolom);
                        $spreadsheet->getActiveSheet()->getRowDimension($kolom)->setRowHeight(30);
                        $kolom ++;

                        $spreadsheet->getActiveSheet()
                        ->setCellValue('H'.$kolom, $lppaAktif->accKeuangan->name);

                        $styleArray = [
                            'font' => [
                                'size' => 11,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'left' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                                'right' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                                'bottom' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ]
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('H'.$kolom.':I'.$kolom)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->mergeCells('H'.$kolom.':I'.$kolom);
                        $kolom++;

                        // $writer = new Xls($spreadsheet);

                        // header('Content-Type: application/vnd.ms-excel');
                        // header('Content-Disposition: attachment;filename="lppa_'.$lppaAktif->numberAsName.'.xls"');
                        // header('Cache-Control: max-age=0');

                        // $writer->save('php://output');
                        
                        // ob_end_flush();

                        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

                        $headers = [
                            'Cache-Control' => 'max-age=0',
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment;filename="lppa_'.$lppaAktif->numberAsName.'.xlsx"',
                        ];

                        return response()->stream(function()use($writer){
                            $writer->save('php://output');
                        }, 200, $headers);
                    }
                }
                else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('lppa.index');
    }

    /**
     * Substract the specified resource from storage.
     *
     * @param  \App\Models\Lppa\Lppa  $lppaAktif
     */
    public function addApby($lppaAktif){
        $ppa = $lppaAktif->ppa;
        $isYear = $ppa->year ? true : false;
        $yearAttr = $isYear ? 'year' : 'academic_year_id';

        $isKso = $ppa->jenisAnggaranAnggaran->jenis->isKso;
        $apby = $ppa->jenisAnggaranAnggaran->apby()->where($yearAttr, ($yearAttr == 'year' ? $ppa->year : $ppa->academic_year_id))->aktif()->unfinal()->latest();
        $apby = $isKso ? $apby->where('director_acc_status_id', 1)->first() : $apby->where('president_acc_status_id', 1)->first();

        if($apby){
            $difference_total_value = 0;
            foreach($lppaAktif->detail as $lppaAktifDetail){
                $ppaDetail = $lppaAktifDetail->ppaDetail;
                $selisih = $ppaDetail->value-$lppaAktifDetail->value;
                $apbyDetail = $ppaDetail->akun->apby()->where('apby_id',$apby->id)->first();
                if($apbyDetail && $selisih > 0){
                    $apbyDetail->used -= $selisih;
                    $apbyDetail->balance += $selisih;
                    $apbyDetail->save();

                    $difference_total_value += $selisih;
                }
            }
        
            $apby->total_used -= $difference_total_value;
            $apby->total_balance += $difference_total_value;
            $apby->save();
        }
    }

    /**
     * Generate the specified resources from storage.
     *
     * @param  \App\Models\Lppa\Lppa    $lppaAktif
     */
    public function generatePpa($lppaAktif)
    {
        $oldPpa = $lppaAktif->ppa;
        $isKso = $oldPpa->jenisAnggaranAnggaran->jenis->isKso;
        $isYear = $oldPpa->year ? true : false;

        $yearAttr = $isYear ? 'year' : 'academic_year_id';

        $ppa = new Ppa();
        $ppa->lppa_id = $lppaAktif->id;
        $ppa->date = Date::now('Asia/Jakarta')->format('Y-m-d');
        if(!$isYear)
            $ppa->academic_year_id = $oldPpa->academic_year_id;
        else
            $ppa->year = $oldPpa->year;
        $ppa->budgeting_budgeting_type_id = $oldPpa->budgeting_budgeting_type_id;

        // Number Generator
        $lastPpa = $oldPpa->jenisAnggaranAnggaran->ppa()->where($yearAttr, ($yearAttr == 'year' ? $oldPpa->year : $oldPpa->academic_year_id))->submitted()->latest()->first();

        $lastNumber = $lastPpa && $lastPpa->firstNumber ? $lastPpa->firstNumber+1 : 1;

        $roman_month = $this->romanMonth();
        $year = Date::now('Asia/Jakarta')->format('y');

        $ppa->number = $lastNumber.'/PPA/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$oldPpa->jenisAnggaranAnggaran->anggaran->name));
        $ppa->employee_id = Auth::user()->pegawai->id;
        $ppa->submitted_at = Date::now('Asia/Jakarta');
        $ppa->save();

        $ppa->fresh();

        foreach($lppaAktif->detail as $lppaAktifDetail){
            $ppaDetail = $lppaAktifDetail->ppaDetail;
            $selisih = $ppaDetail->value-$lppaAktifDetail->value;
            if($selisih < 0){
                $ppa->detail()->save(PpaDetail::create([
                    'account_id' => $ppaDetail->account_id,
                    'note' => $ppaDetail->note,
                    'value' => abs($selisih),
                    'employee_id' => Auth::user()->pegawai->id
                ]));
            }
        }

        return $oldPpa;
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
            if(!in_array($user->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
                if($user->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('position_id',$user->pegawai->jabatan->group()->first()->id);});
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('unit_id',$user->pegawai->unit_id);});
                }
            }
            $anggaranCount = $anggaranCount->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
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

    /**
     * Check the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkRole($anggaran,$role){
        $rolesCollection = collect([
            ['name' => 'TKIT', 'unit' => 1, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
            ['name' => 'SDIT', 'unit' => 2, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
            ['name' => 'SMPIT', 'unit' => 3, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
            ['name' => 'SMAIT', 'unit' => 4, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
            ['name' => 'Education Team', 'unit' => 5, 'position' => 18, 'roles' => ['etl','etm']],
            ['name' => 'Customer Team', 'unit' => 5, 'position' => 23, 'roles' => ['ctl','ctm']],
            ['name' => 'Finance and Accounting', 'unit' => 5, 'position' => 28, 'roles' => ['am','fam','faspv','fas']],
            ['name' => 'Administration, Legal, and IT', 'unit' => 5, 'position' => 32, 'roles' => ['am','aspv']],
            ['name' => 'Facilities', 'unit' => 5, 'position' => 36, 'roles' => ['am','ftm','ftspv','fts']],
            ['name' => 'Divisi Edukasi', 'unit' => 5, 'position' => 18, 'roles' => ['etl']],
            ['name' => 'Divisi Layanan', 'unit' => 5, 'position' => 23, 'roles' => ['ctl']],
            ['name' => 'Divisi Umum', 'unit' => 5, 'position' => 32, 'roles' => ['am']]
        ]);

        if($anggaran->unit_id == 5){
            $roles = $rolesCollection->where('unit',$anggaran->unit_id)->where('position',$anggaran->position_id)->first();
        }
        else{
            $roles = $rolesCollection->where('unit',$anggaran->unit_id)->first();
        }

        if($roles && in_array($role,$roles['roles'])) return true;
        else return false;
    }

    /**
     * Generate roman month.
     */
    public function romanMonth(){
        $month = Date::now('Asia/Jakarta')->format('m');

        $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($month > 0) {
            foreach ($map as $roman => $int) {
                if($month >= $int) {
                    $month -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }
}

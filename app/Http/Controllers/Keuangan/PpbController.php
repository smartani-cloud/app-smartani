<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Bbk\Bbk;
use App\Models\Bbk\BbkDetail;
use App\Models\Lppa\Lppa;
use App\Models\Lppa\LppaDetail;
use App\Models\Ppa\Ppa;
use App\Models\Ppa\PpaExclude;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Role;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PpbController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'keuangan';
        $modul = 'ppa';
        $this->modul = $modul;
        $this->active = 'PPA';
        $this->route = $this->subsystem.'.manajemen.'.$this->modul;
        $this->acceptZeroValues = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $jenis = null, $tahun = null)
    {
        // Override Budget Category
        if(!$jenis) $jenis = 'apby';

        $role = $request->user()->role->name;

        $jenisAnggaran = JenisAnggaran::all();
        $isKso = $jenisAktif = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $ppaAcc = $bbk = null;
        $yearsCount = $academicYearsCount = 0;

        if($jenis){
            if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
                return redirect()->route('ppb.index');
            }
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

            $queryPpa = Ppa::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->where('finance_acc_status_id',1);

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
                if($request->tahun){
                    $tahun = str_replace("-","/",$request->tahun);
                    $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                }
                else{
                    $tahun = TahunAjaran::where('is_finance_year',1)->latest()->first();
                }
                if(!$tahun) return redirect()->route('ppb.index');
            }
            else{
                $tahun = $tahun == null ? Date::now('Asia/Jakarta')->format('Y') : $tahun;
            }

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            $anggaranCount = $jenisAktif ? $jenisAktif->anggaran()->whereHas('ppa',function($q){
                $q->where('finance_acc_status_id',1);
            })->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->count() : 0;

            if($anggaranCount > 0){
                $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->pluck('id');

                $ppaAcc = Ppa::where('finance_acc_status_id', 1)->doesntHave('eksklusi')->whereIn('budgeting_budgeting_type_id',$anggarans);
                $ppaAcc = $ppaAcc->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));

                if($ppaAcc->count() > 0){
                    $bbk = $jenisAktif->bbk()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->get();
                    $ppaAcc = $ppaAcc->doesntHave('bbk')->get();

                    if(in_array($role,['ketuayys','direktur','fam','faspv','keulsi','am']))
                        $folder = $role;
                    else $folder = 'read-only';

                    return view('keuangan.'.$folder.'.ppb_index', compact('jenisAnggaran','jenisAktif','tahun','isYear','tahunPelajaran','ppaAcc','bbk','isKso','years','academicYears'));

                }
            }
        }
        if($role == 'keulsi'){
            return redirect()->route('ppb.index', ['jenis' => 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)]);
        }

        return view('keuangan.read-only.ppb_index', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','ppaAcc','bbk','isKso','years','academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $jenis, $tahun)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            $ppaAcc = Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->doesntHave('eksklusi')->doesntHave('bbk');

            if($ppaAcc->count() > 0){
                // Inti function
                $bbk = new Bbk();
                $bbk->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                if(!$isYear)
                    $bbk->academic_year_id = $tahun->id;
                else
                    $bbk->year = $tahun;
                $bbk->budgeting_type_id = $jenisAktif->id;

                // Number Generator
                $lastBbk = $jenisAktif->bbk()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->first();

                $lastNumber = $lastBbk && $lastBbk->firstNumber ? $lastBbk->firstNumber+1 : 1;

                $roman_month = $this->romanMonth();
                $year = Date::now('Asia/Jakarta')->format('y');

                $bbk->number = $lastNumber.'/PPB/'.$roman_month.'/'.$year.'/'.$jenisAktif->ref_number;
                $bbk->employee_id = $request->user()->pegawai->id;
                $bbk->save();

                $bbk->fresh();

                foreach($ppaAcc->take(5)->get() as $ppa){
                    $bbk->detail()->save(BbkDetail::create([
                        'ppa_id' => $ppa->id,
                        'ppa_value' => $ppa->total_value,
                        'employee_id' => $request->user()->pegawai->id
                    ]));
                }

                // Fam generate

                $director = Jabatan::where('code','19')->first();

                if($director){
                    $user = $director->role->loginUsers()->aktif()->first();
                    if($user){
                        Notifikasi::create([
                            'user_id' => $user->id,
                            'desc' => 'PPB No. '.$bbk->number.' menunggu persetujuan Anda',
                            'link' => route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbk->firstNumber]),
                            'is_active' => 1
                        ]);
                    }
                }

                if($isKso){
                    $letris = Role::where('code','29')->first();
                    if($letris){
                        $user = $letris->loginUsers()->aktif();
                        if($user->count() > 0){
                            $letrisUsers = $user->get();

                            foreach($letrisUsers as $u){
                                Notifikasi::create([
                                    'user_id' => $u->id,
                                    'desc' => 'PPB No. '.$bbk->number.' baru saja dibuat',
                                    'link' => route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbk->firstNumber]),
                                    'is_active' => 1,
                                ]);
                            }
                        }
                    }
                }

                // Director generate

                // if($isKso){
                //     // Accept BBK
                //     $bbk->update([
                //         'total_value' => $bbk->detail()->sum('ppa_value'),
                //         'director_acc_id' => $request->user()->pegawai->id,
                //         'director_acc_status_id' => 1,
                //         'director_acc_time' => Date::now('Asia/Jakarta')
                //     ]);

                //     // Substract Apby Detail Balances
                //     $this->substractApby($bbk);

                //     // Generate LPPA
                //     $this->generateLppa($bbk);
                // }
                // else{
                //     // Accept BBK
                //     $bbk->update([
                //         'director_acc_id' => $request->user()->pegawai->id,
                //         'director_acc_status_id' => 1,
                //         'director_acc_time' => Date::now('Asia/Jakarta')
                //     ]);

                //     $president = Jabatan::where('code','18')->first();

                //     if($president){
                //         $user = $president->role->loginUsers()->aktif()->first();
                //         if($user){
                //             Notifikasi::create([
                //                 'user_id' => $user->id,
                //                 'desc' => $isKso ? 'Ada PPB baru' : 'Ada PPB baru menunggu persetujuan Anda',
                //                 'link' => route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => $isKso ? $tahun->academicYearLink : $tahun, 'nomor' => $bbk->firstNumber]),
                //                 'is_active' => 1
                //             ]);
                //         }
                //     }
                // }

                Session::flash('success','PPB baru berhasil ditambahkan');

                return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbk->firstNumber]);
            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
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
     * @param  \App\Models\Bbk\Bbk  $bbk
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $jenis, $tahun, $nomor)
    {
        $role = $request->user()->role->name;

        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }

        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $bbkAktif = $jenisAktif->bbk()->where('number','LIKE',$nomor.'/%')->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->first();

            if($bbkAktif){
                //check active APBY
                $ppa = $bbkAktif->detail()->select('ppa_id')->get();

                $apbyAktifCount = 0;

                foreach($ppa as $p){
                    $anggaranAktif = $p->ppa->jenisAnggaran;

                    $apbyAktif = $p->ppa->jenisAnggaranAnggaran->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->latest();

                    $apbyAktif = !$isYear ? $apbyAktif->where('director_acc_status_id', 1)->first() : $apbyAktif->where('president_acc_status_id', 1)->first();

                    if($apbyAktif) $apbyAktifCount++;
                }
                
                $acceptable = false; 
                if($apbyAktifCount >= count($ppa) && ($this->acceptZeroValues || (!$this->acceptZeroValues && $bbkAktif->detail()->whereHas('ppa.detail',function($q){$q->where('value','<=',0);})->count() <= 0))) $acceptable = true;

                $notifikasi = Notifikasi::where(['id' => $request->notif_id,'user_id' => $request->user()->id])->first();

                if($notifikasi){
                    $notifikasi->update(['is_active' => 0]);
                }

                $acceptZeroValues = $this->acceptZeroValues;

                if(in_array($role,['ketuayys','direktur','fam','faspv','keulsi']))
                    $folder = $role;
                else $folder = 'read-only';

                if($isKso)
                    return view('keuangan.'.$folder.'.ppb_kso_show', compact('jenisAnggaran','jenisAktif','tahun','isYear','bbkAktif','isKso','acceptable','acceptZeroValues'));
                else
                    return view('keuangan.'.$folder.'.ppb_show', compact('jenisAnggaran','jenisAktif','tahun','isYear','bbkAktif','isKso','acceptable','acceptZeroValues'));
            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bbk\Bbk  $bbk
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $jenis, $tahun, $nomor, $ppa)
    {
        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }

        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get();

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $jenisAktif->bbk()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                if($isKso){
                    $bbkAktif = $bbkAktif->where(function($query){
                        $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                    })->first();
                }
                else{
                    if($role == 'ketuayys'){
                        $bbkAktif = $bbkAktif->where('director_acc_status_id',1)->where(function($query){
                            $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                        })->first();
                    }
                    elseif($role == 'direktur'){
                        $bbkAktif = $bbkAktif->where(function($query){
                            $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                        })->first();
                    }
                }

                if($bbkAktif){
                    $bbk = $ppa ? $bbkAktif->detail()->where('ppa_id',$ppa)->first() : null;

                    if($bbk){
                        $ppaAktif = $bbk->ppa;
                        $anggaranAktif = $ppaAktif->jenisAnggaranAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        
                        $apbyAktif = $anggaranAktif->apby()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->unfinal()->latest();
                        
                        $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1)->first() : $apbyAktif->where('president_acc_status_id', 1)->first();

                        $notifikasi = Notifikasi::where(['id' => $request->notif_id,'user_id' => $request->user()->id])->first();

                        if($notifikasi){
                            $notifikasi->update(['is_active' => 0]);
                        }

                        if($isKso)
                            return view('keuangan.'.$role.'.ppb_kso_edit', compact('role', 'jenisAnggaran','jenisAktif','tahun','isYear','bbkAktif','ppaAktif','apbyAktif'));
                        else
                            return view('keuangan.'.$role.'.ppb_edit', compact('jenisAnggaran','jenisAktif','tahun','isYear','bbkAktif','ppaAktif','apbyAktif'));
                    }

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bbk\Bbk  $bbk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jenis, $tahun, $nomor, $ppa)
    {
        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get();

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $jenisAktif->bbk()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                if($isKso){
                    $bbkAktif = $bbkAktif->where(function($query){
                        $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                    })->first();
                }
                else{
                    if($role == 'ketuayys'){
                        $bbkAktif = $bbkAktif->where('director_acc_status_id',1)->where(function($query){
                            $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                        })->first();
                    }
                    elseif($role == 'direktur'){
                        $bbkAktif = $bbkAktif->where(function($query){
                            $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                        })->first();
                    }
                }

                if($bbkAktif){
                    $bbk = $ppa ? $bbkAktif->detail()->where('ppa_id',$ppa)->first() : null;

                    if($bbk){
                        $ppaAktif = $bbk->ppa;
                        $anggaranAktif = $ppaAktif->jenisAnggaranAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                        $apbyAktif = $anggaranAktif->apby()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->unfinal()->latest();
                        
                        $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1)->first() : $apbyAktif->where('president_acc_status_id', 1)->first();

                        if($apbyAktif && $ppaAktif && $ppaAktif->detail()->count() > 0){
                            $successCount = 0;
                            if($isKso)
                                $ppaAktifDetail = $ppaAktif->detail()->where(function($query){
                                    $query->where('letris_acc_status_id','!=',1)->orWhereNull('letris_acc_status_id');
                                })->get();
                            else
                                $ppaAktifDetail = $ppaAktif->detail;

                            foreach($ppaAktifDetail as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                if($role == 'ketuayys'){
                                    $detail->value = $requestValue;
                                    $detail->value_president = $requestValue;
                                    if($requestValue > 0){
                                        if(isset($detail->value_director)){
                                            if($detail->value_director != $requestValue){
                                                $detail->edited_employee_id = $request->user()->pegawai->id;
                                                $detail->edited_status_id = 1;
                                            }
                                        }
                                    }
                                }
                                elseif($role == 'direktur'){
                                    $detail->value = $requestValue;
                                    $detail->value_director = $requestValue;
                                    if($requestValue > 0){
                                        if(isset($detail->value_fam)){
                                            if($detail->value_fam != $requestValue){
                                                $detail->edited_employee_id = $request->user()->pegawai->id;
                                                $detail->edited_status_id = 1;
                                            }
                                            $detail->director_acc_id = $request->user()->pegawai->id;
                                            $detail->director_acc_status_id = 1;
                                            $detail->director_acc_time = Date::now('Asia/Jakarta');

                                            if($isKso && isset($detail->value_letris) && ($detail->value_letris == $requestValue)){
                                                $letris = Role::where('code','29')->first();
                                                if($letris){
                                                    $user = $letris->loginUsers()->aktif()->first();
                                                    if($user){
                                                        $detail->letris_acc_id = $user->id;
                                                        $detail->letris_acc_status_id = 1;
                                                        $detail->letris_acc_time = Date::now('Asia/Jakarta');
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                elseif($isKso && $role == 'keulsi'){
                                    $detail->value_letris = $requestValue;
                                    if($requestValue > 0){
                                        if(isset($detail->value) && ($detail->value == $requestValue)){
                                            $detail->letris_acc_id = $request->user()->pegawai->id;
                                            $detail->letris_acc_status_id = 1;
                                            $detail->letris_acc_time = Date::now('Asia/Jakarta');
                                        }
                                        else{
                                            $jabatan = Jabatan::whereIn('code',['19','22.11','22.12'])->get();
                                            foreach($jabatan as $j){
                                                if($j){
                                                    $user = $j->role->loginUsers()->aktif();
                                                    if($user->count() > 0){
                                                        $users = $user->get();

                                                        foreach($users as $u){
                                                            Notifikasi::create([
                                                                'user_id' => $u->id,
                                                                'desc' => 'Letris mengajukan perubahan pada PPB No. '.$bbk->bbk->number,
                                                                'link' => route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]),
                                                                'is_active' => 1,
                                                                'notification_category_id' => 3
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $detail->save();
                                $successCount++;
                            }
                            if($successCount > 0){
                                if($role != 'keulsi'){
                                    $totalValue = $ppaAktif->detail->sum('value');
                                    $ppaAktif->update(['total_value' => $totalValue]);
                                    $bbk->update(['ppa_value' => $totalValue]);
                                }
                                if($ppaAktif->letris_acc_status_id != 1 && $ppaAktif->detail()->count() > 0 && ($ppaAktif->detail()->where('letris_acc_status_id',1)->count() >= $ppaAktif->detail()->count())){
                                    $ppaAktif->update([
                                        'letris_acc_id' => $request->user()->pegawai->id,
                                        'letris_acc_status_id' => 1,
                                        'letris_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    $director = Jabatan::where('code','19')->first();

                                    if($director){
                                        $user = $director->role->loginUsers()->aktif()->first();
                                        if($user){
                                            Notifikasi::create([
                                                'user_id' => $user->id,
                                                'desc' => 'PPA No. '.$ppaAktif->number.' menunggu untuk disepakati',
                                                'link' => route('ppb.ubah', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $ppaAktif->id]),
                                                'is_active' => 1
                                            ]);
                                        }
                                    }
                                }
                                Session::flash('success','Perubahan data PPA berhasil disimpan');
                            }

                            return redirect()->route('ppb.ubah', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $ppaAktif->id]);
                        }
                    }

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $jenis, $tahun, $nomor, $ppa)
    {
        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = $isKso ? Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get() : null;

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $isKso ? $jenisAktif->bbk()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->where(function($query){
                    $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                })->first() : null;

                if($bbkAktif){
                    $bbk = $ppa ? $bbkAktif->detail()->where('ppa_id',$ppa)->where('ppa_value','<=',0)->first() : null;

                    if($bbk){
                        $ppaAktif = $bbk->ppa;
                        $bbk->delete();
                        if($ppaAktif->finance_acc_status_id == 1){
                            if(!$ppaAktif->eksklusi) PpaExclude::create(['ppa_id'=>$ppaAktif->id]);
                        }

                        Session::flash('success','PPA No. '.$ppaAktif->number.' berhasil dihapus');
                    }
                    else Session::flash('danger','PPA gagal dihapus');

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $jenis, $tahun, $nomor, $ppa)
    {
        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get();

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $jenisAktif->bbk()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                if($isKso){
                    $bbkAktif = $bbkAktif->where(function($query){
                        $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                    })->first();
                }
                else{
                    if($role == 'ketuayys'){
                        $bbkAktif = $bbkAktif->where('director_acc_status_id',1)->where(function($query){
                            $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                        })->first();
                    }
                    elseif($role == 'direktur'){
                        $bbkAktif = $bbkAktif->where(function($query){
                            $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                        })->first();
                    }
                }

                if($bbkAktif){
                    if($bbkAktif->detail()->count() > 1){
                        $bbk = $ppa ? $bbkAktif->detail()->where('ppa_id',$ppa)->first() : null;

                        if($bbk){
                            $ppaAktif = $bbk->ppa;
                            $bbk->delete();
                            if($ppaAktif->finance_acc_status_id == 1){
                                foreach($ppaAktif->detail as $d){
                                    if(in_array($role,['ketuayys'])){
                                        $d->update([
                                            'value_president' => null,
                                            'president_acc_id' => null,
                                            'president_acc_status_id' => null,
                                            'president_acc_time' => null,
                                        ]);
                                    }
                                    $d->update([
                                        'value' => $d->value_fam,
                                        'value_director' => null,
                                        'value_letris' => null,
                                        'director_acc_id' => null,
                                        'director_acc_status_id' => null,
                                        'director_acc_time' => null,
                                        'letris_acc_id' => null,
                                        'letris_acc_status_id' => null,
                                        'letris_acc_time' => null,
                                    ]);
                                }
                                if(in_array($role,['ketuayys'])){
                                    $ppaAktif->update([
                                        'president_acc_id' => null,
                                        'president_acc_status_id' => null,
                                        'president_acc_time' => null,
                                    ]);
                                }
                                $ppaAktif->update([
                                    'total_value' => $ppaAktif->detail()->sum('value_fam'),
                                    'director_acc_id' => null,
                                    'director_acc_status_id' => null,
                                    'director_acc_time' => null,
                                    'letris_acc_id' => null,
                                    'letris_acc_status_id' => null,
                                    'letris_acc_time' => null,
                                ]);
                            }

                            Session::flash('success','PPA No. '.$ppaAktif->number.' berhasil ditunda');
                        }
                        else Session::flash('danger','PPA gagal ditunda');
                    }
                    else Session::flash('danger','PPA tidak dapat ditunda');

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Agree the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bbk\Bbk  $bbk
     * @return \Illuminate\Http\Response
     */
    public function agree(Request $request, $jenis, $tahun, $nomor, $ppa)
    {
        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = $isKso ? Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get() : null;

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $isKso ? $jenisAktif->bbk()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->where(function($query){
                    $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                })->first() : null;

                if($bbkAktif){
                    $bbk = $ppa ? $bbkAktif->detail()->where('ppa_id',$ppa)->first() : null;

                    if($bbk){
                        $ppaAktif = $bbk->ppa;
                        $anggaranAktif = $ppaAktif->jenisAnggaranAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                        $apbyAktif = $anggaranAktif->apby()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->unfinal()->latest();
                        
                        $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1)->first() : $apbyAktif->where('president_acc_status_id', 1)->first();

                        if($apbyAktif && $ppaAktif && ($ppaAktif->director_acc_status_id != 1 || $ppaAktif->letris_acc_status_id != 1) && $ppaAktif->detail()->count() > 0 && ($ppaAktif->detail()->where('letris_acc_status_id',1)->count() >= $ppaAktif->detail()->count())){
                            // Inti function

                            if($role == 'direktur'){
                                $ppaAktif->update([
                                    'director_acc_id' => $request->user()->pegawai->id,
                                    'director_acc_status_id' => 1,
                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                ]);
                                if($ppaAktif->letris_acc_status_id != 1){
                                    $detail = $ppaAktif->detail()->whereNotNull('letris_acc_id')->latest()->first();
                                    $ppaAktif->update([
                                        'letris_acc_id' => $detail->letris_acc_id,
                                        'letris_acc_status_id' => 1,
                                        'letris_acc_time' => $detail->letris_acc_time ? $detail->letris_acc_time : Date::now('Asia/Jakarta')
                                    ]);
                                }
                            }
                            elseif($role == 'keulsi'){
                                $ppaAktif->update([
                                    'letris_acc_id' => $request->user()->pegawai->id,
                                    'letris_acc_status_id' => 1,
                                    'letris_acc_time' => Date::now('Asia/Jakarta')
                                ]);
                            }

                            $bbkDetailAcc = $bbkAktif->detail()->whereHas('ppa',function($q){
                                $q->where(['director_acc_status_id' => 1,'letris_acc_status_id' => 1]);
                            })->count();

                            if($bbkDetailAcc >= $bbkAktif->detail()->count()){
                                if($role == 'direktur'){
                                    $bbkAktif->update([
                                        'total_value' => $bbkAktif->detail()->sum('ppa_value'),
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    $letris = Role::where('code','29')->first();

                                    if($letris){
                                        $user = $letris->loginUsers()->aktif();
                                        if($user->count() > 0){
                                            $letrisUsers = $user->get();

                                            foreach($letrisUsers as $u){
                                                Notifikasi::create([
                                                    'user_id' => $u->id,
                                                    'desc' => 'PPB No. '.$bbkAktif->number.' sudah disepakati MUDA',
                                                    'link' => route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]),
                                                    'is_active' => 1,
                                                    'notification_category_id' => 2,
                                                ]);
                                            }
                                        }
                                    }

                                    foreach($bbkAktif->detail as $bbk){
                                        $ppaAktif = $bbk->ppa;

                                        $pa = $ppaAktif->jenisAnggaranAnggaran->anggaran->accJabatan;

                                        if($pa){
                                            $user = $pa->role->loginUsers()->whereHas('pegawai.units',function($q)use($ppaAktif){
                                                $q->where('unit_id',$ppaAktif->jenisAnggaranAnggaran->anggaran->unit_id);
                                            })->aktif()->first();
                                            if($user){
                                                Notifikasi::create([
                                                    'user_id' => $user->id,
                                                    'desc' => 'PPA No. '.$ppaAktif->number.' sudah disepakati',
                                                    'link' => route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $ppaAktif->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $ppaAktif->firstNumber]),
                                                    'is_active' => 1,
                                                    'notification_category_id' => 2,
                                                ]);
                                            }
                                        }
                                    }
                                }
                                elseif($role == 'keulsi'){
                                    $director = Jabatan::where('code','19')->first();

                                    if($director){
                                        $user = $director->role->loginUsers()->aktif()->first();
                                        if($user){
                                            $bbkAktif->update([
                                                'total_value' => $bbkAktif->detail()->sum('ppa_value'),
                                                'director_acc_id' => $user->id,
                                                'director_acc_status_id' => 1,
                                                'director_acc_time' => Date::now('Asia/Jakarta')
                                            ]);
                                        }
                                    }
                                }

                                // Substract Apby Detail Balances
                                $this->substractApby($bbkAktif);

                                // Generate LPPA
                                $this->generateLppa($bbkAktif);                                

                                return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                            }

                            return redirect()->route('ppb.ubah', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $ppaAktif->id]);
                        }
                    }

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Accept resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $jenis, $tahun, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get();

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $jenisAktif->bbk()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                if($isKso){
                    $bbkAktif = $bbkAktif->where(function($query){
                        $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                    })->first();
                }
                else{
                    if($role == 'ketuayys'){
                        $bbkAktif = $bbkAktif->where('director_acc_status_id',1)->where(function($query){
                            $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                        })->first();
                    }
                    elseif($role == 'direktur'){
                        $bbkAktif = $bbkAktif->where(function($query){
                            $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                        })->first();
                    }
                }

                if($bbkAktif){
                    if($this->acceptZeroValues || (!$this->acceptZeroValues && $bbkAktif->detail()->whereHas('ppa.detail',function($q){$q->where('value','<=',0);})->count() <= 0)){
                        $ppa = $bbkAktif->detail()->select('ppa_id')->get();
                        $apbyAktifCount = 0;
                        foreach($ppa as $p){
                            $anggaranAktif = $p->ppa->jenisAnggaran;

                            $apbyAktif = $p->ppa->jenisAnggaranAnggaran->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->unfinal()->latest();

                            $apbyAktif = !$isYear ? $apbyAktif->where('director_acc_status_id', 1)->first() : $apbyAktif->where('president_acc_status_id', 1)->first();

                            if($apbyAktif) $apbyAktifCount++;
                        }

                        if($apbyAktifCount >= count($ppa)){
                            if($role == 'ketuayys' && !$isKso){
                                // Inti function

                                // Accept BBK
                                $bbkAktif->update([
                                    'total_value' => $bbkAktif->detail()->sum('ppa_value'),
                                    'president_acc_id' => $request->user()->pegawai->id,
                                    'president_acc_status_id' => 1,
                                    'president_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                foreach($bbkAktif->detail as $bbk){
                                    $ppaAktif = $bbk->ppa;

                                    $pa = $ppaAktif->jenisAnggaranAnggaran->anggaran->accJabatan;

                                    if($pa){
                                        $user = $pa->role->loginUsers()->whereHas('pegawai.units',function($q)use($ppaAktif){
                                            $q->where('unit_id',$ppaAktif->jenisAnggaranAnggaran->anggaran->unit_id);
                                        })->aktif()->first();
                                        if($user){
                                            Notifikasi::create([
                                                'user_id' => $user->id,
                                                'desc' => 'PPA No. '.$ppaAktif->number.' sudah disetujui',
                                                'link' => route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $ppaAktif->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $ppaAktif->firstNumber]),
                                                'is_active' => 1,
                                                'notification_category_id' => 2,
                                            ]);
                                        }
                                    }
                                }

                                // Substract Apby Detail Balances
                                $this->substractApby($bbkAktif);

                                // Generate LPPA
                                $this->generateLppa($bbkAktif);

                                Session::flash('success','Data PPB berhasil disetujui');
                            }
                            elseif($role == 'direktur'){
                                // Inti function

                                if($isKso){
                                    // Accept BBK
                                    $bbkAktif->update([
                                        'total_value' => $bbkAktif->detail()->sum('ppa_value'),
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    // Substract Apby Detail Balances
                                    $this->substractApby($bbkAktif);

                                    // Generate LPPA
                                    $this->generateLppa($bbkAktif);

                                    Session::flash('success','Data PPB berhasil disetujui');
                                }
                                else{
                                    if($bbkAktif->detail()->count() > 0){
                                        foreach($bbkAktif->detail as $d){
                                            $ppaAktif = $d->ppa;
                                            if($ppaAktif){
                                                $ppaAktif->update([
                                                    'director_acc_id' => $request->user()->pegawai->id,
                                                    'director_acc_status_id' => 1,
                                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                                ]);
                                            }
                                        }
                                    }

                                    // Accept BBK
                                    $bbkAktif->update([
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    $president = Jabatan::where('code','18')->first();

                                    if($president){
                                        $user = $president->role->loginUsers()->aktif()->first();
                                        if($user){
                                            Notifikasi::create([
                                                'user_id' => $user->id,
                                                'desc' => 'PPB No. '.$bbkAktif->number.' menunggu persetujuan Anda',
                                                'link' => route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]),
                                                'is_active' => 1
                                            ]);
                                        }
                                    }

                                    Session::flash('success','Data PPB berhasil disetujui');
                                }
                            }
                            else Session::flash('danger','Data PPB gagal disetujui');
                        }
                    }
                    else Session::flash('danger','Data PPB tidak dapat disetujui karena masih ada detail pengajuan 0');

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * View the specified resource.
     *
     * @param  \App\Models\Bbk\Bbk  $bbk
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $jenis, $tahun, $nomor, $ppa)
    {
        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }

        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $ppaAcc = Ppa::where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'finance_acc_status_id' => 1])->whereIn('budgeting_budgeting_type_id',$anggarans)->get();

            if($ppaAcc && count($ppaAcc) > 0){
                $bbkAktif = $jenisAktif->bbk()->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id), 'director_acc_status_id' => 1])->where('number','LIKE',$nomor.'/%')->first();

                if($bbkAktif){
                    $bbk = $ppa ? $bbkAktif->detail()->where('ppa_id',$ppa)->first() : null;

                    if($bbk){
                        $ppaAktif = $bbk->ppa;

                        return view('keuangan.'.$role.'.ppb_kso_view', compact('role', 'jenisAnggaran','jenisAktif','tahun','bbkAktif','ppaAktif'));
                    }

                    return redirect()->route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]);
                }
                else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);

            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $jenis, $tahun, $nomor)
    {
        // Pre-defined formats
        $FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-';
        $FORMAT_CURRENCY_IDR = 'Rp#,##0_-';
        $FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)';

        $role = $request->user()->role->name;

        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $isKso = $jenisAktif->isKso;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->pluck('id');

            $bbkAktif = $jenisAktif->bbk()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
            $bbkAktif = $isKso ? $bbkAktif->where('director_acc_status_id', 1)->first() : $bbkAktif->where('president_acc_status_id', 1)->first();

            if($bbkAktif && $bbkAktif->detail()->count() > 0){
                $spreadsheet = new Spreadsheet();

                $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
                ->setLastModifiedBy($request->user()->pegawai->name)
                ->setTitle("Data Pengajuan Perintah Bayar".($isKso?" KSO":null)." MUDA Nomor ".$bbkAktif->number)
                ->setSubject("Pengajuan Perintah Bayar".($isKso?" KSO":null)." MUDA Nomor ".$bbkAktif->number)
                ->setDescription("Rekapitulasi Data Pengajuan Perintah Bayar".($isKso?" KSO":null)." MUDA Nomor ".$bbkAktif->number)
                ->setKeywords("Pengajuan, Perintah, Bayar, PPB, MUDA".($isKso?", KSO":null));

                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('B1', 'PENGAJUAN PERINTAH BAYAR'.($isKso?" - KSO":null))
                ->setCellValue('B2', 'YAYASAN MUDA INCOMSO'.($isKso?" DAN YAYASAN LETRIS LUMINTOO":null))
                ->setCellValue('A4', 'No. PPB')
                ->setCellValue('B4', $bbkAktif->number ? $bbkAktif->number : '-')
                ->setCellValue('A5', 'Tanggal')
                ->setCellValue('B5', $bbkAktif->date ? $bbkAktif->dateId : '-')
                ->setCellValue('A7', 'No. PPA')
                ->setCellValue('D7', 'Jumlah Perintah Bayar')
                ->setCellValue('F7', 'Nomor Rekening Tujuan');

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

                if($isKso){
                    $logo = new Drawing;
                    $logo->setName('Logo Letris');
                    $logo->setDescription('Logo Letris');
                    $logo->setPath('./img/logo/logomark-letris.png');
                    $logo->setHeight(70);
                    $logo->setOffsetY(6);
                    $logo->setWorksheet($spreadsheet->getActiveSheet());
                    $logo->setCoordinates('G1');

                    $spreadsheet->getActiveSheet()->mergeCells('G1:G2');
                }

                $spreadsheet->getActiveSheet()->mergeCells('A7:C7');
                $spreadsheet->getActiveSheet()->mergeCells('D7:E7');
                $spreadsheet->getActiveSheet()->mergeCells('F7:G7');

                $kolom = $first_kolom = 8;
                $max_kolom = $bbkAktif->detail()->count()+$kolom-1;
                foreach($bbkAktif->detail as $b){
                    $spreadsheet->getActiveSheet()
                    ->setCellValue('A'.$kolom, $b->ppa->number)
                    ->setCellValue('D'.$kolom, $b->ppa_value)
                    ->setCellValue('F'.$kolom, $b->ppa->jenisAnggaranAnggaran->anggaran->unit->account_number);
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':C'.$kolom);
                    $spreadsheet->getActiveSheet()->mergeCells('D'.$kolom.':E'.$kolom);
                    $spreadsheet->getActiveSheet()->mergeCells('F'.$kolom.':G'.$kolom);

                    $kolom++;
                }

                // Total Row
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom, 'Total')
                ->setCellValue('D'.$kolom, '=SUM(D'.$first_kolom.':D'.$max_kolom.')');
                $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':C'.$kolom);
                $spreadsheet->getActiveSheet()->mergeCells('D'.$kolom.':E'.$kolom);
                $spreadsheet->getActiveSheet()->mergeCells('F'.$kolom.':G'.$kolom);

                $kolom += 2;

                $spreadsheet->getActiveSheet()->setTitle($bbkAktif->numberOnly);

                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(22);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
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
                        'size' => 12,
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ],
                ];

                $spreadsheet->getActiveSheet()->getStyle('A4:A5')->applyFromArray($styleArray);

                $styleArray = [
                    'font' => [
                        'size' => 12
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ],
                ];

                $spreadsheet->getActiveSheet()->getStyle('B4:B5')->applyFromArray($styleArray);

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
                $spreadsheet->getActiveSheet()->getStyle('A7:G7')->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('A8:A'.$max_kolom)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $spreadsheet->getActiveSheet()->getStyle('D8:D'.($max_kolom+1))->getNumberFormat()
                ->setFormatCode($FORMAT_CURRENCY_IDR);
                $spreadsheet->getActiveSheet()->getStyle('D8:D'.($max_kolom+1))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('F8:F'.$max_kolom)->getAlignment()
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

                $spreadsheet->getActiveSheet()->getStyle('A8:G'.($max_kolom+1))->applyFromArray($styleArray);

                // Director or President Signature Row
                $spreadsheet->getActiveSheet()
                ->setCellValue('E'.$kolom, ($isKso?'Direktur':'Ketua Yayasan').' Sekolah MUDA');
                $spreadsheet->getActiveSheet()->mergeCells('E'.$kolom.':G'.$kolom);

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

                $spreadsheet->getActiveSheet()->getStyle('E'.$kolom.':G'.$kolom)->applyFromArray($styleArray);

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

                $spreadsheet->getActiveSheet()->getStyle('E'.$kolom.':G'.$kolom)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->mergeCells('E'.$kolom.':G'.$kolom);
                $spreadsheet->getActiveSheet()->getRowDimension($kolom)->setRowHeight(30);
                $kolom ++;

                if($isKso){
                    $spreadsheet->getActiveSheet()->setCellValue('E'.$kolom, $bbkAktif->director_acc_status_id == 1 ? $bbkAktif->accDirektur->name : '...');
                }
                else{
                    $spreadsheet->getActiveSheet()->setCellValue('E'.$kolom, $bbkAktif->president_acc_status_id == 1 ? $bbkAktif->accKetua->name : '...');
                }

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

                $spreadsheet->getActiveSheet()->getStyle('E'.$kolom.':G'.$kolom)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->mergeCells('E'.$kolom.':G'.$kolom);
                $kolom++;

                // $writer = new Xls($spreadsheet);

                // header('Content-Type: application/vnd.ms-excel');
                // header('Content-Disposition: attachment;filename="ppb_'.($isKso?'kso_':null).$bbkAktif->numberAsName.'.xls"');
                // header('Cache-Control: max-age=0');

                // $writer->save('php://output');
                        
                // ob_end_flush();

                $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

                $headers = [
                    'Cache-Control' => 'max-age=0',
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment;filename="ppb_'.($isKso?'kso_':null).$bbkAktif->numberAsName.'.xlsx"',
                ];

                return response()->stream(function()use($writer){
                    $writer->save('php://output');
                }, 200, $headers);
            }
            else return redirect()->route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppb.index');
    }

    /**
     * Substract the specified resource from storage.
     *
     * @param  \App\Models\Bbk\Bbk  $bbkAktif
     */
    public function substractApby($bbkAktif){
        foreach($bbkAktif->detail as $bbkDetail){
            $ppa = $bbkDetail->ppa;
            $isYear = $ppa->year ? true : false;
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            $exclusiveCount = $ppa->detail()->whereHas('akun',function($q){$q->where('is_exclusive', 1);})->count();

            if($exclusiveCount < 1){
                $isKso = $ppa->jenisAnggaranAnggaran->jenis->isKso;
                foreach($ppa->detail as $ppaDetail){
                    $apbyDetail = $ppaDetail->akun->apby()->where('account_id',$ppaDetail->account_id);
                    if($isKso)
                        $apbyDetail = $apbyDetail->whereHas('apby',function($q)use($yearAttr,$ppa){$q->where([$yearAttr => ($yearAttr == 'year' ? $ppa->year : $ppa->academic_year_id),'budgeting_budgeting_type_id' => $ppa->budgeting_budgeting_type_id,'director_acc_status_id' => 1])->aktif()->unfinal()->latest();})->first();
                    else
                        $apbyDetail = $ppaDetail->akun->apby()->whereHas('apby',function($q)use($yearAttr,$ppa){$q->where([$yearAttr => ($yearAttr == 'year' ? $ppa->year : $ppa->academic_year_id),'budgeting_budgeting_type_id' => $ppa->budgeting_budgeting_type_id,'president_acc_status_id' => 1])->aktif()->unfinal()->latest();})->first();
                    if($apbyDetail){
                        $apbyDetail->used += $ppaDetail->value;
                        $apbyDetail->balance -= $ppaDetail->value;
                        $apbyDetail->save();
                    }
                }
                $apby = $ppa->jenisAnggaranAnggaran->apby()->where($yearAttr,($yearAttr == 'year' ? $ppa->year : $ppa->academic_year_id))->aktif()->unfinal()->latest();
                
                $apby = $isKso ? $apby->where('director_acc_status_id', 1)->first() : $apby->where('president_acc_status_id', 1)->first();
                
                $apby->total_used += $bbkDetail->ppa_value;
                $apby->total_balance -= $bbkDetail->ppa_value;
                $apby->save();
            }
        }
    }

    /**
     * Generate the specified resources from storage.
     *
     * @param  \App\Models\Bbk\Bbk                        $bbkAktif
     */
    public function generateLppa($bbkAktif)
    {
        foreach($bbkAktif->detail as $bbkDetail){
            $ppa = $bbkDetail->ppa;
            $exclusiveCount = $ppa->detail()->whereHas('akun',function($q){$q->where('is_exclusive', 1);})->count();

            if($exclusiveCount < 1){
                $lppa = $ppa->lppa;
                if(!$lppa){
                    $lppa = new Lppa();

                    // Number Generator
                    $roman_month = $this->romanMonth();
                    $year = Date::now('Asia/Jakarta')->format('y');

                    $lppa->number = $ppa->firstNumber.'/RPPA/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$ppa->jenisAnggaranAnggaran->anggaran->name));
                    $lppa->ppa_id = $ppa->id;
                    $lppa->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                    $lppa->save();

                    $lppa->fresh();
                }

                if($lppa->detail()->count() < 1){
                    foreach($ppa->detail as $d){
                        $lppa->detail()->save(LppaDetail::create(['ppa_detail_id' => $d->id]));
                    }
                }
            }
        }
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

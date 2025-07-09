<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\AnggaranAkun;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Bbk\Bbk;
use App\Models\Bbk\BbkDetail;
use App\Models\Lppa\Lppa;
use App\Models\Lppa\LppaDetail;
use App\Models\Ppa\Ppa;
use App\Models\Ppa\PpaDetail;
use App\Models\Ppa\PpaExclude;
use App\Models\Ppa\PpaProposal;
use App\Models\Ppa\PpaProposalDetail;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Notifikasi;
use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PpaController extends Controller
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
            $checkAccAttr = $j->isKso ? 'director_acc_status_id' : 'president_acc_status_id';
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
            $anggaranCount = $anggaranCount->whereHas('apby',function($q)use($checkAccAttr){$q->where($checkAccAttr,1);})->count();
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        
        $jenisAktif = $kategori = $allPpa = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = $accAttr = null;
        $yearsCount = $academicYearsCount = 0;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif){
                if($jenisAnggaranCount->where('id',1)->first()['anggaranCount'] < 1){
                    return view('keuangan.read-only.ppa_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','yearAttr','allPpa','years','academicYears','accAttr'));
                }
                else return redirect()->route('ppa.index');
            }

            $anggaranCount = 0;

            $queryPpa = Ppa::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });
            
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
                if(!$tahun) return redirect()->route('ppa.index');
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
                    return redirect()->route('ppa.index');
                }
            }

            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

            if($jenisAktif){
                $anggaranCount = $jenisAktif->anggaran()->whereHas('apby',function($q)use($accAttr){$q->where($accAttr,1);})->count();
            }

            if($anggaranCount > 0){
                $kategori = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();
                $allPpa = clone $queryPpa;
                $allPpa = $allPpa->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->get();

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

                    if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','fas','am','akunspv']) && !$checkAnggaran)){
                        $anggaranAktif = $anggaranAktif->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values());
                    }
                    $anggaranAktif = $anggaranAktif->first();

                    if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv']) && !$checkAnggaran){
                        $anggaranAktif = null;
                    }

                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $apbyAktif = $anggaranAktif->apby()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->where($accAttr, 1)->latest()->first();
                        $ppaCount = !$isYear ? $anggaranAktif->ppa()->where('academic_year_id',$tahun->id)->count() : $anggaranAktif->ppa()->where('year',$tahun)->count();

                        if(($apbyAktif && (($isYear && (($tahun != date('Y') && $ppaCount > 0) || $tahun == date('Y'))) || (!$isYear && (($tahun->is_finance_year != 1 && $ppaCount > 0) || $tahun->is_finance_year == 1))))
                        || (!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv']) && !$apbyAktif && ($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1))){
                            $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'];
                            $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                            if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $budgetingHistory = $anggaranAktif->tahuns()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->first();
                                $creatable = isset($budgetingHistory) && $budgetingHistory->ppa_active == 1 ? true : false;

                                $ppa = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                                if((!$isPa && $request->user()->pegawai->unit_id == 5) || (!$isAnggotaPa && $request->user()->pegawai->unit_id != 5))
                                    $ppa = $ppa->submitted();
                                $ppa = $ppa->get();

                                if(in_array($role,['fam','faspv','am']))
                                    $folder = $role;
                                elseif(in_array($role,['akunspv'])) $folder = 'faspv';
                                elseif($isPa) $folder = 'pa';
                                elseif($isAnggotaPa) $folder = 'anggota-pa';
                                else $folder = 'read-only';

                                return view('keuangan.'.$folder.'.ppa_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','allPpa','anggaranAktif','apbyAktif','isPa','ppa','years','academicYears','isAnggotaPa','isKso','accAttr','creatable'));
                            }
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
                if(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
                    $anggaranAktif = null;
                    if($request->user()->pegawai->unit_id == '5'){
                        $anggaranAktif = Anggaran::where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
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
                        })->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
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
                        $apbyAktif = !$isYear ? $anggaranAktif->apby()->where('academic_year_id',$tahun->id)->latest()->first() : $anggaranAktif->apby()->where('year',$tahun)->latest()->first();

                        if(!$apbyAktif){
                            $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        }
                        
                        return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                    elseif($checkAnggaran){
                        $anggaranAktif = $checkAnggaran->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            })->first();
                        $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
            }

            else return redirect()->route('ppa.index');
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            elseif($accessibleAnggaranCount < 1){
                return redirect()->route('keuangan.index');
            }
        }

        return view('keuangan.read-only.ppa_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','yearAttr','allPpa','years','academicYears','accAttr'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $jenis, $tahun, $anggaran, $type = null)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
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
            if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','am','akunspv']) && !$checkAnggaran)){
                $anggaranAktif = $anggaranAktif->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values());
            }
            $anggaranAktif = $anggaranAktif->first();

            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']) && !$checkAnggaran){
                $anggaranAktif = null;
            }

            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal()->where($accAttr,1)->first();
                $budgetingHistory = $anggaranAktif->tahuns()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->first();
                $creatable = isset($budgetingHistory) && $budgetingHistory->ppa_active == 1 ? true : false;

                if($apbyAktif && $this->checkRole($anggaranAktif->anggaran,$role) && $creatable){
                    // Inti function
                    $ppa = new Ppa();
                    if($type == 'proposal'){
                        $ppa->type_id = 2;
                    }
                    $ppa->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                    if(!$isYear)
                        $ppa->academic_year_id = $tahun->id;
                    else
                        $ppa->year = $tahun;
                    $ppa->budgeting_budgeting_type_id = $anggaranAktif->id;

                    // Number Generator
                    if(!$isYear)
                        $lastPpa = $anggaranAktif->ppa()->where('academic_year_id',$tahun->id)->draft()->latest()->first();
                    else
                        $lastPpa = $anggaranAktif->ppa()->where('year',$tahun)->draft()->latest()->first();

                    $lastNumber = $lastPpa && $lastPpa->firstNumber ? $lastPpa->firstNumber+1 : 1;

                    $roman_month = $this->romanMonth();
                    $year = Date::now('Asia/Jakarta')->format('y');

                    $ppa->number = $lastNumber.'/DRAFT/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$anggaranAktif->anggaran->name));
                    $ppa->employee_id = $request->user()->pegawai->id;
                    $ppa->is_draft = 1;
                    $ppa->save();

                    $ppa->fresh();

                    return redirect()->route('ppa.draft', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppa->firstNumber]);
                }
                elseif($apbyAktif && $this->checkRole($anggaranAktif->anggaran,$role) && !$creatable){
                    Session::flash('danger','Mohon maaf, sementara Anda tidak dapat membuat PPA baru');
                    return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link,]);
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal()->where($accAttr,1)->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','faspv','am','akunspv'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = null;
                        $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');

                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        if($isPa){
                            $ppaAktif = $ppaAktif->where(function($query){
                                $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                            });
                        }
                        else{
                            $ppaAktif = $ppaAktif->where(function($query){
                                $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                            });
                        }

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if($ppaAktif->type_id == 2){
                                $messages = [
                                    'account.required' => 'Mohon pilih salah satu akun anggaran',
                                    'proposals.required' => 'Mohon pilih satu atau beberapa proposal',
                                ];

                                $this->validate($request, [
                                    'account' => 'required',
                                    'proposals' => 'required',
                                ], $messages);
                            }
                            else{
                                $messages = [
                                    'account.required' => 'Mohon pilih salah satu akun anggaran',
                                    'note.required' => 'Mohon tuliskan keterangan',
                                    'value.required' => 'Mohon masukkan jumlah nominal pengajuan',
                                ];

                                $this->validate($request, [
                                    'account' => 'required',
                                    'note' => 'required',
                                    'value' => 'required'
                                ], $messages);
                            }

                            //$akun = $apbyAktif->detail()->whereHas('akun.kategori.parent',function($q){$q->where('name','Belanja');})->where('value','>',0)->with('akun')->get()->pluck('akun.id')->toArray();

                            // Without Exclusivity

                            // $autodebitCount = $ppaAktif->detail()->whereHas('akun',function($q){
                            //     $q->where('is_autodebit', 1);
                            // })->count();
                            // $exclusiveCount = $ppaAktif->detail()->whereHas('akun',function($q){
                            //     $q->where('is_exclusive', 1);
                            // })->count();

                            // $akun = $apbyAktif->detail()->whereHas('akun.kategori.parent',function($q){$q->where('name','Belanja');})->where('value','>',0)->with('akun')->get()->pluck('akun');
                            // if($autodebitCount > 0) $akun = $akun->where('is_autodebit', 1);
                            // elseif($ppaAktif->detail()->count() > 0) $akun = $akun->where('is_autodebit', 0);

                            if($ppaAktif->detail()->count() > 0){
                                // Check PPA details
                                $autodebitCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                    $q->where('is_autodebit', 1);
                                })->count();
                                $exclusiveCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                    $q->where('is_exclusive', 1);
                                })->count();

                                if($exclusiveCount > 0){
                                    $akun = $anggaranAktif->akun()->where('is_exclusive', 1)->orderBy('sort_order')->get();
                                }
                                else{
                                    $akun = $anggaranAktif->akun()->whereHas('apby',function($q)use($apbyAktif){
                                        $q->whereHas('akun.kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        })->whereHas('apby',function($q)use($apbyAktif){
                                            $q->where('id',$apbyAktif->id);
                                        });
                                        //})->where('value','>',0);
                                    });

                                    if($autodebitCount > 0) $akun = $akun->where('is_autodebit', 1);
                                    else $akun = $akun->where('is_autodebit', 0); 

                                    $akun = $akun->get();
                                }
                            }
                            else{
                                $akun = $anggaranAktif->akun()->where(function($q)use($apbyAktif){
                                    $q->whereHas('apby',function($q)use($apbyAktif){
                                        $q->whereHas('akun.kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        })->whereHas('apby',function($q)use($apbyAktif){
                                            $q->where('id',$apbyAktif->id); 
                                        });
                                        //})->where('value','>',0);
                                    })->orWhere('is_exclusive', 1);
                                })->get();
                            }
                            $akun = $akun->pluck('id')->toArray();

                            if(in_array($request->account,$akun) && $ppaAktif->detail()->count() < 5){
                                if($ppaAktif->type_id == 2){
                                    $proposals = PpaProposal::select('id','total_value','position_id')->whereNull('ppa_detail_id')->whereIn('id',$request->proposals)->get();
                                    $requestValue = $proposals->sum('total_value');
                                }
                                else{
                                    $requestValue =  (int)str_replace('.','',$request->value);
                                }

                                if($isPa){
                                    if($ppaAktif->type_id == 2){
                                        if($proposals && count($proposals) > 0){
                                            $ppaDetail = new PpaDetail();
                                            $ppaDetail->account_id = $request->account;
                                            $ppaDetail->value = $requestValue;
                                            $ppaDetail->value_pa = $requestValue;
                                            $ppaDetail->employee_id = $request->user()->pegawai->id;
                                            $ppaDetail->pa_acc_id = $request->user()->pegawai->id;
                                            $ppaDetail->pa_acc_status_id = 1;
                                            $ppaDetail->pa_acc_time = Date::now('Asia/Jakarta');

                                            $ppaAktif->detail()->save($ppaDetail);

                                            $ppaDetail->fresh();

                                            PpaProposal::whereNull('ppa_detail_id')->whereIn('id',$proposals->pluck('id'))->update([
                                                'ppa_detail_id' => $ppaDetail->id
                                            ]);
                                        }
                                    }
                                    else{
                                        $ppaAktif->detail()->save(PpaDetail::create([
                                            'account_id' => $request->account,
                                            'note' => $request->note,
                                            'value' => $requestValue,
                                            'value_pa' => $requestValue,
                                            'employee_id' => $request->user()->pegawai->id,
                                            'pa_acc_id' => $request->user()->pegawai->id,
                                            'pa_acc_status_id' => 1,
                                            'pa_acc_time' => Date::now('Asia/Jakarta')
                                        ]));

                                    }
                                }
                                else{
                                    if($ppaAktif->type_id == 2){
                                        if($proposals && count($proposals) > 0){
                                            $ppaDetail = new PpaDetail();
                                            $ppaDetail->account_id = $request->account;
                                            $ppaDetail->value = $requestValue;
                                            $ppaDetail->employee_id = $request->user()->pegawai->id;

                                            $ppaAktif->detail()->save($ppaDetail);

                                            $ppaDetail->fresh();

                                            PpaProposal::whereNull('ppa_detail_id')->whereIn('id',$proposals->pluck('id'))->update([
                                                'ppa_detail_id' => $ppaDetail->id
                                            ]);
                                        }
                                    }
                                    else{
                                        $ppaAktif->detail()->save(PpaDetail::create([
                                            'account_id' => $request->account,
                                            'note' => $request->note,
                                            'value' => $requestValue,
                                            'employee_id' => $request->user()->pegawai->id
                                        ]));
                                    }
                                }
                            }

                            if($ppaAktif->type_id == 2){
                                if($proposals && count($proposals) > 0){
                                    Session::flash('success','Data pengajuan berhasil ditambahkan');
                                }
                                else{
                                    Session::flash('danger','Data pengajuan tidak dapat ditambahkan');   
                                }
                            }
                            else
                                Session::flash('success','Data pengajuan berhasil ditambahkan');

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function draft(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->draft()->where('number','LIKE',$nomor.'/%')->first();

                        if($ppaAktif){
                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                            //$akun = $apbyAktif->detail()->whereHas('akun.kategori.parent',function($q){$q->where('name','Belanja');})->where('value','>',0)->with('akun')->get()->pluck('akun');

                            if($ppaAktif->detail()->count() > 0){
                                // Check PPA details
                                $autodebitCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                    $q->where('is_autodebit', 1);
                                })->count();
                                $exclusiveCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                    $q->where('is_exclusive', 1);
                                })->count();

                                if($exclusiveCount > 0){
                                    $akun = $anggaranAktif->akun()->where('is_exclusive', 1)->orderBy('sort_order')->get();
                                }
                                else{
                                    $akun = $anggaranAktif->akun()->whereHas('apby',function($q)use($apbyAktif){
                                        $q->whereHas('akun.kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        })->whereHas('apby',function($q)use($apbyAktif){
                                            $q->where('id',$apbyAktif->id);
                                        });
                                        //})->where('value','>',0);
                                    });

                                    if($autodebitCount > 0) $akun = $akun->where('is_autodebit', 1);
                                    else $akun = $akun->where('is_autodebit', 0); 

                                    $akun = $akun->get();
                                }
                            }
                            else{
                                $akun = $anggaranAktif->akun()->where(function($q)use($apbyAktif){
                                    $q->whereHas('apby',function($q)use($apbyAktif){
                                        $q->whereHas('akun.kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        })->whereHas('apby',function($q)use($apbyAktif){
                                            $q->where('id',$apbyAktif->id);
                                        });
                                        //})->where('value','>',0);
                                    })->orWhere('is_exclusive', 1);
                                })->orderBy('sort_order')->get();
                            }

                            $proposals = null;
                            if($ppaAktif->type_id == 2 && ($isPa || ($isAnggotaPa && $request->user()->pegawai->unit_id != 5))){
                                // $units = Unit::select('id')->whereHas('anggaran.jenisAnggaran.jenis',function($q)use($jenisAktif){
                                //     $q->where('id',$jenisAktif->id);
                                // })->pluck('id')->values()->unique();
                                $proposals = PpaProposal::whereNull('ppa_detail_id')->whereNull('declined_at')->where('budgeting_id',$anggaranAktif->budgeting_id)->has('details')->get();
                            }

                            $checkSetting = Setting::where('name','ppa_check_balance')->first();
                            $checkBalance = $checkSetting ? filter_var($checkSetting->value, FILTER_VALIDATE_BOOLEAN) : true;

                            $notifikasi = Notifikasi::where(['id' => $request->notif_id,'user_id' => $request->user()->id])->first();

                            if($notifikasi){
                                $notifikasi->update(['is_active' => 0]);
                            }

                            if(in_array($role,['fam','faspv','fas','am'])){
                                $folder = $role;
                            }
                            elseif($isPa) $folder = 'pa';
                            elseif($isAnggotaPa) $folder = 'anggota-pa';
                            else $folder = 'read-only';

                            return view('keuangan.'.$folder.'.ppa_show', compact('jenisAnggaran','jenisAktif','tahun','isYear','yearAttr','accAttr','anggaranAktif','apbyAktif','ppaAktif','isPa','isAnggotaPa','akun','checkBalance','proposals','isKso'));
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->submitted()->where('number','LIKE',$nomor.'/%')->first();

                        if($ppaAktif){
                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                            //$akun = $apbyAktif->detail()->whereHas('akun.kategori.parent',function($q){$q->where('name','Belanja');})->where('value','>',0)->with('akun')->get()->pluck('akun');

                            if($ppaAktif->detail()->count() > 0){
                                // Check PPA details
                                $autodebitCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                    $q->where('is_autodebit', 1);
                                })->count();
                                $exclusiveCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                    $q->where('is_exclusive', 1);
                                })->count();

                                if($exclusiveCount > 0){
                                    $akun = $anggaranAktif->akun()->where('is_exclusive', 1)->orderBy('sort_order')->get();
                                }
                                else{
                                    $akun = $anggaranAktif->akun()->whereHas('apby',function($q)use($apbyAktif){
                                        $q->whereHas('akun.kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        })->whereHas('apby',function($q)use($apbyAktif){
                                            $q->where('id',$apbyAktif->id);
                                        });
                                        //})->where('value','>',0);
                                    });

                                    if($autodebitCount > 0) $akun = $akun->where('is_autodebit', 1);
                                    else $akun = $akun->where('is_autodebit', 0); 

                                    $akun = $akun->get();
                                }
                            }
                            else{
                                $akun = $anggaranAktif->akun()->where(function($q)use($apbyAktif){
                                    $q->whereHas('apby',function($q)use($apbyAktif){
                                        $q->whereHas('akun.kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        })->whereHas('apby',function($q)use($apbyAktif){
                                            $q->where('id',$apbyAktif->id);
                                        });
                                        //})->where('value','>',0);
                                    })->orWhere('is_exclusive', 1);
                                })->orderBy('sort_order')->get();
                            }

                            $proposals = null;
                            if($ppaAktif->type_id == 2 && ($isPa || ($isAnggotaPa && $request->user()->pegawai->unit_id != 5))){
                                // $units = Unit::select('id')->whereHas('anggaran.jenisAnggaran.jenis',function($q)use($jenisAktif){
                                //     $q->where('id',$jenisAktif->id);
                                // })->pluck('id')->values()->unique();
                                $proposals = PpaProposal::whereNull('ppa_detail_id')->whereNull('declined_at')->where('budgeting_id',$anggaranAktif->budgeting_id)->has('details')->get();
                            }

                            $checkSetting = Setting::where('name','ppa_check_balance')->first();
                            $checkBalance = $checkSetting ? filter_var($checkSetting->value, FILTER_VALIDATE_BOOLEAN) : true;

                            $notifikasi = Notifikasi::where(['id' => $request->notif_id,'user_id' => $request->user()->id])->first();

                            if($notifikasi){
                                $notifikasi->update(['is_active' => 0]);
                            }

                            if(in_array($role,['am'])){
                                $folder = $role;
                            }
                            elseif(in_array($role,['keu','fam','faspv','fas']))  $folder = 'read-only';
                            elseif($isPa) $folder = 'pa';
                            elseif($isAnggotaPa) $folder = 'anggota-pa';
                            else $folder = 'read-only';

                            return view('keuangan.'.$folder.'.ppa_show', compact('jenisAnggaran','jenisAktif','tahun','isYear','yearAttr','accAttr','anggaranAktif','apbyAktif','ppaAktif','isPa','isAnggotaPa','akun','checkBalance','proposals','isKso'));
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','am'];
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $this->checkRole($anggaranAktif->anggaran,$role))){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->where(function($query){
                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                        });

                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if(isset($request->id)){
                                $ppaDetail = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $request->id;
                                if($this->checkRole($anggaranAktif->anggaran,$role)){
                                    if($isPa){
                                        $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                            $q->where('type_id',2);
                                        })->where(function($query){
                                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                        })->first();
                                    }
                                    else{
                                        $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                            $q->where('type_id',2);
                                        })->where(function($query){
                                            $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                        })->first();
                                    }
                                }

                                if($ppaDetail){
                                    $proposals = PpaProposal::where(function($q)use($ppaDetail,$anggaranAktif){
                                        $q->where(function($q)use($anggaranAktif){
                                            $q->whereNull('ppa_detail_id')->whereNull('declined_at')->where('budgeting_id',$anggaranAktif->budgeting_id)->has('details');
                                        })->orWhere(function($q)use($ppaDetail){
                                            $q->whereNotNull('ppa_detail_id')->whereIn('id',$ppaDetail->proposals->pluck('id'));
                                        });
                                    })->get();

                                    return view('keuangan.read-only.ppa_detail_ubah', compact('jenisAktif','tahun','isYear','anggaranAktif','ppaDetail','proposals'));
                                }
                                else Session::flash('danger','Data pengajuan gagal diubah');
                            }
                            else Session::flash('danger','Data pengajuan tidak ditemukan');

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editDraftProposal(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->draft()->first();

                        if($ppaAktif){
                            if(isset($request->id)){
                                $ppaDetail = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $request->id;

                                $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                    $q->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    })->where('type_id',2);
                                });
                                if(!in_array($role,$exceptionRoles)){
                                    if($isPa){
                                        $ppaDetail = $ppaDetail->where(function($query){
                                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                        });
                                    }
                                    else{
                                        $ppaDetail = $ppaDetail->where(function($query){
                                            $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                        });
                                    }
                                }
                                $ppaDetail = $ppaDetail->first();

                                if($ppaDetail){
                                    if(in_array($role,['fam','faspv','fas','am']))
                                        $folder = $role;
                                    elseif($isPa) $folder = 'pa';
                                    elseif($isAnggotaPa) $folder = 'anggota-pa';
                                    else $folder = 'read-only';

                                    return view('keuangan.'.$folder.'.ppa_edit_proposal', compact('jenisAnggaran','jenisAktif','tahun','isYear','yearAttr','accAttr','anggaranAktif','apbyAktif','ppaAktif','isPa','isAnggotaPa','isKso','ppaDetail'));
                                }
                                else Session::flash('danger','Data pengajuan tidak ditemukan');
                            }

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editProposal(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['ketuayys','direktur','fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->submitted()->first();

                        if($ppaAktif){
                            if(isset($request->id)){
                                $ppaDetail = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $request->id;

                                $ppaDetail = $ppaAktif->detail()->where('id',$id);
                                if((!in_array($role,$exceptionRoles) && !$isPa) || in_array($role,['fas'])){
                                    $ppaDetail = $ppaDetail->whereHas('ppa',function($q){
                                        $q->doesntHave('bbk');
                                    });
                                }
                                $ppaDetail = $ppaDetail->first();

                                if($ppaDetail){
                                    $isEditable = false;

                                    if((in_array($role,['am']) && $ppaDetail->finance_acc_status_id != 1) || ($isAnggotaPa && $ppaDetail->pa_acc_status_id != 1)){
                                        $isEditable = true;
                                    }

                                    if(in_array($role,['am']) && $isEditable)
                                        $folder = $role;
                                    elseif(in_array($role,['fam','faspv','fas']) || !$isEditable)  $folder = 'read-only';
                                    elseif($isPa) $folder = 'pa';
                                    elseif($isAnggotaPa) $folder = 'anggota-pa';
                                    else $folder = 'read-only';

                                    return view('keuangan.'.$folder.'.ppa_edit_proposal', compact('jenisAnggaran','jenisAktif','tahun','isYear','yearAttr','accAttr','anggaranAktif','apbyAktif','ppaAktif','isPa','isAnggotaPa','isKso','ppaDetail','isEditable'));
                                }
                                else Session::flash('danger','Data pengajuan tidak ditemukan');
                            }

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editProposalDesc(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();
                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if(isset($request->id)){
                                $ppaDetail = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $request->id;

                                $ppaDetail = $ppaAktif->detail()->where('id',$id);
                                if(!in_array($role,$exceptionRoles) || in_array($role,['fas'])){
                                    $ppaDetail = $ppaDetail->whereHas('ppa',function($q){
                                        $q->doesntHave('bbk');
                                    });
                                }
                                $ppaDetail = $ppaDetail->first();

                                if($ppaDetail){
                                    $isEditable = false;

                                    if((in_array($role,['am']) && $ppaDetail->finance_acc_status_id != 1) || ($isAnggotaPa && $ppaDetail->pa_acc_status_id != 1)){
                                        $isEditable = true;
                                    }

                                    if(in_array($role,['am']) && $isEditable)
                                        $folder = $role;
                                    elseif(in_array($role,['fam','faspv','fas']) || !$isEditable)  $folder = 'read-only';
                                    elseif($isPa) $folder = 'pa';
                                    elseif($isAnggotaPa) $folder = 'anggota-pa';
                                    else $folder = 'read-only';

                                    // Temp location
                                    $folder = 'read-only';

                                    // Inti Controller
                                    $data = $request->proposal ? PpaProposal::where(['id' => $request->proposal])->first() : null;
                                    
                                    if($data){
                                        return view('keuangan.'.$folder.'.ppa_edit_proposal_desc', compact('jenisAnggaran','jenisAktif','tahun','isYear','yearAttr','accAttr','anggaranAktif','apbyAktif','ppaAktif','isPa','isAnggotaPa','isKso','ppaDetail','isEditable','data'));
                                    }
                                    else{
                                        return "Ups, tidak dapat memuat data";
                                    }
                                }
                                else Session::flash('danger','Data pengajuan tidak ditemukan');
                            }

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->where(function($query){
                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                        });
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if($ppaAktif->detail()->count() > 0){
                                // Change to 'direktur' to enable
                                if($role == 'director'){
                                    // Inti function
                                    $ppaAktifDetail = $ppaAktif->detail();

                                    if($ppaAktif->finance_acc_status_id == 1 && $ppaAktifDetail->count() > 0){
                                        $ppaAktifDetailClone = clone $ppaAktifDetail;
                                        foreach($ppaAktifDetailClone->get() as $detail){
                                            $inputName = 'value-'.$detail->id;
                                            $requestValue = (int)str_replace('.','',$request->{$inputName});
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
                                                }
                                            }
                                            $detail->save();
                                        }

                                        if(isset($request->validate) && $request->validate == 'validate'){
                                            $ppaAktifDetailUpdate = clone $ppaAktifDetail;

                                            // Accept unaccepted value
                                            $ppaAktifDetailUpdate->whereNull([
                                                'director_acc_id',
                                                'director_acc_status_id',
                                                'director_acc_time',
                                            ])->update([
                                                'director_acc_id' => $request->user()->pegawai->id,
                                                'director_acc_status_id' => 1,
                                                'director_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            // Accept PPA
                                            $ppaAktif->update([
                                                'total_value' => $ppaAktifDetail->sum('value'),
                                                'director_acc_id' => $request->user()->pegawai->id,
                                                'director_acc_status_id' => 1,
                                                'director_acc_time' => Date::now('Asia/Jakarta')
                                            ]);
                                        }
                                    }
                                }elseif(in_array($role,['fam','faspv'])){
                                    // Inti function
                                    // $ppaAktifDetail = $ppaAktif->detail()->where(function($query){
                                    //     $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                    // });

                                    $ppaAktifDetail = $ppaAktif->detail();

                                    $autodebitCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                        $q->where('is_autodebit', 1);
                                    })->count();

                                    if(($isAnggotaPa || (!$isAnggotaPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktifDetail->count() > 0){
                                        if($autodebitCount > 0 && !$isKso){
                                            $ketuayys = $direktur = null;

                                            $jabatan = Jabatan::where('code','18')->first();
                                            $ketuayys = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                                                $q->aktif();
                                            })->first()->pegawai;

                                            $jabatan = Jabatan::where('code','19')->first();
                                            $direktur = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                                                $q->aktif();
                                            })->first()->pegawai;
                                        }

                                        $ppaAktifDetailClone = clone $ppaAktifDetail;
                                        foreach($ppaAktifDetailClone->get() as $detail){
                                            if($ppaAktif->type_id == 2){
                                                $proposals = PpaProposal::select('id','total_value')->whereIn('id',$detail->proposals->pluck('id'))->get();
                                                $requestValue = $proposals->sum('total_value');
                                            }
                                            else{
                                                $inputName = 'value-'.$detail->id;
                                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                            }
                                            if($isAnggotaPa){
                                                $detail->value = $requestValue;
                                                if($isPa) $detail->value_pa = $requestValue;
                                                //$detail->value_fam = $requestValue;
                                                if($requestValue > 0){
                                                    if($detail->value != $requestValue && $detail->employee_id != $request->user()->pegawai->id){
                                                        $detail->edited_employee_id = $request->user()->pegawai->id;
                                                        $detail->edited_status_id = 1;
                                                    }
                                                    if($isPa){
                                                        $detail->pa_acc_id = $request->user()->pegawai->id;
                                                        $detail->pa_acc_status_id = 1;
                                                        $detail->pa_acc_time = Date::now('Asia/Jakarta');
                                                    }
                                                    // $detail->finance_acc_id = $request->user()->pegawai->id;
                                                    // $detail->finance_acc_status_id = 1;
                                                    // $detail->finance_acc_time = Date::now('Asia/Jakarta');

                                                    // Autodebit
                                                    // if($autodebitCount > 0 && !$isKso){
                                                    //     $detail->director_acc_id = $direktur ? $direktur->id : $request->user()->pegawai->id;
                                                    //     $detail->director_acc_status_id = 1;
                                                    //     $detail->director_acc_time = Date::now('Asia/Jakarta');
                                                    // }
                                                }
                                                $detail->save();
                                            }
                                            else{
                                                $detail->value = $requestValue;
                                                $detail->value_fam = $requestValue;
                                                if($requestValue > 0){
                                                    if(isset($detail->value_pa)){
                                                        if($detail->value_pa != $requestValue){
                                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                                            $detail->edited_status_id = 1;
                                                        }
                                                        $detail->finance_acc_id = $request->user()->pegawai->id;
                                                        $detail->finance_acc_status_id = 1;
                                                        $detail->finance_acc_time = Date::now('Asia/Jakarta');

                                                        // Autodebit
                                                        if($autodebitCount > 0 && !$isKso){
                                                            $detail->director_acc_id = $direktur ? $direktur->id : $request->user()->pegawai->id;
                                                            $detail->director_acc_status_id = 1;
                                                            $detail->director_acc_time = Date::now('Asia/Jakarta');
                                                        }
                                                    }
                                                }
                                                $detail->save();
                                            }
                                        }
                                        if($isPa && isset($request->validate) && $request->validate == 'validate'){
                                            // Accept unaccepted value
                                            $ppaAktifDetail->whereNull([
                                                'pa_acc_id',
                                                'pa_acc_status_id',
                                                'pa_acc_time',
                                            ])->update([
                                                'pa_acc_id' => $request->user()->pegawai->id,
                                                'pa_acc_status_id' => 1,
                                                'pa_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            // Accept PPA
                                            $ppaAktif->update([
                                                'pa_acc_id' => $request->user()->pegawai->id,
                                                'pa_acc_status_id' => 1,
                                                'pa_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            if($ppaAktif->is_draft == 1){
                                                $ppaAktif->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                                                // Number Generator
                                                $lastPpa = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->submitted()->orderBy('submitted_at','DESC')->first();

                                                $lastNumber = $lastPpa && $lastPpa->firstNumber ? $lastPpa->firstNumber+1 : 1;

                                                $roman_month = $this->romanMonth();
                                                $year = Date::now('Asia/Jakarta')->format('y');

                                                $ppaAktif->number = $lastNumber.'/PPA/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$anggaranAktif->anggaran->name));
                                                $ppaAktif->is_draft = 0;
                                                $ppaAktif->submitted_at = Date::now('Asia/Jakarta');

                                                $ppaAktif->save();

                                                $ppaAktif->fresh();
                                            }

                                            $gadh = Jabatan::where('code','23.11')->first();

                                            if($gadh){
                                                $user = $gadh->role->loginUsers()->aktif()->first();
                                                if($user){
                                                    Notifikasi::create([
                                                        'user_id' => $user->id,
                                                        'desc' => $request->user()->pegawai->jabatan->name.' mengajukan PPA baru No. '.$ppaAktif->number,
                                                        'link' => route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]),
                                                        'is_active' => 1
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                                elseif($role == 'am'){
                                    // Inti function
                                    // $ppaAktifDetail = $ppaAktif->detail()->where(function($query){
                                    //     $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                    // });

                                    $ppaAktifDetail = $ppaAktif->detail();

                                    $autodebitCount = $ppaAktif->detail()->whereHas('akun',function($q){
                                        $q->where('is_autodebit', 1);
                                    })->count();

                                    if(($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktifDetail->count() > 0){
                                        if($autodebitCount > 0 && !$isKso){
                                            $ketuayys = $direktur = null;

                                            $jabatan = Jabatan::where('code','18')->first();
                                            $ketuayys = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                                                $q->aktif();
                                            })->first()->pegawai;

                                            $jabatan = Jabatan::where('code','19')->first();
                                            $direktur = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                                                $q->aktif();
                                            })->first()->pegawai;
                                        }

                                        $ppaAktifDetailClone = clone $ppaAktifDetail;
                                        foreach($ppaAktifDetailClone->get() as $detail){
                                            if($ppaAktif->type_id == 2){
                                                $proposals = PpaProposal::select('id','total_value')->whereIn('id',$detail->proposals->pluck('id'))->get();
                                                $requestValue = $proposals->sum('total_value');
                                            }
                                            else{
                                                $inputName = 'value-'.$detail->id;
                                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                            }
                                            if($isPa){
                                                $detail->value = $requestValue;
                                                $detail->value_pa = $requestValue;
                                                $detail->value_fam = $requestValue;
                                                if($requestValue > 0){
                                                    if($detail->value != $requestValue && $detail->employee_id != $request->user()->pegawai->id){
                                                        $detail->edited_employee_id = $request->user()->pegawai->id;
                                                        $detail->edited_status_id = 1;
                                                    }
                                                    $detail->pa_acc_id = $request->user()->pegawai->id;
                                                    $detail->pa_acc_status_id = 1;
                                                    $detail->pa_acc_time = Date::now('Asia/Jakarta');
                                                    $detail->finance_acc_id = $request->user()->pegawai->id;
                                                    $detail->finance_acc_status_id = 1;
                                                    $detail->finance_acc_time = Date::now('Asia/Jakarta');

                                                    // Autodebit
                                                    if($autodebitCount > 0 && !$isKso){
                                                        $detail->director_acc_id = $direktur ? $direktur->id : $request->user()->pegawai->id;
                                                        $detail->director_acc_status_id = 1;
                                                        $detail->director_acc_time = Date::now('Asia/Jakarta');
                                                    }
                                                }
                                                $detail->save();
                                            }
                                            else{
                                                $detail->value = $requestValue;
                                                $detail->value_fam = $requestValue;
                                                if($requestValue > 0){
                                                    if(isset($detail->value_pa)){
                                                        if($detail->value_pa != $requestValue){
                                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                                            $detail->edited_status_id = 1;
                                                        }
                                                        $detail->finance_acc_id = $request->user()->pegawai->id;
                                                        $detail->finance_acc_status_id = 1;
                                                        $detail->finance_acc_time = Date::now('Asia/Jakarta');

                                                        // Autodebit
                                                        if($autodebitCount > 0 && !$isKso){
                                                            $detail->director_acc_id = $direktur ? $direktur->id : $request->user()->pegawai->id;
                                                            $detail->director_acc_status_id = 1;
                                                            $detail->director_acc_time = Date::now('Asia/Jakarta');
                                                        }
                                                    }
                                                }
                                                $detail->save();
                                            }
                                        }

                                        if(isset($request->validate) && $request->validate == 'validate'){
                                            $ppaAktifDetailUpdate = clone $ppaAktifDetail;

                                            if($isPa){
                                                // Accept unaccepted value
                                                $ppaAktifDetailUpdate->whereNull([
                                                    'pa_acc_id',
                                                    'pa_acc_status_id',
                                                    'pa_acc_time',
                                                ])->update([
                                                    'pa_acc_id' => $request->user()->pegawai->id,
                                                    'pa_acc_status_id' => 1,
                                                    'pa_acc_time' => Date::now('Asia/Jakarta')
                                                ]);

                                                // Accept PPA
                                                $ppaAktif->update([
                                                    'pa_acc_id' => $request->user()->pegawai->id,
                                                    'pa_acc_status_id' => 1,
                                                    'pa_acc_time' => Date::now('Asia/Jakarta')
                                                ]);

                                                if($ppaAktif->is_draft == 1){
                                                    $ppaAktif->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                                                    // Number Generator
                                                    $lastPpa = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->submitted()->orderBy('submitted_at','DESC')->first();

                                                    $lastNumber = $lastPpa && $lastPpa->firstNumber ? $lastPpa->firstNumber+1 : 1;

                                                    $roman_month = $this->romanMonth();
                                                    $year = Date::now('Asia/Jakarta')->format('y');

                                                    $ppaAktif->number = $lastNumber.'/PPA/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$anggaranAktif->anggaran->name));
                                                    $ppaAktif->is_draft = 0;
                                                    $ppaAktif->submitted_at = Date::now('Asia/Jakarta');

                                                    $ppaAktif->save();

                                                    $ppaAktif->fresh();
                                                }
                                            }
                                            
                                            // Accept unaccepted value
                                            $ppaAktifDetailUpdate->whereNull([
                                                'finance_acc_id',
                                                'finance_acc_status_id',
                                                'finance_acc_time',
                                            ])->update([
                                                'finance_acc_id' => $request->user()->pegawai->id,
                                                'finance_acc_status_id' => 1,
                                                'finance_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            if($autodebitCount > 0 && !$isKso){
                                                $ppaAktifDetailUpdate->whereNull([
                                                    'director_acc_id',
                                                    'director_acc_status_id',
                                                    'director_acc_time',
                                                ])->update([
                                                    'director_acc_id' => $direktur ? $direktur->id : $request->user()->pegawai->id,
                                                    'director_acc_status_id' => 1,
                                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                                ]);
                                            }

                                            // Accept PPA
                                            $ppaAktif->update([
                                                'total_value' => $ppaAktifDetail->sum('value'),
                                                'finance_acc_id' => $request->user()->pegawai->id,
                                                'finance_acc_status_id' => 1,
                                                'finance_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            Session::flash('success','Data pengajuan berhasil disetujui');

                                            if($autodebitCount > 0 && !$isKso){
                                                $ppaAktif->update([
                                                    'total_value' => $ppaAktif->detail()->sum('value'),
                                                    'director_acc_id' => $direktur ? $direktur->id : $request->user()->pegawai->id,
                                                    'director_acc_status_id' => 1,
                                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                                ]);

                                                $ppaAktif->fresh();

                                                // Generate BBK
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

                                                $bbk->number = $lastNumber.'/PPB/'.$roman_month.'/'.$year;
                                                $bbk->employee_id = $direktur ? $direktur->id : $request->user()->pegawai->id;

                                                $bbk->director_acc_id = $direktur ? $direktur->id : $request->user()->pegawai->id;
                                                $bbk->director_acc_status_id = 1;
                                                $bbk->director_acc_time = Date::now('Asia/Jakarta');

                                                if(!$isKso){
                                                    $bbk->president_acc_id = $ketuayys ? $ketuayys->id : $request->user()->pegawai->id;
                                                    $bbk->president_acc_status_id = 1;
                                                    $bbk->president_acc_time = Date::now('Asia/Jakarta');
                                                }

                                                $bbk->save();

                                                $bbk->fresh();

                                                $bbk->detail()->save(BbkDetail::create([
                                                    'ppa_id' => $ppaAktif->id,
                                                    'ppa_value' => $ppaAktif->total_value,
                                                    'employee_id' => $direktur ? $direktur->id : $request->user()->pegawai->id
                                                ]));

                                                // Accept BBK
                                                $bbk->update([
                                                    'total_value' => $bbk->detail()->sum('ppa_value')
                                                ]);

                                                // Substract Apby Detail Balances
                                                $this->substractApby($bbk);

                                                // Generate LPPA
                                                $lppa = $ppaAktif->lppa;
                                                if(!$lppa){
                                                    $lppa = new Lppa();

                                                    $lppa->number = $ppaAktif->firstNumber.'/LPPA/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$ppaAktif->jenisAnggaranAnggaran->anggaran->name));
                                                    $lppa->ppa_id = $ppaAktif->id;
                                                    $lppa->date = Date::now('Asia/Jakarta')->format('Y-m-d');

                                                    $lppa->finance_acc_id = $request->user()->pegawai->id;
                                                    $lppa->finance_acc_status_id = 1;
                                                    $lppa->finance_acc_time = Date::now('Asia/Jakarta');

                                                    $lppa->save();

                                                    $lppa->fresh();
                                                }

                                                if($lppa->detail()->count() < 1){
                                                    foreach($ppaAktif->detail as $d){
                                                        $lppa->detail()->save(LppaDetail::create([
                                                            'ppa_detail_id' => $d->id,
                                                            'value' => $d->value,
                                                            'receipt_status_id' => 1,
                                                            'employee_id' => $request->user()->pegawai->id,
                                                            'acc_employee_id' => $request->user()->pegawai->id,
                                                            'acc_status_id' => 1,
                                                            'acc_time' => Date::now('Asia/Jakarta')
                                                        ]));
                                                    }
                                                }

                                                Session::flash('success','Data pengajuan berhasil disetujui');
                                            }
                                            else{
                                                //$director = Jabatan::where('code','19')->first();
                                                
                                                $director = null;

                                                if($director){
                                                    $user = $director->role->loginUsers()->aktif()->first();
                                                    if($user){
                                                        Notifikasi::create([
                                                            'user_id' => $user->id,
                                                            'desc' => 'PPA No. '.$ppaAktif->number.' menunggu persetujuan Anda',
                                                            'link' => route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]),
                                                            'is_active' => 1
                                                        ]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                elseif($isPa){
                                    // Inti function
                                    $ppaAktifDetail = $ppaAktif->detail()->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    });

                                    if($ppaAktifDetail->count() > 0){
                                        $ppaAktifDetailClone = clone $ppaAktifDetail;
                                        foreach($ppaAktifDetailClone->get() as $detail){
                                            if($ppaAktif->type_id == 2){
                                                $proposals = PpaProposal::select('id','total_value')->whereIn('id',$detail->proposals->pluck('id'))->get();
                                                $requestValue = $proposals->sum('total_value');
                                            }
                                            else{
                                                $inputName = 'value-'.$detail->id;
                                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                            }
                                            $detail->value = $requestValue;
                                            $detail->value_pa = $requestValue;
                                            if($requestValue > 0){
                                                if($detail->employee_id != $request->user()->pegawai->id){
                                                    if($detail->value != $requestValue){
                                                        $detail->edited_employee_id = $request->user()->pegawai->id;
                                                        $detail->edited_status_id = 1;
                                                    }
                                                    $detail->pa_acc_id = $request->user()->pegawai->id;
                                                    $detail->pa_acc_status_id = 1;
                                                    $detail->pa_acc_time = Date::now('Asia/Jakarta');
                                                }
                                            }
                                            $detail->save();
                                        }

                                        if(isset($request->validate) && $request->validate == 'validate'){
                                            // Accept unaccepted value
                                            $ppaAktifDetail->whereNull([
                                                'pa_acc_id',
                                                'pa_acc_status_id',
                                                'pa_acc_time',
                                            ])->update([
                                                'pa_acc_id' => $request->user()->pegawai->id,
                                                'pa_acc_status_id' => 1,
                                                'pa_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            // Accept PPA
                                            $ppaAktif->update([
                                                'pa_acc_id' => $request->user()->pegawai->id,
                                                'pa_acc_status_id' => 1,
                                                'pa_acc_time' => Date::now('Asia/Jakarta')
                                            ]);

                                            if($ppaAktif->is_draft == 1){
                                                $ppaAktif->date = Date::now('Asia/Jakarta')->format('Y-m-d');
                                                // Number Generator
                                                $lastPpa = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->submitted()->orderBy('submitted_at','DESC')->first();

                                                $lastNumber = $lastPpa && $lastPpa->firstNumber ? $lastPpa->firstNumber+1 : 1;

                                                $roman_month = $this->romanMonth();
                                                $year = Date::now('Asia/Jakarta')->format('y');

                                                $ppaAktif->number = $lastNumber.'/PPA/'.$roman_month.'/'.$year.'/'.strtoupper(str_replace(' ','',$anggaranAktif->anggaran->name));
                                                $ppaAktif->is_draft = 0;
                                                $ppaAktif->submitted_at = Date::now('Asia/Jakarta');

                                                $ppaAktif->save();

                                                $ppaAktif->fresh();
                                            }

                                            $gadh = Jabatan::where('code','23.11')->first();

                                            if($gadh){
                                                $user = $gadh->role->loginUsers()->aktif()->first();
                                                if($user){
                                                    Notifikasi::create([
                                                        'user_id' => $user->id,
                                                        'desc' => $request->user()->pegawai->jabatan->name.' mengajukan PPA baru No. '.$ppaAktif->number,
                                                        'link' => route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]),
                                                        'is_active' => 1
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                                elseif($isAnggotaPa){
                                    // Inti function
                                    $ppaAktifDetail = $ppaAktif->detail()->where(function($query){
                                        $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                    });

                                    if($ppaAktifDetail->count() > 0){
                                        $ppaAktifDetailClone = clone $ppaAktifDetail;
                                        foreach($ppaAktifDetailClone->get() as $detail){
                                            if($ppaAktif->type_id == 2){
                                                $proposals = PpaProposal::select('id','total_value')->whereIn('id',$detail->proposals->pluck('id'))->get();
                                                $requestValue = $proposals->sum('total_value');
                                            }
                                            else{
                                                $inputName = 'value-'.$detail->id;
                                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                            }
                                            $detail->value = $requestValue;
                                            if($requestValue > 0){
                                                if($detail->employee_id != $request->user()->pegawai->id){
                                                    if($detail->value != $requestValue){
                                                        $detail->edited_employee_id = $request->user()->pegawai->id;
                                                        $detail->edited_status_id = 1;
                                                    }
                                                }
                                            }
                                            $detail->save();
                                        }
                                    }
                                }
                            }

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function updateDetail(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','am'];
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $this->checkRole($anggaranAktif->anggaran,$role))){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->where(function($query){
                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                        });
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if(isset($request->editId)){
                                $ppa = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $ppaAktif->type_id == 2 ? $request->editId : explode('-',$request->editId)[1];
                                if($this->checkRole($anggaranAktif->anggaran,$role)){
                                    if($isPa){
                                        $ppa = $ppaAktif->detail()->where('id',$id)->where(function($query){
                                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                        })->first();
                                    }
                                    else{
                                        $ppa = $ppaAktif->detail()->where('id',$id)->where(function($query){
                                            $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                        })->first();
                                    }
                                }

                                if($ppa){
                                    $edited = 0;
                                    if($ppaAktif->type_id == 2){
                                        $proposals = PpaProposal::select('id','total_value','position_id')->where(function($q)use($ppa){
                                            $q->whereNull('ppa_detail_id')->orWhere(function($q)use($ppa){
                                                $q->whereNotNull('ppa_detail_id')->whereIn('id',$ppa->proposals->pluck('id'));
                                            });
                                        })->whereIn('id',$request->editProposals)->get();
                                            
                                        $requestValue = $proposals->sum('total_value');

                                        if($ppa->proposals->pluck('id')->toArray() != $proposals->pluck('id')->toArray()){
                                            $diff = $ppa->proposals->pluck('id')->diff($proposals->pluck('id'));
                                            $differences = $ppa->proposals()->whereIn('id',$diff)->get();
                                            if($differences && count($differences) > 0){
                                                foreach($differences as $d){
                                                    $d->details()->onlyTrashed()->restore();
                                                    $d->update(['total_value' => $d->details()->sum('value')]);
                                                }
                                                $ppa->proposals()->whereIn('id',$diff)->update(['ppa_detail_id' => null]);
                                            }
                                            $ppa->value = $requestValue;
                                            if($isPa){
                                                $ppa->value_pa = $requestValue;
                                                $ppa->pa_acc_id = $request->user()->pegawai->id;
                                                $ppa->pa_acc_status_id = 1;
                                                $ppa->pa_acc_time = Date::now('Asia/Jakarta');
                                            }
                                            $edited++;

                                            if($ppa->employee_id != $request->user()->pegawai->id){
                                                $ppa->edited_employee_id = $request->user()->pegawai->id;
                                                $ppa->edited_status_id = 1;
                                            }

                                            $ppa->fresh();

                                            PpaProposal::whereNull('ppa_detail_id')->whereIn('id',$proposals->pluck('id'))->update([
                                                'ppa_detail_id' => $ppa->id
                                            ]);
                                        }
                                    }
                                    else{
                                        if(isset($request->editNote) && $ppa->note != $request->editNote){
                                            $ppa->note = $request->editNote;
                                            $edited++;
                                        }
                                        if(isset($request->editValue)){
                                            $requestValue = (int)str_replace('.','',$request->editValue);
                                            if($ppa->value != $requestValue){
                                                $ppa->value = $requestValue;
                                                if($isPa){
                                                    $ppa->value_pa = $requestValue;
                                                    $ppa->pa_acc_id = $request->user()->pegawai->id;
                                                    $ppa->pa_acc_status_id = 1;
                                                    $ppa->pa_acc_time = Date::now('Asia/Jakarta');
                                                }
                                                $edited++;

                                                if($ppa->employee_id != $request->user()->pegawai->id){
                                                    $ppa->edited_employee_id = $request->user()->pegawai->id;
                                                    $ppa->edited_status_id = 1;
                                                }
                                            }
                                        }
                                    }

                                    if($edited > 0){
                                        $ppa->save();
                                        Session::flash('success','Data pengajuan berhasil diubah');
                                    }
                                }
                                else Session::flash('danger','Data pengajuan gagal diubah');
                            }
                            else Session::flash('danger','Data pengajuan tidak ditemukan');

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function updateProposal(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if(isset($request->id)){
                                $ppaDetail = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $request->id;

                                $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                    $q->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    })->where('type_id',2);
                                });
                                if(!in_array($role,$exceptionRoles)){
                                    if($isPa){
                                        $ppaDetail = $ppaDetail->where(function($query){
                                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                        });
                                    }
                                    else{
                                        $ppaDetail = $ppaDetail->where(function($query){
                                            $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                        });
                                    }
                                }
                                $ppaDetail = $ppaDetail->first();

                                if($ppaDetail){
                                    if(isset($request->editId)){
                                        $item = explode('-',$request->editId)[1];
                                        $proposalDetail = PpaProposalDetail::where('id',$item)->whereHas('proposal',function($q)use($ppaDetail){
                                            $q->whereIn('id',$ppaDetail->proposals->pluck('id'));
                                        })->first();

                                        if($proposalDetail){
                                            $descEdited = false;
                                            $edited = 0;
                                            if(isset($request->editDesc)){
                                                $proposalDetail->desc = $request->editDesc;
                                                $descEdited = true;
                                            }
                                            if(in_array($role,['fam','faspv','am'])){
                                                if($isPa){
                                                    if(isset($request->editPrice)){
                                                        $requestPrice = (int)str_replace('.','',$request->editPrice);
                                                        if(($proposalDetail->price_pa != $requestPrice) || ($proposalDetail->price_fam != $requestPrice)){
                                                            $proposalDetail->price = $requestPrice;
                                                            $proposalDetail->price_pa = $requestPrice;
                                                            $proposalDetail->price_fam = $requestPrice;
                                                            $edited++;
                                                        }
                                                    }
                                                    if(isset($request->editQty)){
                                                        $requestQty = (int)str_replace('.','',$request->editQty);
                                                        if(($proposalDetail->quantity_pa != $requestQty) || ($proposalDetail->quantity_fam != $requestQty)){
                                                            $proposalDetail->quantity = $requestQty;
                                                            $proposalDetail->quantity_pa = $requestQty;
                                                            $proposalDetail->quantity_fam = $requestQty;
                                                            $edited++;
                                                        }
                                                    }
                                                }
                                                else{
                                                    if(isset($request->editPrice)){
                                                        $requestPrice = (int)str_replace('.','',$request->editPrice);
                                                        if($proposalDetail->price_fam != $requestPrice){
                                                            $proposalDetail->price = $requestPrice;
                                                            $proposalDetail->price_fam = $requestPrice;
                                                            $edited++;
                                                        }
                                                    }
                                                    if(isset($request->editQty)){
                                                        $requestQty = (int)str_replace('.','',$request->editQty);
                                                        if($proposalDetail->quantity_fam != $requestQty){
                                                            $proposalDetail->quantity = $requestQty;
                                                            $proposalDetail->quantity_fam = $requestQty;
                                                            $edited++;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif($isAnggotaPa){
                                                if(isset($request->editPrice)){
                                                    $requestPrice = (int)str_replace('.','',$request->editPrice);
                                                    if($proposalDetail->price_pa != $requestPrice){
                                                        $proposalDetail->price = $requestPrice;
                                                        $proposalDetail->price_pa = $requestPrice;
                                                        $edited++;
                                                    }
                                                }
                                                if(isset($request->editQty)){
                                                    $requestQty = (int)str_replace('.','',$request->editQty);
                                                    if($proposalDetail->quantity_pa != $requestQty){
                                                        $proposalDetail->quantity = $requestQty;
                                                        $proposalDetail->quantity_pa = $requestQty;
                                                        $edited++;
                                                    }
                                                }
                                            }

                                            if($descEdited || $edited > 0){
                                                if($edited > 0){
                                                    if($proposalDetail->employee_id != $request->user()->pegawai->id){
                                                        $proposalDetail->edited_employee_id = $request->user()->pegawai->id;
                                                        $proposalDetail->edited_status_id = 1;
                                                        $ppaDetail->edited_employee_id = $request->user()->pegawai->id;
                                                        $ppaDetail->edited_status_id = 1;
                                                        $ppaDetail->save();
                                                    }

                                                    $proposalDetail->value = ($proposalDetail->price)*($proposalDetail->quantity);
                                                }

                                                $proposalDetail->save();

                                                if($edited > 0){
                                                    $data = $proposalDetail->proposal;

                                                    $data->update(['total_value' => $data->details()->sum('value')]);

                                                    $ppaDetail->update(['value' => $ppaDetail->proposals()->sum('total_value')]);
                                                    if(in_array($role,['fam','faspv','am'])){
                                                        if($isPa) $ppaDetail->update(['value_pa' => $ppaDetail->proposals()->sum('total_value')]);
                                                        $ppaDetail->update(['value_fam' => $ppaDetail->proposals()->sum('total_value')]);
                                                    }
                                                    elseif($isAnggotaPa) $ppaDetail->update(['value_pa' => $ppaDetail->proposals()->sum('total_value')]);
                                                }

                                                Session::flash('success','Data pengajuan berhasil diubah');
                                            }
                                        }
                                        else Session::flash('danger','Data pengajuan gagal diubah');
                                    }
                                    else Session::flash('danger','Data pengajuan tidak ditemukan');

                                    return redirect()->route('ppa.'.($ppaAktif->is_draft == 1 ? 'draft.' : null).'ubah.proposal', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $ppaDetail->id]);
                                }
                                else Session::flash('danger','Data pengajuan tidak ditemukan');
                            }

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }
    
    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function updateProposalDesc(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            $ppaDetail = null;
                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                            $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                $q->where(function($query){
                                    $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                })->where('type_id',2);
                            });
                            if(!in_array($role,$exceptionRoles)){
                                if($isPa){
                                    $ppaDetail = $ppaDetail->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    });
                                }
                                else{
                                    $ppaDetail = $ppaDetail->where(function($query){
                                        $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                    });
                                }
                            }
                            $ppaDetail = $ppaDetail->first();

                            if($ppaDetail){
                                if(isset($request->editId)){
                                    $proposal = $ppaDetail->proposals()->where('id',explode('-',$request->editId)[1])->first();

                                    if($proposal){
                                        $messages = [
                                            'editTitle.required' => 'Mohon tuliskan nama proposal yang diajukan',
                                        ];
                                    
                                        $this->validate($request, [
                                            'editTitle' => 'required',
                                        ], $messages);
                                        
                                        $old = $proposal->title;
                                        $proposal->title = $request->editTitle;
                                        $proposal->desc = isset($request->editDesc) ? $request->editDesc : null;
                                        $proposal->save();
                                        
                                        if($old != $proposal->title)
                                            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$proposal->title);
                                        else
                                            Session::flash('success','Perubahan deskripsi data pengajuan berhasil disimpan');
                                    }
                                    else Session::flash('danger','Data pengajuan gagal diubah');
                                }
                                else Session::flash('danger','Data pengajuan tidak ditemukan');

                                return redirect()->route('ppa.'.($ppaAktif->is_draft == 1 ? 'draft.' : null).'ubah.proposal', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $ppaDetail->id]);
                            }
                            else Session::flash('danger','Data pengajuan tidak ditemukan');

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function updateAllProposal(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            if(isset($request->id)){
                                $ppaDetail = null;
                                $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                                $id = $request->id;

                                $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                    $q->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    })->where('type_id',2);
                                });
                                if(!in_array($role,$exceptionRoles)){
                                    if($isPa){
                                        $ppaDetail = $ppaDetail->where(function($query){
                                            $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                        });
                                    }
                                    else{
                                        $ppaDetail = $ppaDetail->where(function($query){
                                            $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                        });
                                    }
                                }
                                $ppaDetail = $ppaDetail->first();

                                if($ppaDetail){
                                    if($ppaDetail->proposals()->count() > 0){
                                        foreach($ppaDetail->proposals as $proposal){
                                            $inputTitleName = 'title-'.$proposal->id;
                                            if(isset($request->{$inputTitleName})){
                                                $proposal->title = $request->{$inputTitleName};
                                                $proposal->save();
                                            }
                                            foreach($proposal->details as $detail){
                                                $edited = 0;
                                                $inputDescName = 'desc-'.$detail->id;
                                                $inputPriceName = 'price-'.$detail->id;
                                                $inputQtyName = 'qty-'.$detail->id;
                                                $requestPrice = (int)str_replace('.','',$request->{$inputPriceName});
                                                $requestQuantity = (int)str_replace('.','',$request->{$inputQtyName});
                                                if(isset($request->{$inputDescName})) $detail->desc = $request->{$inputDescName};
                                                $detail->price = $requestPrice;
                                                $detail->quantity = $requestQuantity;

                                                if(in_array($role,['fam','faspv','am'])){
                                                    if($isPa){
                                                        $detail->price_pa = $requestPrice;
                                                        $detail->quantity_pa = $requestQuantity;
                                                        $detail->price_fam = $requestPrice;
                                                        $detail->quantity_fam = $requestQuantity;

                                                        if($requestPrice > 0 && $requestQuantity > 0){
                                                            if((($detail->price != $requestPrice) || ($detail->quantity != $requestQuantity)) && $detail->employee_id != $request->user()->pegawai->id){
                                                                $edited++;
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        $detail->price_fam = $requestPrice;
                                                        $detail->quantity_fam = $requestQuantity;

                                                        if($requestPrice > 0 && $requestQuantity > 0){
                                                            if((isset($detail->price_pa) || isset($detail->quantity_pa)) && (($detail->price != $requestPrice) || ($detail->quantity != $requestQuantity)) && $detail->employee_id != $request->user()->pegawai->id){
                                                                $edited++;
                                                            }
                                                        }
                                                    }
                                                }
                                                elseif($isAnggotaPa){
                                                    $detail->price_pa = $requestPrice;
                                                    $detail->quantity_pa = $requestQuantity;

                                                    if($requestPrice > 0 && $requestQuantity > 0){
                                                        if((($detail->price != $requestPrice) || ($detail->quantity != $requestQuantity)) && $detail->employee_id != $request->user()->pegawai->id){
                                                            $edited++;
                                                        }
                                                    }
                                                    // Checked Bypass
                                                    if(!$isPa && $request->user()->pegawai->unit_id != '5'){
                                                        $ppaDetail->edited_employee_id = $request->user()->pegawai->id;
                                                        $ppaDetail->edited_status_id = 1;
                                                        $ppaDetail->save();
                                                    }
                                                }

                                                $detail->value = $requestPrice*$requestQuantity;

                                                if($edited > 0){
                                                    $detail->edited_employee_id = $request->user()->pegawai->id;
                                                    $detail->edited_status_id = 1;
                                                    $ppaDetail->edited_employee_id = $request->user()->pegawai->id;
                                                    $ppaDetail->edited_status_id = 1;
                                                    $ppaDetail->save();
                                                }

                                                $detail->save();
                                            }

                                            $proposal->update(['total_value' => $proposal->details()->sum('value')]);
                                        }
                                        $ppaDetail->update(['value' => $ppaDetail->proposals()->sum('total_value')]);
                                        if(in_array($role,['fam','faspv','am'])){
                                            if($isPa) $ppaDetail->update(['value_pa' => $ppaDetail->proposals()->sum('total_value')]);
                                            $ppaDetail->update(['value_fam' => $ppaDetail->proposals()->sum('total_value')]);
                                        }
                                        elseif($isAnggotaPa) $ppaDetail->update(['value_pa' => $ppaDetail->proposals()->sum('total_value')]);

                                        Session::flash('success','Rincian pengajuan berhasil diperbarui');
                                    }
                                    else Session::flash('danger','Data pengajuan tidak ditemukan');

                                    return redirect()->route('ppa.'.($ppaAktif->is_draft == 1 ? 'draft.' : null).'ubah.proposal', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $ppaDetail->id]);
                                }
                                else Session::flash('danger','Data pengajuan tidak ditemukan');
                            }

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function destroyDetail(Request $request, $jenis, $tahun, $anggaran, $nomor, $id)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    $exceptionRoles = ['pembinayys','ketuayys','direktur','fam','faspv','am'];
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $this->checkRole($anggaranAktif->anggaran,$role))){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            $ppa = null;
                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;
                            $zeroAccount = false;

                            if(in_array($role,['fam','am']) && $ppaAktif->detail()->where('value','<=',0)->whereDoesntHave('ppa.lppa')->count() > 0){
                                $ppa = $ppaAktif->detail()->where('id',$id)->where('value','<=',0)->whereDoesntHave('ppa.lppa')->first();
                                $zeroAccount = true;
                            }
                            if($this->checkRole($anggaranAktif->anggaran,$role) && !$ppa){
                                if($isPa){
                                    $ppa = $ppaAktif->detail()->where('id',$id)->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    })->first();
                                }
                                else{
                                    $ppa = $ppaAktif->detail()->where('id',$id)->where(function($query){
                                        $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                    })->first();
                                }
                            }

                            if($ppa){
                                $ppa->proposals()->update(['ppa_detail_id' => null]);
                                $ppa->delete();
                                if($zeroAccount && $ppaAktif->detail()->count() == 0 && $ppaAktif->finance_acc_status_id == 1){
                                    if(!$ppaAktif->eksklusi) PpaExclude::create(['ppa_id'=>$ppaAktif->id]);
                                    $ppaAktif->bbk()->delete();
                                }

                                Session::flash('success','Data pengajuan berhasil dihapus');
                            }
                            else Session::flash('danger','Data pengajuan gagal dihapus');

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function destroyProposalItem(Request $request, $jenis, $tahun, $anggaran, $nomor, $id, $item)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';
                $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';

                $apbyAktif = $anggaranAktif->apby()->where([
                    $yearAttr => $yearAttr == 'year' ? $tahun : $tahun->id,
                    $accAttr => 1
                ])->latest()->aktif()->unfinal()->first();

                if($apbyAktif){
                    $exceptionRoles = ['fam','faspv','fas','am'];
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);
                    if(in_array($role,$exceptionRoles) || (!in_array($role,$exceptionRoles) && $isAnggotaPa)){
                        // Inti function
                        $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%');
                        
                        $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                        $ppaAktif = $ppaAktif->first();

                        if($ppaAktif){
                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                            $ppaDetail = $ppaAktif->detail()->where('id',$id)->whereHas('ppa',function($q){
                                $q->where(function($query){
                                    $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                })->where('type_id',2);
                            });
                            if(!in_array($role,$exceptionRoles)){
                                if($isPa){
                                    $ppaDetail = $ppaDetail->where(function($query){
                                        $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    });
                                }
                                else{
                                    $ppaDetail = $ppaDetail->where(function($query){
                                        $query->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');
                                    });
                                }
                            }
                            $ppaDetail = $ppaDetail->first();

                            if($ppaDetail){
                                $proposalDetail = PpaProposalDetail::where('id',$item)->whereHas('proposal',function($q)use($ppaDetail){
                                    $q->whereIn('id',$ppaDetail->proposals->pluck('id'));
                                })->first();

                                if($proposalDetail){
                                    $desc = $proposalDetail->desc;
                                    $data = $proposalDetail->proposal;

                                    if($proposalDetail->employee_id != $request->user()->pegawai->id){
                                        $ppaDetail->edited_employee_id = $request->user()->pegawai->id;
                                        $ppaDetail->edited_status_id = 1;
                                        $ppaDetail->save();
                                    }

                                    $proposalDetail->delete();

                                    $data->update(['total_value' => $data->details()->sum('value')]);

                                    $ppaDetail->update(['value' => $ppaDetail->proposals()->sum('total_value')]);
                                    if(in_array($role,['fam','faspv','am'])){
                                        if($isPa) $ppaDetail->update(['value_pa' => $ppaDetail->proposals()->sum('total_value')]);
                                        $ppaDetail->update(['value_fam' => $ppaDetail->proposals()->sum('total_value')]);
                                    }
                                    elseif($isAnggotaPa) $ppaDetail->update(['value_pa' => $ppaDetail->proposals()->sum('total_value')]);

                                    Session::flash('success','Data '.$desc.' berhasil dihapus');
                                }
                                else Session::flash('danger','Data pengajuan gagal dihapus');

                                return redirect()->route('ppa.'.($ppaAktif->is_draft == 1 ? 'draft.' : null).'ubah.proposal', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $ppaDetail->id]);
                            }
                            else Session::flash('danger','Data pengajuan tidak ditemukan');

                            return redirect()->route(($ppaAktif->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]);
                        }
                        else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                }
                return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Exclude the specified resource from storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function exclude(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    // Inti function
                    $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->where('total_value','<=',0)->whereDoesntHave('lppa');
                        
                    $ppaAktif = $request->submitted == 1 ? $ppaAktif->submitted() : $ppaAktif->draft();

                    $ppaAktif = $ppaAktif->first();

                    if($ppaAktif){
                        if(!$ppaAktif->eksklusi) PpaExclude::create(['ppa_id'=>$ppaAktif->id]);
                        $ppaAktif->bbk()->delete();

                        Session::flash('success','Data PPA No. '.$ppaAktif->number.' berhasil dikecualikan');
                    }
                    else Session::flash('danger','PPA gagal dikecualikan');

                    return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Destroy the specified resource from storage.
     *
     * @param  \App\Models\Ppa\Ppa  $ppa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->unfinal();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    // Inti function
                    $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('number','LIKE',$nomor.'/%')->draft()->first();

                    $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                    if($ppaAktif && $isPa){
                        $number = $ppaAktif->number;
                        foreach($ppaAktif->detail as $d){
                            $d->proposals()->update(['ppa_detail_id' => null]);
                        }
                        $ppaAktif->detail()->delete();
                        $ppaAktif->delete();

                        Session::flash('success','Data Draf PPA No. '.$number.' berhasil dihapus');
                    }
                    else Session::flash('danger','Draf PPA gagal dihapus');

                    return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $jenis, $tahun, $anggaran, $nomor)
    {
        // Pre-defined formats
        $FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-';
        $FORMAT_CURRENCY_IDR = 'Rp#,##0_-';
        $FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)';

        $role = $request->user()->role->name;
        if(($jenis != 'apb-kso-'.strtolower($request->user()->pegawai->unit->name)) && $role == 'keulsi'){
            return redirect()->route('ppb.index');
        }
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $allPpa = Ppa::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$allPpa->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id')->unique()->values())->first();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link]);
            }
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif();

                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id',1)->first() : $apbyAktif->where('president_acc_status_id',1)->first();

                if($apbyAktif){
                    // Inti function
                    $ppaAktif = $anggaranAktif->ppa()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));

                    if($isKso)
                        $ppaAktif = $ppaAktif->where('pa_acc_status_id',1);
                    else
                        $ppaAktif = $ppaAktif->where('director_acc_status_id',1)->whereHas('bbk.bbk',function($q){
                            $q->where('president_acc_status_id',1);
                        });

                    $ppaAktif = $ppaAktif->submitted()->where('number','LIKE',$nomor.'/%')->first();

                    if($ppaAktif && $ppaAktif->detail()->count() > 0){
                        $ppaKsoCair = ($isKso && $ppaAktif->bbk && $ppaAktif->bbk->bbk->director_acc_status_id == 1) ? true : false;

                        $spreadsheet = new Spreadsheet();

                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
                            ->setLastModifiedBy($request->user()->pegawai->name)
                            ->setTitle("Data Pengajuan Penggunaan Anggaran KSO MUDA Nomor ".$ppaAktif->number)
                            ->setSubject("Pengajuan Penggunaan Anggaran MUDA KSO Nomor ".$ppaAktif->number)
                            ->setDescription("Rekapitulasi Data Pengajuan Penggunaan Anggaran KSO MUDA Nomor ".$ppaAktif->number)
                            ->setKeywords("Pengajuan, Penggunaan, Anggaran, PPA, KSO, MUDA");

                            $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('C1', 'PENGAJUAN PENGGUNAAN ANGGARAN - KSO')
                            ->setCellValue('C2', 'YAYASAN MUDA INCOMSO DAN YAYASAN LETRIS LUMINTOO');

                            // Logo
                            $logo = new Drawing;
                            $logo->setName('Logo Letris');
                            $logo->setDescription('Logo Letris');
                            $logo->setPath('./img/logo/logomark-letris.png');
                            $logo->setHeight(70);
                            $logo->setOffsetX(125);
                            $logo->setOffsetY(6);
                            $logo->setWorksheet($spreadsheet->getActiveSheet());
                            $logo->setCoordinates('H1');

                            $spreadsheet->getActiveSheet()->mergeCells('H1:H2');
                        }
                        else{
                            $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
                            ->setLastModifiedBy($request->user()->pegawai->name)
                            ->setTitle("Data Referensi Penjurnalan MUDA Nomor ".$ppaAktif->number)
                            ->setSubject("Referensi Penjurnalan MUDA KSO Nomor ".$ppaAktif->number)
                            ->setDescription("Rekapitulasi Data Referensi Penjurnalan MUDA Nomor ".$ppaAktif->number)
                            ->setKeywords("Referensi, Jurnal, Penjurnalan, PPA, MUDA");

                            $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('C1', 'REFERENSI PENJURNALAN')
                            ->setCellValue('C2', 'YAYASAN MUDA INCOMSO');
                        }

                        $spreadsheet->getActiveSheet()
                        ->setCellValue('A4', 'Tanggal')
                        ->setCellValue('B4', ':')
                        ->setCellValue('C4', $ppaAktif->date ? $ppaAktif->dateId : '-')
                        ->setCellValue('A5', 'Anggaran')
                        ->setCellValue('B5', ':')
                        ->setCellValue('C5', $ppaAktif->budgeting_budgeting_type_id ? $ppaAktif->jenisAnggaranAnggaran->anggaran->name : '-')
                        ->setCellValue('A6', 'Nomor')
                        ->setCellValue('B6', ':')
                        ->setCellValue('C6', $ppaAktif->number ? $ppaAktif->number : '-')
                        ->setCellValue('A8', 'No.')
                        ->setCellValue('B8', 'Akun Anggaran')
                        ->setCellValue('E8', 'Keterangan');

                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('F8', 'Jumlah MUDA')
                            ->setCellValue('H8', 'Jumlah Letris');
                        }
                        else{
                            $spreadsheet->getActiveSheet()->setCellValue('G8', 'Jumlah');
                        }

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

                        $spreadsheet->getActiveSheet()->mergeCells('A1:B2');

                        // Merge Table Head
                        $spreadsheet->getActiveSheet()->mergeCells('B8:D8');
                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getActiveSheet()->mergeCells('F8:G8');
                        }
                        else{
                            $spreadsheet->getActiveSheet()->mergeCells('E8:F8');
                        }

                        $i = 1;
                        $kolom = $first_kolom = 9;
                        $max_kolom = $ppaAktif->detail()->count()+$kolom-1;
                        foreach($ppaAktif->detail as $p){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('A'.$kolom, $i++)
                            ->setCellValue('B'.$kolom, $p->akun->codeName)
                            ->setCellValue('E'.$kolom, $p->note);

                            if($isKso && !$ppaKsoCair){
                                $spreadsheet->getActiveSheet()
                                ->setCellValue('F'.$kolom, $p->value)
                                ->setCellValue('H'.$kolom, $p->value_letris);
                                
                                $spreadsheet->getActiveSheet()->mergeCells('F'.$kolom.':G'.$kolom);
                            }
                            else{
                                $spreadsheet->getActiveSheet()
                                ->setCellValue('G'.$kolom, $p->value);

                                $spreadsheet->getActiveSheet()->mergeCells('E'.$kolom.':F'.$kolom);
                            }

                            $spreadsheet->getActiveSheet()->mergeCells('B'.$kolom.':D'.$kolom);

                            $kolom++;
                        }

                        // Total Row
                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('A'.$kolom, 'Total')
                            ->setCellValue('F'.$kolom, '=SUM(F'.$first_kolom.':F'.$max_kolom.')')
                            ->setCellValue('H'.$kolom, '=SUM(H'.$first_kolom.':H'.$max_kolom.')');
                            $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':E'.$kolom);
                            $spreadsheet->getActiveSheet()->mergeCells('F'.$kolom.':G'.$kolom);
                        }
                        else{
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('A'.$kolom, 'Total')
                            ->setCellValue('G'.$kolom, '=SUM(G'.$first_kolom.':G'.$max_kolom.')');
                            $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':F'.$kolom);
                        }

                        $kolom += 2;

                        $spreadsheet->getActiveSheet()->setTitle($ppaAktif->numberOnly);

                        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(9);
                        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(1);
                        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(45);
                            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
                            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        }
                        else{
                            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
                            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                        }
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

                        $spreadsheet->getActiveSheet()->getStyle('C1:C2')->applyFromArray($styleArray);

                        $styleArray = [
                            'font' => [
                                'size' => 12
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('A4:A6')->applyFromArray($styleArray);

                        $styleArray = [
                            'font' => [
                                'size' => 12,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('C4:C6')->applyFromArray($styleArray);

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
                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getActiveSheet()->getStyle('A8:H8')->applyFromArray($styleArray);
                        }
                        else{
                            $spreadsheet->getActiveSheet()->getStyle('A8:G8')->applyFromArray($styleArray);
                        }
                        $spreadsheet->getActiveSheet()->getStyle('A9:A'.$max_kolom)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        if($isKso && !$ppaKsoCair){
                            $spreadsheet->getActiveSheet()->getStyle('F9:F'.($max_kolom+1))->getNumberFormat()
                            ->setFormatCode($FORMAT_CURRENCY_IDR);
                            $spreadsheet->getActiveSheet()->getStyle('F9:F'.($max_kolom+1))->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $spreadsheet->getActiveSheet()->getStyle('H9:H'.($max_kolom+1))->getNumberFormat()
                            ->setFormatCode($FORMAT_CURRENCY_IDR);
                            $spreadsheet->getActiveSheet()->getStyle('H9:H'.($max_kolom+1))->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $spreadsheet->getActiveSheet()->getStyle('C9:E'.($max_kolom))->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }
                        else{
                            $spreadsheet->getActiveSheet()->getStyle('G9:G'.($max_kolom+1))->getNumberFormat()
                            ->setFormatCode($FORMAT_CURRENCY_IDR);
                            $spreadsheet->getActiveSheet()->getStyle('G9:G'.($max_kolom+1))->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $spreadsheet->getActiveSheet()->getStyle('C9:F'.($max_kolom))->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }

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

                        // Letter Variables
                        if($isKso && !$ppaKsoCair){
                            $firstColumn = 'G';
                            $lastColumn = 'H';
                        }
                        else{
                            $firstColumn = 'F';
                            $lastColumn = 'G';
                        }

                        $spreadsheet->getActiveSheet()->getStyle('A9:'.$lastColumn.($max_kolom+1))->applyFromArray($styleArray);

                        // Director Signature Row
                        $spreadsheet->getActiveSheet()
                        ->setCellValue($firstColumn.$kolom, 'Direktur Sekolah MUDA');
                        $spreadsheet->getActiveSheet()->mergeCells($firstColumn.$kolom.':'.$lastColumn.$kolom);

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

                        $spreadsheet->getActiveSheet()->getStyle($firstColumn.$kolom.':'.$lastColumn.$kolom)->applyFromArray($styleArray);

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

                        $spreadsheet->getActiveSheet()->getStyle($firstColumn.$kolom.':'.$lastColumn.$kolom)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->mergeCells($firstColumn.$kolom.':'.$lastColumn.$kolom);
                        $spreadsheet->getActiveSheet()->getRowDimension($kolom)->setRowHeight(30);
                        $kolom ++;

                        $jabatan = Jabatan::where('code','19')->first();
                        $pejabat = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){$q->aktif();})->first()->pegawai;

                        $spreadsheet->getActiveSheet()
                        ->setCellValue($firstColumn.$kolom, $ppaAktif->director_acc_status_id == 1 ? $ppaAktif->accDirektur->name : ($pejabat ? $pejabat->name : '...'));

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

                        $spreadsheet->getActiveSheet()->getStyle($firstColumn.$kolom.':'.$lastColumn.$kolom)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->mergeCells($firstColumn.$kolom.':'.$lastColumn.$kolom);
                        $kolom++;

                        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

                        $headers = [
                            'Cache-Control' => 'max-age=0',
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment;filename="ppa_'.($isKso?'kso_':null).$ppaAktif->numberAsName.'.xlsx"',
                        ];

                        return response()->stream(function()use($writer){
                            $writer->save('php://output');
                        }, 200, $headers);
                    }
                    else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                }
                else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else return redirect()->route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('ppa.index');
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
            $exclusiveCount = $ppa->detail()->whereHas('akun',function($q){$q->where('is_exclusive', 1);})->count();

            if($exclusiveCount < 1){
                $isKso = $ppa->jenisAnggaranAnggaran->jenis->isKso;
                foreach($ppa->detail as $ppaDetail){
                    $apbyDetail = $ppaDetail->akun->apby()->where('account_id',$ppaDetail->account_id);
                    if($isKso)
                        $apbyDetail = $apbyDetail->whereHas('apby',function($q)use($ppa){$q->where([$yearAttr => ($yearAttr == 'year' ? $ppa->year : $ppa->academic_year_id),'budgeting_budgeting_type_id' => $ppa->budgeting_budgeting_type_id,'director_acc_status_id' => 1])->aktif()->unfinal()->latest();})->first();
                    else
                        $apbyDetail = $apbyDetail->whereHas('apby',function($q)use($ppa){$q->where([$yearAttr => ($yearAttr == 'year' ? $ppa->year : $ppa->academic_year_id),'budgeting_budgeting_type_id' => $ppa->budgeting_budgeting_type_id,'president_acc_status_id' => 1])->aktif()->unfinal()->latest();})->first();
                    if($apbyDetail){
                        $apbyDetail->used += $ppaDetail->value;
                        $apbyDetail->balance -= $ppaDetail->value;
                        $apbyDetail->save();
                    }
                }
                $apby = $ppa->jenisAnggaranAnggaran->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->unfinal()->latest();
                
                $apby = $isKso ? $apby->where('director_acc_status_id', 1)->first() : $apby->where('president_acc_status_id', 1)->first();

                $apby->total_used += $bbkDetail->ppa_value;
                $apby->total_balance -= $bbkDetail->ppa_value;
                $apby->save();
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
}

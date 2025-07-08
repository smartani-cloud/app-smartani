<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Unit;

class DasborBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Dashboard BMS';
        $this->route = 'bms.dasbor';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$jenis = null)
    {
        if(!isset($jenis) || (isset($jenis) && !in_array($jenis,['berkala','tunai']))){
            $jenis = 'tunai';
        }

        $bmsCalon = BmsCalonSiswa::has('termin')->where('register_paid','>',0)->whereHas('siswa',function($q){
            $q->where('status_id','!=',7);
        });
        $bmsSiswa = BMS::has('termin')->where('register_paid','>',0);

        if($jenis == 'berkala'){
            $bmsCalon = $bmsCalon->whereHas('tipe',function($q){
                $q->where('bms_type','Berkala 1');
            });
            $bmsSiswa = $bmsSiswa->whereHas('tipe',function($q){
                $q->where('bms_type','Berkala 1');
            });
        }
        else{
            $bmsCalon = $bmsCalon->where(function($q){
                $q->whereHas('tipe',function($q){
                    $q->where('bms_type','Tunai');
                })->orWhereNull('bms_type_id');
            });
            $bmsSiswa = $bmsSiswa->where(function($q){
                $q->whereHas('tipe',function($q){
                    $q->where('bms_type','Tunai');
                })->orWhereNull('bms_type_id');
            });
        }

        if($request->user()->pegawai->unit_id == 5){
            // $units = Unit::select('id','name')->where(function($q){
            //     $q->whereHas('bmsSiswa',function($q){
            //         $q->whereIn('id',$bmsSiswaIds);
            //     })->whereHas('bmsCalon',function($q){
            //         $q->whereIn('id',$bmsCalonIds);
            //     });
            // })->get();
            $units = Unit::select('id','name')->sekolah()->get();
            $unit_id = $units && count($units) > 0 ? $units->first()->id : null;
            if($request->unit_id && $units && $units->where('id',$request->unit_id)->count() > 0){
                $unit_id = $request->unit_id;
            }
        }else{
            $units = Unit::select('id','name')->where('id',$request->user()->pegawai->unit_id)->get();
            $unit_id = $request->user()->pegawai->unit_id;
            if($request->unit_id && $units->where('id',$request->unit_id)->count() > 0){
                $unit_id = $request->unit_id;
            }
        }

        $academicYears = TahunAjaran::where(function($q)use($jenis){
            $q->whereHas('bmsTermin',function($q)use($jenis){
                $q->where(function($q)use($jenis){
                    $q->where(function($q)use($jenis){
                        $q->where('is_student',0)->whereHas('bmsCalon',function($q)use($jenis){
                            $q->when($jenis == 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Berkala 1');
                                });
                            })->when($jenis != 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Tunai');
                                })->orWhereNull('bms_type_id');
                            })->where('register_paid','>',0)->whereHas('siswa',function($q){
                                $q->where('status_id','!=',7);
                            });
                        });
                    })->orWhere(function($q)use($jenis){
                        $q->where('is_student',1)->whereHas('bmsSiswa',function($q)use($jenis){
                            $q->when($jenis == 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Berkala 1');
                                });
                            })->when($jenis != 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Tunai');
                                })->orWhereNull('bms_type_id');
                            })->where('register_paid','>',0);
                        });
                    });
                });
            })->orWhere('is_active',1);
        })->orderBy('academic_year_start','desc')->get();

        $tahun_akademik_aktif = TahunAjaran::where('is_active',1)->first();

        $year = $request->year && $academicYears->where('id',$request->year)->count() > 0 ? $request->year : $tahun_akademik_aktif->id;

        $lists = null;

        $bmsCalonObj = $bmsCalon->orderBy('unit_id','asc')->whereHas('termin',function($q)use($year){
            $q->where(['academic_year_id' => $year,'is_student' => 0]);
        });
        $bmsSiswaObj = $bmsSiswa->orderBy('unit_id','asc')->whereHas('termin',function($q)use($year){
            $q->where(['academic_year_id' => $year,'is_student' => 1]);
        });

        if($request->user()->pegawai->unit_id == 5){
            $bmsCalonObj = $bmsCalonObj->where('unit_id',$unit_id);
            $bmsSiswaObj = $bmsSiswaObj->where('unit_id',$unit_id);
        }else{
            $bmsCalonObj = $bmsCalonObj->where('unit_id',$request->user()->pegawai->unit_id);
            $bmsSiswaObj = $bmsSiswaObj->where('unit_id',$request->user()->pegawai->unit_id);
        }

        $bmsArr = [
            [
                'name' => 'Calon Siswa',
                'object' => $bmsCalonObj
            ],
            [
                'name' => 'Siswa',
                'object' => $bmsSiswaObj
            ],
            [
                'name' => 'Total BMS',
                'object' => null
            ]
        ];

        foreach($bmsArr as $b){
            if($b['name'] != 'Total BMS'){
                $bms = $b['object'];
                $bmsCount = $bms->count();

                if($bmsCount > 0){
                    if($jenis == 'berkala'){
                        $bmsIds = clone $bms;
                        $termins = BmsTermin::where(['academic_year_id' => $year,'is_student' => 0])->whereHas('bmsCalon',function($q)use($request,$unit_id){
                            $q->whereHas('tipe',function($q){
                                $q->where('bms_type','Berkala 1');
                            })->when($request->user()->pegawai->unit_id == 5, function ($q)use($unit_id){
                                $q->where('unit_id',$unit_id);
                            })->when($request->user()->pegawai->unit_id != 5, function ($q)use($request){
                                $q->where('unit_id',$request->user()->pegawai->unit_id);
                            })->where('register_paid','>',0)->whereHas('siswa',function($q){
                                $q->where('status_id','!=',7);
                            });
                        });

                        //$firstTerminIds = array();
                        $firstTermins = null;
                        $loopTermins = clone $termins;
                        foreach($loopTermins->get() as $t){
                            if($t->indexNumber == 1){
                                //array_push($firstTerminIds,$t->id);
                                $newTermin = collect([
                                    [
                                        'id' => $t->id,
                                        'bms_id' => $t->bms->id,
                                        'register_nominal' => $t->bms->register_nominal,
                                        'register_paid' => $t->bms->register_paid,
                                        'register_remain' => $t->bms->register_remain,
                                        'bms_deduction' => $t->bms->bms_deduction,
                                    ]
                                ]);
                                $firstTermins = isset($firstTermins) ? $firstTermins->concat($newTermin) : $newTermin;
                            }
                        }
                        //return implode(', ', $firstTerminIds);
                        // $bms = $bms->select('id','register_nominal','register_paid','register_remain','bms_deduction')->whereHas('termin',function($q)use($firstTerminIds){
                        //     $q->whereIn('id',$firstTerminIds);
                        // });

                        $summary = collect([
                            [
                                'name' => $b['name'],
                                'total' => $termins->sum('nominal')+($firstTermins ? $firstTermins->sum('register_nominal')+$firstTermins->sum('bms_deduction') : 0),
                                'deduction' => $firstTermins ? $firstTermins->sum('bms_deduction') : 0,
                                'nominal' => $termins->sum('nominal')+($firstTermins ? $firstTermins->sum('register_nominal') : 0),
                                'paid' => ($termins->sum('nominal')-$termins->sum('remain'))+($firstTermins ? $firstTermins->sum('register_paid') : 0),
                                'remain' => $termins->sum('remain')+($firstTermins ? $firstTermins->sum('register_remain') : 0)
                            ]
                        ]);
                    }
                    else{
                        $summary = collect([
                            [
                                'name' => $b['name'],
                                'total' => $bms->sum('bms_nominal')+$bms->sum('bms_deduction'),
                                'deduction' => $bms->sum('bms_deduction'),
                                'nominal' => $bms->sum('bms_nominal'),
                                'paid' => $bms->sum('bms_paid'),
                                'remain' => $bms->sum('bms_remain')
                            ]
                        ]);
                    }
                }
                else{
                    $summary = collect([
                        [
                            'name' => $b['name'],
                            'total' => 0,
                            'deduction' => 0,
                            'nominal' => 0,
                            'paid' => 0,
                            'remain' => 0
                        ]
                    ]);
                }
            }
            else{
                if($lists){
                    $summary = collect([
                        [
                            'name' => $b['name'].' '.ucwords($jenis),
                            'total' => $lists->sum('total'),
                            'deduction' => $lists->sum('deduction'),
                            'nominal' => $lists->sum('nominal'),
                            'paid' => $lists->sum('paid'),
                            'remain' => $lists->sum('remain')
                        ]
                    ]);
                }
                else{
                    $summary = collect([
                        [
                            'name' => $b['name'].' '.ucwords($jenis),
                            'total' => 0,
                            'deduction' => 0,
                            'nominal' => 0,
                            'paid' => 0,
                            'remain' => 0
                        ]
                    ]);
                }
            }

            if($lists){
                $lists = $lists->concat($summary);
            }
            else{
                $lists = $summary;
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','lists','units','unit_id','academicYears','year','jenis'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGet(Request $request,$jenis = null)
    {
        if(!isset($jenis) || (isset($jenis) && !in_array($jenis,['berkala','tunai']))){
            $jenis = 'tunai';
        }

        $bmsCalon = BmsCalonSiswa::has('termin')->where('register_paid','>',0)->whereHas('siswa',function($q){
            $q->where('status_id','!=',7);
        });
        $bmsSiswa = BMS::has('termin')->where('register_paid','>',0);

        if($jenis == 'berkala'){
            $bmsCalon = $bmsCalon->whereHas('tipe',function($q){
                $q->where('bms_type','Berkala 1');
            });
            $bmsSiswa = $bmsSiswa->whereHas('tipe',function($q){
                $q->where('bms_type','Berkala 1');
            });
        }
        else{
            $bmsCalon = $bmsCalon->where(function($q){
                $q->whereHas('tipe',function($q){
                    $q->where('bms_type','Tunai');
                })->orWhereNull('bms_type_id');
            });
            $bmsSiswa = $bmsSiswa->where(function($q){
                $q->whereHas('tipe',function($q){
                    $q->where('bms_type','Tunai');
                })->orWhereNull('bms_type_id');
            });
        }

        if($request->user()->pegawai->unit_id == 5){
            $units = Unit::select('id','name')->sekolah()->get();
            $unit_id = $units && count($units) > 0 ? $units->first()->id : null;
            if($request->unit_id && $units && $units->where('id',$request->unit_id)->count() > 0){
                $unit_id = $request->unit_id;
            }
        }else{
            $units = Unit::select('id','name')->where('id',$request->user()->pegawai->unit_id)->get();
            $unit_id = $request->user()->pegawai->unit_id;
            if($request->unit_id && $units->where('id',$request->unit_id)->count() > 0){
                $unit_id = $request->unit_id;
            }
        }

        $academicYears = TahunAjaran::where(function($q)use($jenis){
            $q->whereHas('bmsTermin',function($q)use($jenis){
                $q->where(function($q)use($jenis){
                    $q->where(function($q)use($jenis){
                        $q->where('is_student',0)->whereHas('bmsCalon',function($q)use($jenis){
                            $q->when($jenis == 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Berkala 1');
                                });
                            })->when($jenis != 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Tunai');
                                })->orWhereNull('bms_type_id');
                            })->where('register_paid','>',0)->whereHas('siswa',function($q){
                                $q->where('status_id','!=',7);
                            });
                        });
                    })->orWhere(function($q)use($jenis){
                        $q->where('is_student',1)->whereHas('bmsSiswa',function($q)use($jenis){
                            $q->when($jenis == 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Berkala 1');
                                });
                            })->when($jenis != 'berkala', function ($q) {
                                $q->whereHas('tipe',function($q){
                                    $q->where('bms_type','Tunai');
                                })->orWhereNull('bms_type_id');
                            })->where('register_paid','>',0);
                        });
                    });
                });
            })->orWhere('is_active',1);
        })->orderBy('academic_year_start','desc')->get();

        $tahun_akademik_aktif = TahunAjaran::where('is_active',1)->first();

        $year = $request->year && $academicYears->where('id',$request->year)->count() > 0 ? $request->year : $tahun_akademik_aktif->id;

        $datas = null;

        $bmsCalonObj = $bmsCalon->orderBy('unit_id','asc')->whereHas('termin',function($q)use($year){
            $q->where(['academic_year_id' => $year,'is_student' => 0]);
        });
        $bmsSiswaObj = $bmsSiswa->orderBy('unit_id','asc')->whereHas('termin',function($q)use($year){
            $q->where(['academic_year_id' => $year,'is_student' => 1]);
        });

        if($request->user()->pegawai->unit_id == 5){
            $bmsCalonObj = $bmsCalonObj->where('unit_id',$unit_id);
            $bmsSiswaObj = $bmsSiswaObj->where('unit_id',$unit_id);
        }else{
            $bmsCalonObj = $bmsCalonObj->where('unit_id',$request->user()->pegawai->unit_id);
            $bmsSiswaObj = $bmsSiswaObj->where('unit_id',$request->user()->pegawai->unit_id);
        }

        $bmsArr = [
            [
                'name' => 'Calon Siswa',
                'object' => $bmsCalonObj
            ],
            [
                'name' => 'Siswa',
                'object' => $bmsSiswaObj
            ],
            [
                'name' => 'Total BMS',
                'object' => null
            ]
        ];

        foreach($bmsArr as $b){
            if($b['name'] != 'Total BMS'){
                $bms = $b['object'];
                $bmsCount = $bms->count();

                if($bmsCount > 0){
                    if($jenis == 'berkala'){
                        $bmsIds = clone $bms;
                        $termins = BmsTermin::where(['academic_year_id' => $year,'is_student' => 0])->whereHas('bmsCalon',function($q)use($request,$unit_id){
                            $q->whereHas('tipe',function($q){
                                $q->where('bms_type','Berkala 1');
                            })->when($request->user()->pegawai->unit_id == 5, function ($q)use($unit_id){
                                $q->where('unit_id',$unit_id);
                            })->when($request->user()->pegawai->unit_id != 5, function ($q)use($request){
                                $q->where('unit_id',$request->user()->pegawai->unit_id);
                            })->where('register_paid','>',0)->whereHas('siswa',function($q){
                                $q->where('status_id','!=',7);
                            });
                        });

                        //$firstTerminIds = array();
                        $firstTermins = null;
                        $loopTermins = clone $termins;
                        foreach($loopTermins->get() as $t){
                            if($t->indexNumber == 1){
                                //array_push($firstTerminIds,$t->id);
                                $newTermin = collect([
                                    [
                                        'id' => $t->id,
                                        'bms_id' => $t->bms->id,
                                        'register_nominal' => $t->bms->register_nominal,
                                        'register_paid' => $t->bms->register_paid,
                                        'register_remain' => $t->bms->register_remain,
                                        'bms_deduction' => $t->bms->bms_deduction,
                                    ]
                                ]);
                                $firstTermins = isset($firstTermins) ? $firstTermins->concat($newTermin) : $newTermin;
                            }
                        }
                        //return implode(', ', $firstTerminIds);
                        // $bms = $bms->select('id','register_nominal','register_paid','register_remain','bms_deduction')->whereHas('termin',function($q)use($firstTerminIds){
                        //     $q->whereIn('id',$firstTerminIds);
                        // });

                        $summary = collect([
                            [
                                'name' => $b['name'],
                                'total' => $termins->sum('nominal')+($firstTermins ? $firstTermins->sum('register_nominal')+$firstTermins->sum('bms_deduction') : 0),
                                'deduction' => $firstTermins ? $firstTermins->sum('bms_deduction') : 0,
                                'nominal' => $termins->sum('nominal')+($firstTermins ? $firstTermins->sum('register_nominal') : 0),
                                'paid' => ($termins->sum('nominal')-$termins->sum('remain'))+($firstTermins ? $firstTermins->sum('register_paid') : 0),
                                'remain' => $termins->sum('remain')+($firstTermins ? $firstTermins->sum('register_remain') : 0)
                            ]
                        ]);
                    }
                    else{
                        $summary = collect([
                            [
                                'name' => $b['name'],
                                'total' => $bms->sum('bms_nominal')+$bms->sum('bms_deduction'),
                                'deduction' => $bms->sum('bms_deduction'),
                                'nominal' => $bms->sum('bms_nominal'),
                                'paid' => $bms->sum('bms_paid'),
                                'remain' => $bms->sum('bms_remain')
                            ]
                        ]);
                    }
                }
                else{
                    $summary = collect([
                        [
                            'name' => $b['name'],
                            'total' => 0,
                            'deduction' => 0,
                            'nominal' => 0,
                            'paid' => 0,
                            'remain' => 0
                        ]
                    ]);
                }
            }
            else{
                if($datas){
                    $summary = collect([
                        [
                            'name' => $b['name'].' '.ucwords($jenis),
                            'total' => $datas->sum('total'),
                            'deduction' => $datas->sum('deduction'),
                            'nominal' => $datas->sum('nominal'),
                            'paid' => $datas->sum('paid'),
                            'remain' => $datas->sum('remain')
                        ]
                    ]);
                }
                else{
                    $summary = collect([
                        [
                            'name' => $b['name'].' '.ucwords($jenis),
                            'total' => 0,
                            'deduction' => 0,
                            'nominal' => 0,
                            'paid' => 0,
                            'remain' => 0
                        ]
                    ]);
                }
            }

            if($datas){
                $datas = $datas->concat($summary);
            }
            else{
                $datas = $summary;
            }
        }

        return response()->json($datas,200);
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

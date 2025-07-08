<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Kbm\TahunAjaran;

use App\Models\Anggaran\JenisAnggaranAnggaran;
use App\Models\Ppa\PpaProposal;
use App\Models\Ppa\PpaProposalDetail;
use App\Models\Unit;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProposalPpaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.read-only.';
        $this->active = 'Proposal PPA';
        $this->route = 'proposal-ppa';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $status = 'menunggu';
        if(isset($request->status) && $request->status != 'menunggu'){
            if(in_array($request->status,['diajukan'])) $status = $request->status;
        }

        $isDynamic = true;
        $isYear = false;
        $years = null;

        $yearAttr = $isYear ? 'year' : 'academic_year_id';

        $thisYear = $isYear ? Date::now('Asia/Jakarta')->format('Y') : TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();

        $myBudgetings = $request->user()->pegawai->jabatan->budgetUsers()->when($request->user()->pegawai->unit_id != 5,function($q)use($request){
            return $q->where('unit_id',$request->user()->pegawai->unit_id);
        })->whereHas('jenisAnggaran.tahuns',function($q)use($yearAttr,$thisYear){
            $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
        })->get();
        $isPa = $myBudgetings && count($myBudgetings) > 0 ? true : false;

        /* isAnggotaPa? */

        // Check candidate budgets
        $jenisAnggaran = JenisAnggaranAnggaran::whereHas('tahuns',function($q)use($yearAttr,$thisYear){
            $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
        })->whereHas('anggaran',function($q)use($request){
            $q->when($request->user()->pegawai->unit_id == 5,function($q)use($request){
                $q->where('unit_id',$request->user()->pegawai->unit_id)->where('position_id',$request->user()->pegawai->position_id);
            },function($q)use($request){
                $q->where('unit_id',$request->user()->pegawai->unit_id);
            });
        })->with('anggaran')->get();

        // Status
        $isAnggotaPa = false;
        // Available budgets
        $anggarans = null;

        // Confirm valid candidate budgets
        if(in_array($role,['wakasek','keu']) && $jenisAnggaran && count($jenisAnggaran) > 0){
            $anggarans = $jenisAnggaran->pluck('anggaran')->unique();
            foreach($anggarans as $a){
                $checkRole = $this->checkRole($a,$role);
                if($checkRole){
                    if(!$isAnggotaPa) $isAnggotaPa = $checkRole;
                }
                else{
                    $anggarans->diff([$a]);
                }
            }
        }

        /* End of isAnggotaPa */

        $data = PpaProposal::latest();
        if(!in_array($role,['ketuayys','direktur','fam','faspv'])){
            $data = $data->when($isPa,function($q)use($request,$myBudgetings){
                return $q->where(function($q)use($request,$myBudgetings){
                    $q->where([
                        'employee_id' => $request->user()->pegawai->id,
                        'unit_id' => $request->user()->pegawai->unit_id,
                        'position_id' => $request->user()->pegawai->position_id,
                    ])->orWhereIn('budgeting_id', $myBudgetings->pluck('id')->unique()->toArray());
                });
            },function($q)use($request,$role,$isAnggotaPa,$anggarans){
                return $q->when(in_array($role,['wakasek','keu']) && $isAnggotaPa,function($q)use($request,$anggarans){
                    return $q->where(function($q)use($request,$anggarans){
                        $q->where([
                            'employee_id' => $request->user()->pegawai->id,
                            'unit_id' => $request->user()->pegawai->unit_id,
                            'position_id' => $request->user()->pegawai->position_id,
                        ])->orWhereIn('budgeting_id', $anggarans->pluck('id')->toArray());
                    });
                },function($q)use($request){
                    return $q->where([
                        'employee_id' => $request->user()->pegawai->id,
                        'unit_id' => $request->user()->pegawai->unit_id,
                        'position_id' => $request->user()->pegawai->position_id,
                    ]);
                });
            });
        }
        else{
            if(isset($request->filter) && $request->filter == '3' && $isPa) $data = $data->whereIn('budgeting_id', $myBudgetings->pluck('id')->unique()->toArray());
            else{
                $data = $data->where(function($q)use($request){
                    $q->where([
                        'employee_id' => $request->user()->pegawai->id,
                        'unit_id' => $request->user()->pegawai->unit_id,
                        'position_id' => $request->user()->pegawai->position_id,
                    ])->orWhereNotNull('budgeting_id');
                });
            }
        }

        if($isYear){
            // If use year
            $year = isset($request->year) ? $request->year : null;
            if(!$year){
                $year = $thisYear;
            }

            $years = PpaProposal::select('year')->orderBy('year')->pluck('year')->unique();

        }
        else{
            // If use academic year
            $years = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->take(1)->get();

            if($data->count() > 0){
                $years = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where(function($q)use($role,$request){
                    $q->where(function($q){
                        $q->where('is_finance_year',1);
                    })->orWhere(function($q)use($role,$request){
                        $q->when(in_array($role,['ketuayys','direktur','fam','faspv']),function($q){
                            return $q->has('proposalPpas');
                        },function($q)use($request){
                            return $q->whereHas('proposalPpas',function($q)use($request){
                                $q->where([
                                    'employee_id' => $request->user()->pegawai->id,
                                    'unit_id' => $request->user()->pegawai->unit_id,
                                    'position_id' => $request->user()->pegawai->position_id,
                                ]);
                            });
                        });
                    });
                })->latest()->get();
            }

            if($request->year){
                $year = str_replace("-","/",$request->year);
                $year = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('academic_year',$year)->first();
            }
            else{
                $year = $thisYear;
            }
            if(!$year) return redirect()->route($this->route.'.index');
        }

        $data = $status == 'menunggu' ? $data->doesntHave('ppa') : $data->has('ppa');
        if($year) $data = $data->where($yearAttr,($yearAttr == 'year' ? $year : $year->id));
        $data = $data->get();
        $used = null;
        foreach($data as $d){
            if($d->ppa) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'_index', compact('data','used','active','route','isYear','years','year','status','isPa','myBudgetings','isAnggotaPa','anggarans','isDynamic'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $messages = [
            'title.required' => 'Mohon tuliskan deskripsi yang diajukan'
        ];

        $this->validate($request, [
            'title' => 'required'
        ], $messages);

        $thisYear = Date::now('Asia/Jakarta')->format('Y');
        //$thisDate = Date::now('Asia/Jakarta')->format('Y-m-d');

        $count = PpaProposal::where([
            'date' => null,
            'title' => $request->title,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->count();

        if($count < 1){
            $tahun = TahunAjaran::select('id')->where('is_finance_year',1)->latest()->first();

            $item = new PpaProposal();
            $item->date = null;
            $item->year = $thisYear;
            $item->academic_year_id = $tahun->id;
            $item->title = $request->title;
            if(isset($request->desc)) $item->desc = $request->desc;
            $item->employee_id = $request->user()->pegawai->id;
            $item->unit_id = $request->user()->pegawai->unit_id;
            $item->position_id = $request->user()->pegawai->position_id;
            $item->save();

            $item->fresh();

            Session::flash('success','Data '.$request->title.' berhasil ditambahkan');

            return redirect()->route($this->route.'.detail.show',['id' => $item->id]);
        }

        else{
            Session::flash('danger','Data proposal sudah pernah ditambahkan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detailStore(Request $request,$id)
    {
        $messages = [
            'desc.required' => 'Mohon tuliskan deskripsi yang diajukan',
            'price.required' => 'Mohon masukkan harga yang diajukan',
            'qty.required' => 'Mohon masukkan kuantitas yang diajukan',
        ];

        $this->validate($request, [
            'desc' => 'required',
            'price' => 'required',
            'qty' => 'required',
        ], $messages);

        $price = (int)str_replace('.','',$request->price);
        $qty = (int)str_replace('.','',$request->qty);

        $data = PpaProposal::where([
            'id' => $id,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id
        ])->doesntHave('ppa')->first();

        if($data){
            $proposalDetail = $data->details()->where([
                'desc' => $request->desc,
                'price' => $price,
                'quantity' => $qty
            ]);

            if($proposalDetail->count() < 1){
                $item = new PpaProposalDetail();
                $item->desc = $request->desc;
                $item->price = $price;
                $item->quantity = $qty;
                $item->value = $price*$qty;
                $item->price_ori = $price;
                $item->quantity_ori = $qty;
                $item->employee_id = $request->user()->pegawai->id;
                
                $data->details()->save($item);

                $data->update([
                    'total_value' => $data->details()->sum('value'),
                    'declined_at' => null
                ]);

                $item->fresh();

                Session::flash('success','Data '.$request->desc.' berhasil ditambahkan');

                return redirect()->route($this->route.'.detail.show',['id' => $data->id]);
            }
            else{
                $proposalDetail = $proposalDetail->first();

                Session::flash('danger','Data pengajuan sudah pernah ditambahkan');

                return redirect()->route($this->route.'.detail.show',['id' => $proposalDetail->id]);
            }
        }
        else{
            Session::flash('danger','Data proposal tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $role = $request->user()->role->name;

        $data = null;        

        if($request->id){
            $isYear = false;
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $thisYear = $isYear ? Date::now('Asia/Jakarta')->format('Y') : TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();

            $myBudgetings = $request->user()->pegawai->jabatan->budgetUsers()->when($request->user()->pegawai->unit_id != 5,function($q)use($request){
                return $q->where('unit_id',$request->user()->pegawai->unit_id);
            })->whereHas('jenisAnggaran.tahuns',function($q)use($yearAttr,$thisYear){
                $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
            })->get();
            $isPa = $myBudgetings && count($myBudgetings) > 0 ? true : false;

            /* isAnggotaPa? */

            // Check candidate budgets
            $jenisAnggaran = JenisAnggaranAnggaran::whereHas('tahuns',function($q)use($yearAttr,$thisYear){
                $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
            })->whereHas('anggaran',function($q)use($request){
                $q->when($request->user()->pegawai->unit_id == 5,function($q)use($request){
                    $q->where('unit_id',$request->user()->pegawai->unit_id)->where('position_id',$request->user()->pegawai->position_id);
                },function($q)use($request){
                    $q->where('unit_id',$request->user()->pegawai->unit_id);
                });
            })->with('anggaran')->get();

            // Status
            $isAnggotaPa = false;
            // Available budgets
            $anggarans = null;

            // Confirm valid candidate budgets
            if(in_array($role,['wakasek','keu']) && $jenisAnggaran && count($jenisAnggaran) > 0){
                $anggarans = $jenisAnggaran->pluck('anggaran')->unique();
                foreach($anggarans as $a){
                    $checkRole = $this->checkRole($a,$role);
                    if($checkRole){
                        if(!$isAnggotaPa) $isAnggotaPa = $checkRole;
                    }
                    else{
                        $anggarans->diff([$a]);
                    }
                }
            }

            /* End of isAnggotaPa */

            $data = PpaProposal::where('id', $request->id);
            if(!in_array($role,['ketuayys','direktur','fam','faspv'])){
                $data = $data->when($isPa,function($q)use($request,$myBudgetings){
                    return $q->where(function($q)use($request,$myBudgetings){
                        $q->where([
                            'employee_id' => $request->user()->pegawai->id,
                            'unit_id' => $request->user()->pegawai->unit_id,
                            'position_id' => $request->user()->pegawai->position_id,
                        ])->orWhereIn('budgeting_id', $myBudgetings->pluck('id')->unique()->toArray());
                    });
                },function($q)use($request,$role,$isAnggotaPa,$anggarans){
                    return $q->when(in_array($role,['wakasek','keu']) && $isAnggotaPa,function($q)use($request,$anggarans){
                        return $q->where(function($q)use($request,$anggarans){
                            $q->where([
                                'employee_id' => $request->user()->pegawai->id,
                                'unit_id' => $request->user()->pegawai->unit_id,
                                'position_id' => $request->user()->pegawai->position_id,
                            ])->orWhereIn('budgeting_id', $anggarans->pluck('id')->toArray());
                        });
                    },function($q)use($request){
                        return $q->where([
                            'employee_id' => $request->user()->pegawai->id,
                            'unit_id' => $request->user()->pegawai->unit_id,
                            'position_id' => $request->user()->pegawai->position_id,
                        ]);
                    });
                });
            }
            $data = $data->first();
        }

        $active = $this->active;
        $route = $this->route;

        if($data){
            $editable = !$data->ppa && !$data->budgeting_id && $request->user()->pegawai->id == $data->employee_id ? true : false;
            $isDynamic = true;
            $isWithTrashed = false;
            if(!$isDynamic) $isWithTrashed = true;

            if($isYear){
                $year = Date::now('Asia/Jakarta')->format('Y');
            }
            else{
                $year = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();
            }

            $budgetings = JenisAnggaranAnggaran::select('id','budgeting_id')->whereHas('tahuns',function($q)use($yearAttr,$year){
                $q->where($yearAttr,($yearAttr == 'year' ? $year : $year->id));
            })->with('anggaran:id,name,acc_position_id')->get()->pluck('anggaran')->unique();

            return view($this->template.$route.'_detail', compact('data','active','route','id','editable','isDynamic','isWithTrashed','isYear','year','budgetings'));
        }

        return redirect()->route($route.'.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detailModal(Request $request,$id)
    {
        $role = $request->user()->role->name;

        $data = $request->id ? PpaProposal::where('id', $request->id)->has('ppa')->first() : null;

        $active = $this->active;
        $route = $this->route;

        if($data){
            return view($this->template.$route.'_detail_modal', compact('data','active','route','id'));
        }

        return "Ups, tidak dapat memuat data";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data = $request->id ? PpaProposal::where([
            'id' => $request->id,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->doesntHave('ppa')->first() : null;

        $isYear = false;
        $yearAttr = $isYear ? 'year' : 'academic_year_id';
        $thisYear = $isYear ? Date::now('Asia/Jakarta')->format('Y') : TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();

        $myBudgetings = $request->user()->pegawai->jabatan->budgetUsers()->when($request->user()->pegawai->unit_id != 5,function($q)use($request){
            return $q->where('unit_id',$request->user()->pegawai->unit_id);
        })->whereHas('jenisAnggaran.tahuns',function($q)use($yearAttr,$thisYear){
            $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
        })->get();
        $isPa = $myBudgetings && count($myBudgetings) > 0 ? true : false;

        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'_edit', compact('data','active','route','isPa'));
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
        $item = PpaProposal::where([
            'id' => $request->id,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->doesntHave('ppa')->first();

        if($item){
            $isYear = false;
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $thisYear = $isYear ? Date::now('Asia/Jakarta')->format('Y') : TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();

            $myBudgetings = $request->user()->pegawai->jabatan->budgetUsers()->when($request->user()->pegawai->unit_id != 5,function($q)use($request){
                return $q->where('unit_id',$request->user()->pegawai->unit_id);
            })->whereHas('jenisAnggaran.tahuns',function($q)use($yearAttr,$thisYear){
                $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
            })->get();
            $isPa = $myBudgetings && count($myBudgetings) > 0 ? true : false;

            if(!$item->anggaran){
                $messages = [
                    'editTitle.required' => 'Mohon tuliskan nama proposal yang diajukan',
                ];
            
                $this->validate($request, [
                    'editTitle' => 'required',
                ], $messages);

                $count = PpaProposal::where([
                    'date' => Date::now('Asia/Jakarta')->format('Y-m-d'),
                    'title' => $request->editTitle,
                    'employee_id' => $request->user()->pegawai->id,
                    'unit_id' => $request->user()->pegawai->unit_id,
                    'position_id' => $request->user()->pegawai->position_id,
                ])->doesntHave('ppa')->where('id','!=',$request->id)->count();

                if($count < 1){
                    $old = $item->title;
                    $item->title = $request->editTitle;
                    if($old != $request->editTitle && $item->declined_at) $item->declined_at = null;
                    $item->desc = isset($request->editDesc) ? $request->editDesc : null;
                    $item->save();
                    
                    if($old != $item->title)
                        Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->title);
                    else
                        Session::flash('success','Perubahan deskripsi data pengajuan berhasil disimpan');
                }
                else Session::flash('danger','Perubahan gagal disimpan. Sepertinya data pengajuan sudah ada di daftar pengajuan.');
            }
            elseif($isPa){
                $item->desc = isset($request->editDesc) ? $request->editDesc : null;
                $item->save();
                    
                Session::flash('success','Perubahan deskripsi data pengajuan berhasil disimpan');
            }
            else Session::flash('danger','Perubahan data pengajuan gagal disimpan');
        }
        else Session::flash('danger','Perubahan data pengajuan gagal disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detailUpdate(Request $request, $id)
    {
        $data = PpaProposal::where([
            'id' => $id,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->doesntHave('ppa')->first();

        if($data){
            if(isset($request->editId)){
                $item = explode('-',$request->editId)[1];
                $proposalDetail = $data->details()->where('id',$item)->first();

                if($proposalDetail){
                    $edited = 0;
                    if(isset($request->editDesc) && $proposalDetail->desc != $request->editDesc){
                        $proposalDetail->desc = $request->editDesc;
                        $edited++;
                    }
                    if(isset($request->editPrice)){
                        $requestPrice = (int)str_replace('.','',$request->editPrice);
                        if($proposalDetail->price_ori != $requestPrice){
                            $proposalDetail->price = $requestPrice;
                            $proposalDetail->price_ori = $requestPrice;
                            $edited++;
                        }
                    }
                    if(isset($request->editQty)){
                        $requestQty = (int)str_replace('.','',$request->editQty);
                        if($proposalDetail->quantity_ori != $requestQty){
                            $proposalDetail->quantity = $requestQty;
                            $proposalDetail->quantity_ori = $requestQty;
                            $edited++;
                        }
                    }

                    if($edited > 0){
                        $proposalDetail->value = ($proposalDetail->price_ori)*($proposalDetail->quantity_ori);
                        $proposalDetail->save();

                        $data->update([
                            'total_value' => $data->details()->sum('value'),
                            'declined_at' => null
                        ]);

                        Session::flash('success','Data pengajuan berhasil diubah');
                    }
                }
                else Session::flash('danger','Data pengajuan gagal diubah');
            }
            else Session::flash('danger','Data pengajuan tidak ditemukan');
        }
        else Session::flash('danger','Data proposal tidak ditemukan');

        return redirect()->route($this->route.'.detail.show', ['id' => $id]);
    }

    /**
     * Update all specified resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detailUpdateAll(Request $request, $id)
    {
        $data = PpaProposal::where([
            'id' => $id,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->doesntHave('ppa')->first();

        $thisDate = Date::now('Asia/Jakarta')->format('Y-m-d');

        $isYear = false;

        if($isYear){
            $year = Date::now('Asia/Jakarta')->format('Y');
        }
        else{
            $year = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();
        }

        $yearAttr = $isYear ? 'year' : 'academic_year_id';

        $budgeting = JenisAnggaranAnggaran::select('id','budgeting_id')->whereHas('tahuns',function($q)use($yearAttr,$year){
            $q->where($yearAttr,($yearAttr == 'year' ? $year : $year->id));
        })->where('budgeting_id',$request->budgeting_id)->first();

        if($data && (!isset($request->validate) || (isset($request->validate) && $request->validate == 'validate' && $budgeting))){
            if($data->details()->count() > 0){
                $editedCount = 0;
                foreach($data->details as $detail){
                    $inputName = 'price-'.$detail->id;
                    $requestPrice = (int)str_replace('.','',$request->{$inputName});
                    $detail->price = $requestPrice;
                    $detail->price_ori = $requestPrice;

                    $inputName = 'qty-'.$detail->id;
                    $requestQuantity = (int)str_replace('.','',$request->{$inputName});
                    $detail->quantity = $requestQuantity;
                    $detail->quantity_ori = $requestQuantity;

                    if($detail->value != $requestPrice*$requestQuantity) $editedCount++;
                    $detail->value = $requestPrice*$requestQuantity;

                    $detail->save();
                }

                if($request->validate == 'validate' && !$data->declined_at){
                    $data->update([
                        'date' => $thisDate,
                        'budgeting_id' => $budgeting->budgeting_id
                    ]);
                }
                if($editedCount > 0) $data->update(['declined_at' => null]);
                $data->update(['total_value' => $data->details()->sum('value')]);

                if($request->validate == 'validate' && !$data->declined_at) Session::flash('success','Proposal PPA berhasil disimpan dan diajukan');
                else Session::flash('success','Rincian pengajuan berhasil diperbarui');
            }
            else Session::flash('danger','Data pengajuan tidak ditemukan');
        }
        else Session::flash('danger','Data proposal tidak ditemukan');

        return redirect()->route($this->route.'.detail.show', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $role = $request->user()->role->name;

        $isYear = false;
        $yearAttr = $isYear ? 'year' : 'academic_year_id';
        $thisYear = $isYear ? Date::now('Asia/Jakarta')->format('Y') : TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();

        $myBudgetings = $request->user()->pegawai->jabatan->budgetUsers()->when($request->user()->pegawai->unit_id != 5,function($q)use($request){
            return $q->where('unit_id',$request->user()->pegawai->unit_id);
        })->whereHas('jenisAnggaran.tahuns',function($q)use($yearAttr,$thisYear){
            $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
        })->get();
        $isPa = $myBudgetings && count($myBudgetings) > 0 ? true : false;

        /* isAnggotaPa? */

        // Check candidate budgets
        $jenisAnggaran = JenisAnggaranAnggaran::whereHas('tahuns',function($q)use($yearAttr,$thisYear){
            $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
        })->whereHas('anggaran',function($q)use($request){
            $q->when($request->user()->pegawai->unit_id == 5,function($q)use($request){
                $q->where('unit_id',$request->user()->pegawai->unit_id)->where('position_id',$request->user()->pegawai->position_id);
            },function($q)use($request){
                $q->where('unit_id',$request->user()->pegawai->unit_id);
            });
        })->with('anggaran')->get();

        // Status
        $isAnggotaPa = false;
        // Available budgets
        $anggarans = null;

        // Confirm valid candidate budgets
        if(in_array($role,['wakasek','keu']) && $jenisAnggaran && count($jenisAnggaran) > 0){
            $anggarans = $jenisAnggaran->pluck('anggaran')->unique();
            foreach($anggarans as $a){
                $checkRole = $this->checkRole($a,$role);
                if($checkRole){
                    if(!$isAnggotaPa) $isAnggotaPa = $checkRole;
                }
                else{
                    $anggarans->diff([$a]);
                }
            }
        }

        /* End of isAnggotaPa */

        $data = PpaProposal::where('id', $id);
        if(!in_array($role,['ketuayys','direktur','fam','faspv'])){
            $data = $data->when($isPa,function($q)use($request,$myBudgetings){
                return $q->where(function($q)use($request,$myBudgetings){
                    $q->where([
                        'employee_id' => $request->user()->pegawai->id,
                        'unit_id' => $request->user()->pegawai->unit_id,
                        'position_id' => $request->user()->pegawai->position_id,
                    ])->orWhereIn('budgeting_id', $myBudgetings->pluck('id')->unique()->toArray());
                });
            },function($q)use($request,$role,$isAnggotaPa,$anggarans){
                return $q->when(in_array($role,['wakasek','keu']) && $isAnggotaPa,function($q)use($request,$anggarans){
                    return $q->where(function($q)use($request,$anggarans){
                        $q->where([
                            'employee_id' => $request->user()->pegawai->id,
                            'unit_id' => $request->user()->pegawai->unit_id,
                            'position_id' => $request->user()->pegawai->position_id,
                        ])->orWhereIn('budgeting_id', $anggarans->pluck('id')->toArray());
                    });
                },function($q)use($request){
                    return $q->where([
                        'employee_id' => $request->user()->pegawai->id,
                        'unit_id' => $request->user()->pegawai->unit_id,
                        'position_id' => $request->user()->pegawai->position_id,
                    ]);
                });
            });
        }
        $data = $data->doesntHave('ppa')->first();

        if($data){
            $title = $data->title;
            if($data->anggaran && (($data->anggaran->acc_position_id == $request->user()->pegawai->position_id) || ($isAnggotaPa && in_array($data->anggaran->id,$anggarans->pluck('id')->toArray())))){
                $data->date = null;
                $data->budgeting_id = null;
                $data->declined_at = Date::now('Asia/Jakarta');
                $data->save();
            }
            else{
                $data->details()->forceDelete();
                $data->delete();
            }
            Session::flash('success','Data '.$title.' berhasil dihapus');
        }
        else Session::flash('danger','Data pengajuan gagal dihapus');

        $count = PpaProposal::where([
            'year' => $request->year,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->doesntHave('ppa')->count();

        $params = $count > 0 ? ['year' => $request->year, 'status' => $request->status] : null;

        return redirect()->route($this->route.'.index',$params);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $item
     * @return \Illuminate\Http\Response
     */
    public function detailDestroy(Request $request, $id, $item)
    {
        $data = PpaProposal::where([
            'id' => $id,
            'employee_id' => $request->user()->pegawai->id,
            'unit_id' => $request->user()->pegawai->unit_id,
            'position_id' => $request->user()->pegawai->position_id,
        ])->doesntHave('ppa')->first();

        if($data){
            $proposalDetail = $data->details()->where('id',$item)->first();

            if($proposalDetail){
                $desc = $proposalDetail->desc;
                $proposalDetail->forceDelete();

                $data->update([
                    'total_value' => $data->details()->sum('value'),
                    'declined_at' => null
                ]);

                Session::flash('success','Data '.$desc.' berhasil dihapus');
            }
            else Session::flash('danger','Data pengajuan gagal dihapus');
        }
        else Session::flash('danger','Data proposal tidak ditemukan');

        return redirect()->route($this->route.'.detail.show', ['id' => $id]);
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $id)
    {
        // Pre-defined formats
        $FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-';
        $FORMAT_CURRENCY_IDR = 'Rp#,##0_-';
        $FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)';

        $role = $request->user()->role->name;

        $data = null;        

        if($request->id){
            $isYear = false;
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $thisYear = $isYear ? Date::now('Asia/Jakarta')->format('Y') : TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();

            $myBudgetings = $request->user()->pegawai->jabatan->budgetUsers()->when($request->user()->pegawai->unit_id != 5,function($q)use($request){
                return $q->where('unit_id',$request->user()->pegawai->unit_id);
            })->whereHas('jenisAnggaran.tahuns',function($q)use($yearAttr,$thisYear){
                $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
            })->get();
            $isPa = $myBudgetings && count($myBudgetings) > 0 ? true : false;

            /* isAnggotaPa? */

            // Check candidate budgets
            $jenisAnggaran = JenisAnggaranAnggaran::whereHas('tahuns',function($q)use($yearAttr,$thisYear){
                $q->where($yearAttr,($yearAttr == 'year' ? $thisYear : $thisYear->id));
            })->whereHas('anggaran',function($q)use($request){
                $q->when($request->user()->pegawai->unit_id == 5,function($q)use($request){
                    $q->where('unit_id',$request->user()->pegawai->unit_id)->where('position_id',$request->user()->pegawai->position_id);
                },function($q)use($request){
                    $q->where('unit_id',$request->user()->pegawai->unit_id);
                });
            })->with('anggaran')->get();

            // Status
            $isAnggotaPa = false;
            // Available budgets
            $anggarans = null;

            // Confirm valid candidate budgets
            if(in_array($role,['wakasek','keu']) && $jenisAnggaran && count($jenisAnggaran) > 0){
                $anggarans = $jenisAnggaran->pluck('anggaran')->unique();
                foreach($anggarans as $a){
                    $checkRole = $this->checkRole($a,$role);
                    if($checkRole){
                        if(!$isAnggotaPa) $isAnggotaPa = $checkRole;
                    }
                    else{
                        $anggarans->diff([$a]);
                    }
                }
            }

            /* End of isAnggotaPa */

            $data = PpaProposal::where('id', $request->id);
            if(!in_array($role,['ketuayys','direktur','fam','faspv'])){
                $data = $data->when($isPa,function($q)use($request,$myBudgetings){
                    return $q->where(function($q)use($request,$myBudgetings){
                        $q->where([
                            'employee_id' => $request->user()->pegawai->id,
                            'unit_id' => $request->user()->pegawai->unit_id,
                            'position_id' => $request->user()->pegawai->position_id,
                        ])->orWhereIn('budgeting_id', $myBudgetings->pluck('id')->unique()->toArray());
                    });
                },function($q)use($request,$role,$isAnggotaPa,$anggarans){
                    return $q->when(in_array($role,['wakasek','keu']) && $isAnggotaPa,function($q)use($request,$anggarans){
                        return $q->where(function($q)use($request,$anggarans){
                            $q->where([
                                'employee_id' => $request->user()->pegawai->id,
                                'unit_id' => $request->user()->pegawai->unit_id,
                                'position_id' => $request->user()->pegawai->position_id,
                            ])->orWhereIn('budgeting_id', $anggarans->pluck('id')->toArray());
                        });
                    },function($q)use($request){
                        return $q->where([
                            'employee_id' => $request->user()->pegawai->id,
                            'unit_id' => $request->user()->pegawai->unit_id,
                            'position_id' => $request->user()->pegawai->position_id,
                        ]);
                    });
                });
            }
            $data = $data->first();
        }

        $active = $this->active;
        $route = $this->route;

        $isDynamic = true;
        $isWithTrashed = false;
        if(!$isDynamic) $isWithTrashed = true;

        if($data && ((!$isWithTrashed && $data->details()->count() > 0) || ($isWithTrashed && $data->details()->withTrashed()->count() > 0))){
            $editable = !$data->ppa && !$data->budgeting_id && $request->user()->pegawai->id == $data->employee_id ? true : false;

            if($isYear){
                $year = Date::now('Asia/Jakarta')->format('Y');
            }
            else{
                $year = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end','is_finance_year')->where('is_finance_year',1)->latest()->first();
            }

            $budgetings = JenisAnggaranAnggaran::select('id','budgeting_id')->whereHas('tahuns',function($q)use($yearAttr,$year){
                $q->where($yearAttr,($yearAttr == 'year' ? $year : $year->id));
            })->with('anggaran:id,name,acc_position_id')->get()->pluck('anggaran')->unique();

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
            ->setLastModifiedBy($request->user()->pegawai->name)
            ->setTitle("Data Proposal PPA MUDA".($data->unit_id ? " Unit ".$data->unit->name : '-').($data->dateId ? " Tanggal ".$data->dateId : null))
            ->setSubject($data->title)
            ->setDescription($data->desc ? $data->desc : $data->title)
            ->setKeywords("Proposal, PPA, MUDA");

            $pa = null;
            $jabatan = $data->anggaran ? $data->anggaran->accJabatan : null;
            if($jabatan){
                $pa = $jabatan->pegawaiUnit()->when($data->anggaran->unit_id != 5,function($q)use($data){
                    return $q->where('unit_Id',$data->anggaran->unit_id);
                })->whereHas('pegawai',function($q){
                    $q->aktif();
                })->first();
            }

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Nama Proposal')
            ->setCellValue('C1', ':')
            ->setCellValue('D1', $data->title ? $data->title : '-')
            ->setCellValue('A2', 'Tanggal')
            ->setCellValue('C2', ':')
            ->setCellValue('D2', $data->date ? $data->dateId : '-')
            ->setCellValue('A3', 'Unit')
            ->setCellValue('C3', ':')
            ->setCellValue('D3', $data->unit_id ? $data->unit->name : '-')
            ->setCellValue('A4', 'Pengguna Anggaran')
            ->setCellValue('C4', ':')
            ->setCellValue('D4', $data->anggaran ? ($pa ? $pa->pegawai->name : ($data->anggaran->accJabatan->name.' - '.$data->anggaran->name)) : '-')
            ->setCellValue('A5', 'PJ Proposal')
            ->setCellValue('C5', ':')
            ->setCellValue('D5', $data->employee_id ? $data->pegawai->name : '-')
            ->setCellValue('A6', 'Jabatan')
            ->setCellValue('C6', ':')
            ->setCellValue('D6', $data->position_id ? $data->jabatan->name : '-')
            ->setCellValue('A7', 'Tahap')
            ->setCellValue('C7', ':')
            ->setCellValue('D7', !$data->date ? 'Draft' : (!$data->ppa ? 'Diajukan ke PA' : 'Proses PPA'))            
            ->setCellValue('A9', 'No.')
            ->setCellValue('C9', 'Deskripsi')
            ->setCellValue('E9', 'Harga')
            ->setCellValue('F9', 'Kuantitas')
            ->setCellValue('G9', 'Subtotal');

            $i = 1;
            $kolom = $first_kolom = 10;

            $max_kolom = ($isWithTrashed ? $data->details()->withTrashed()->count() : $data->details()->count())+$kolom-1;
            $datas = $isWithTrashed ? $data->details()->withTrashed()->get() : $data->details()->get();

            foreach($datas as $d){
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom, $i++)
                ->setCellValue('B'.$kolom, $d->desc)
                ->setCellValue('G'.$kolom, '=E'.$kolom.'*F'.$kolom);
                if($isDynamic){
                    $spreadsheet->getActiveSheet()
                    ->setCellValue('E'.$kolom, $d->deleted_at ? '0' : $d->price)
                    ->setCellValue('F'.$kolom, $d->deleted_at ? '0' : $d->quantity);
                }
                else{
                    $spreadsheet->getActiveSheet()
                    ->setCellValue('E'.$kolom, $d->price_ori)
                    ->setCellValue('F'.$kolom, $d->quantity_ori);
                }
                $spreadsheet->getActiveSheet()->mergeCells('B'.$kolom.':D'.$kolom);

                $kolom++;
            }

            // Total Row
            $spreadsheet->getActiveSheet()
            ->setCellValue('A'.$kolom, 'Total')
            ->setCellValue('G'.$kolom, '=SUM(G'.$first_kolom.':G'.$max_kolom.')');
            $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':F'.$kolom);

            $kolom += 2;

            $spreadsheet->getActiveSheet()->setTitle('Proposal PPA');

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(9);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);

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

            $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray);

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
            $spreadsheet->getActiveSheet()->getStyle('A9:G9')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A10:A'.$max_kolom)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->getStyle('E10:G'.($max_kolom+1))->getNumberFormat()
            ->setFormatCode($FORMAT_CURRENCY_IDR);
            $spreadsheet->getActiveSheet()->getStyle('E10:G'.($max_kolom+1))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('B10:D'.($max_kolom))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

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
            $spreadsheet->getActiveSheet()->getStyle('A10:G'.($max_kolom+1))->applyFromArray($styleArray);

            $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

            $headers = [
                'Cache-Control' => 'max-age=0',
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="proposal_ppa'.($data->unit_id ? "_".strtolower($data->unit->name) : null)."_".($data->dateId ? $data->date : 'draft').'_'.$data->id.'.xlsx"',
            ];

            return response()->stream(function()use($writer){
                $writer->save('php://output');
            }, 200, $headers);
        }

        return redirect()->route($route.'.index');
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

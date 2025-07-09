<?php

namespace Modules\HR\Http\Controllers\EmployeeManagement\Evaluation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;

use App\Models\Psc\PscGradeSet;
use App\Models\Rekrutmen\EvaluasiPegawai;
use App\Models\Rekrutmen\PegawaiTetap;
use App\Models\Rekrutmen\Spk;
use App\Models\Rekrutmen\StatusPegawai;
use App\Models\Rekrutmen\StatusRekomendasi;
use App\Models\Phk\AlasanPhk;
use App\Models\Phk\Phk;
use App\Models\LoginUser;
use App\Models\StatusAcc;

class EmployeeEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        if($role == 'kepsek'){
            $eval = EvaluasiPegawai::whereHas('pegawai',function($query) use ($request){
                $query->whereHas('units',function($query) use ($request){
                    $query->where('unit_id',$request->user()->pegawai->unit_id);
                });
            })->orderBy('created_at','desc');
        }
        else{
            $eval = EvaluasiPegawai::orderBy('created_at','desc');
        }
        $status = 'aktif';

        if(isset($request->status) && $request->status != 'aktif'){
            $status = $request->status;
        }

        if($status == 'selesai'){
            $eval = $eval->whereNotNull(['education_acc_id','education_acc_status_id','education_acc_time'
            ])->where('education_acc_status_id',1)->get();
        }
        else{
            $eval = $eval->where(function($query){
                $query->where(function($query){
                    $query->whereNull([
                        'education_acc_id',
                        'education_acc_status_id',
                        'education_acc_time'
                    ]);
                })->orWhere(function($query){
                    $query->whereNotNull([
                        'education_acc_id',
                        'education_acc_status_id',
                        'education_acc_time'
                    ])->where('education_acc_status_id',2);
                });
            });

            if($request->user()->role->name == 'etm'){
                $eval = $eval->whereNotNull([
                    'supervision_result',
                    'interview_result'
                ])->get();
                $eval_manajemen = EvaluasiPegawai::whereHas('pegawai',function($query) use ($request){
                    $query->whereHas('units',function($query) use ($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                })->where(function($query){
                    $query->where(function($query){
                        $query->whereNull([
                            'supervision_result',
                            'interview_result',
                            'education_acc_id',
                            'education_acc_status_id',
                            'education_acc_time'
                        ]);
                    })->orWhere(function($query){
                        $query->whereNotNull([
                            'supervision_result',
                            'interview_result',
                            'education_acc_id',
                            'education_acc_status_id',
                            'education_acc_time'
                        ])->where('education_acc_status_id',2);
                    });
                })->get();

                $eval = $eval->concat($eval_manajemen)->unique('id')->sortByDesc('created_at')->all();
            }
            elseif($request->user()->role->name == 'etl'){
                $eval = $eval->whereNotNull([
                    'supervision_result',
                    'interview_result',
                    'recommend_status_id'
                ])->get();
            }
            else{
                $eval = $eval->get();
            }

        }

        if(in_array($role,['kepsek','etl','etm']))
            $folder = $role;
        else $folder = 'read-only';

        if($status == 'selesai')
            return view('kepegawaian.'.$folder.'.evaluasi_selesai', compact('eval','status'));
        else
            return view('kepegawaian.'.$folder.'.evaluasi_index', compact('eval','status'));
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
    public function edit(Request $request)
    {
        $eval = $request->id ? EvaluasiPegawai::find($request->id) : null;

        if($eval){
            if($request->user()->role->name == 'etm'){
                if(in_array($request->user()->pegawai->unit_id,$eval->pegawai->units()->pluck('unit_id')->toArray())){
                    $set = PscGradeSet::aktif()->first();
                    $psc = $set->grade;
                }
                $rekomendasi = StatusRekomendasi::all();
                $status = StatusPegawai::pegawaiAktif()->get();
                $alasan = AlasanPhk::all();
                
                if(in_array($request->user()->pegawai->unit_id,$eval->pegawai->units()->pluck('unit_id')->toArray())){
                    return view('kepegawaian.etm.evaluasi_ubah_manajemen', compact('eval','psc','rekomendasi','status','alasan'));
                }
                else
                    return view('kepegawaian.etm.evaluasi_ubah', compact('eval','rekomendasi','status','alasan'));
            }
            if($request->user()->role->name == 'etl'){
                $acc = StatusAcc::all();
                return view('kepegawaian.etl.evaluasi_ubah', compact('eval','acc'));
            }
            else{
                $set = PscGradeSet::aktif()->first();
                $psc = $set->grade;
                return view('kepegawaian.kepsek.evaluasi_ubah', compact('eval','psc'));
            }
        }
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
        $eval = $request->id ? EvaluasiPegawai::find($request->id) : null;

        if($eval){
            if($request->user()->role->name == 'etm'){
                $messages = [
                    'recommend_status.required' => 'Mohon pilih rekomendasi kelanjutan',
                    'employee_status.required_if' => 'Mohon pilih rekomendasi status',
                    'reason.required_if' => 'Mohon pilih salah satu alasan',
                ];

                $this->validate($request, [
                    'recommend_status' => 'required',
                    'employee_status' => 'required_if:recommend_status,1',
                    'reason' => 'required_if:recommend_status,2'
                ], $messages);

                if(in_array($request->user()->pegawai->unit_id,$eval->pegawai->units()->pluck('unit_id')->toArray())){
                    $eval->temp_psc_grade_id = $request->temp_psc;
                    $eval->supervision_result = $request->supervision;
                    $eval->interview_result = $request->interview;
                }
                if($request->recommend_status == 1){
                    $eval->recommend_status_id = $request->recommend_status;
                    if($request->employee_status) $eval->recommend_employee_status_id = $request->employee_status;
                    $eval->dismissal_reason_id = null;
                }
                else {
                    $eval->recommend_status_id = $request->recommend_status;
                    $eval->recommend_employee_status_id = null;
                    if($request->reason) $eval->dismissal_reason_id = $request->reason;
                }
            }
            elseif($request->user()->role->name == 'etl'){
                $messages = [
                    'acc_status.required' => 'Mohon tentukan persetujuan'
                ];

                $this->validate($request, [
                    'acc_status' => 'required'
                ], $messages);

                $eval->education_acc_id = $request->user()->pegawai->id;
                $eval->education_acc_status_id = $request->acc_status;
                $eval->education_acc_time = Date::now('Asia/Jakarta');

                $pegawai = $eval->pegawai;

                if($request->acc_status == 1){
                    if($eval->recommend_employee_status_id){
                        if($eval->recommend_employee_status_id == 1){
                            $pt = new PegawaiTetap();
                            $pt->promotion_date = Date::now('Asia/Jakarta');

                            $pegawai->tetap()->save($pt);

                            $pegawai->employee_status_id = $eval->recommend_employee_status_id;
                            $pegawai->save();
                        }
                        else{
                            $spk = Spk::where('employee_id',$pegawai->id)->update(['status_id' => 2]);

                            $spk = new Spk();
                            $spk->party_1_name = 'Dr. Kumiasih Mufidayati, M.Si.';
                            $spk->party_1_position = 'Direktur Sekolah Islam Terpadu Auliya';
                            $spk->party_1_address = 'Jalan Raya Jombang No. 1, Pondok Aren, Tangerang Selatan';
                            $spk->employee_name = $pegawai->name;
                            $spk->employee_address = $pegawai->address . ', RT ' . sprintf('%03d',$pegawai->rt) . ' RW ' . sprintf('%03d',$pegawai->rw) . ', ' . $pegawai->alamat->name.', '.$pegawai->alamat->kecamatanName().', '.$pegawai->alamat->kabupatenName().', '.$pegawai->alamat->provinsiName();
                            $spk->employee_status = $eval->rekomendasiStatus->status;
                            $spk->status_id = 1;

                            $pegawai->spk()->save($spk);

                            $pegawai->evaluasi()->save(new EvaluasiPegawai());

                            $pegawai->employee_status_id = $eval->recommend_employee_status_id;
                            $pegawai->save();
                        }
                    } 
                    elseif($eval->dismissal_reason_id){
                        if(!$pegawai->phk){
                            $spk = Spk::where('employee_id',$pegawai->id)->update(['status_id' => 2]);

                            $login = LoginUser::where('user_id',$pegawai->id)->pegawai()->first();
                            $login->delete();

                            $phk = new Phk();
                            $phk->employee_id = $pegawai->id;
                            $phk->dismissal_reason_id = $eval->dismissal_reason_id;
                            $phk->director_acc_id = $request->user()->pegawai->id;
                            $phk->director_acc_status_id = 1;
                            $phk->director_acc_time = Date::now('Asia/Jakarta');
                            $phk->save();

                            $pegawai->join_badge_status_id = 2;
                            $pegawai->disjoin_date = Date::now('Asia/Jakarta');
                            $pegawai->disjoin_badge_status_id = 1;
                            $pegawai->active_status_id = 2;
                            $pegawai->save();

                            Session::flash('success','Data evaluasi '. $pegawai->name .' berhasil ditambahkan');
                        }
                        else{
                            Session::flash('danger','Data evaluasi '. $pegawai->name .' sudah ada');
                            return redirect()->route('evaluasi.index');
                        }  
                    }
                }
            }

            else{
            /*
                $messages = [
                    'temp_psc.required' => 'Mohon pilih nilai PSC sementara',
                    'supervision.required' => 'Mohon tulis hasil supervisi',
                    'interview.required' => 'Mohon tulis hasil interview',
                ];

                $this->validate($request, [
                    'temp_psc' => 'required',
                    'supervision' => 'required',
                    'interview' => 'required'
                ], $messages);
            }*/
                $eval->temp_psc_grade_id = $request->temp_psc;
                $eval->supervision_result = $request->supervision;
                $eval->interview_result = $request->interview;  
            }
            $eval->save();

            Session::flash('success','Data evaluasi '. $eval->pegawai->name .' berhasil diatur');
        }

        return redirect()->route('evaluasi.index');
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

<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;

use App\Http\Services\BsiService;
use App\Http\Services\Psb\VirtualAccountGenerator;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsDeductionYear;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsYearTotal;
use App\Models\Pembayaran\VirtualAccountCalonSiswa;
use App\Models\Pembayaran\VirtualAccountSiswa;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\Siswa;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use stdClass;

class PenerimaanSiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // dd($request);
        // $request_2 = new stdClass();
        // $request_2->du_nominal = str_replace('.','',$request->du_nominal);
        // $request_2->bms_nominal = str_replace('.','',$request->bms_nominal);
        // $request_2->bms_deduction = str_replace('.','',$request->bms_deduction);
        // $request_2->bms_deduction = str_replace('.','',$request->bms_deduction);
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Update wawancara siswa gagal');
        // dd($calons);


        // $calons->status_id = 4;
        $calons->status_id = 3;
        $calons->save();

        $siswa = $calons;

        // Generator VA tanpa BMS
        // $va = VirtualAccountGenerator::VaGenerate($calons);

        $va = date('dmy', strtotime($calons->birth_date)).$calons->unit_id;
        $check_va = VirtualAccountSiswa::where('spp_va','like',$va.'%')->orderBy('spp_va','desc')->first();
        $check_va_calon = VirtualAccountCalonSiswa::where('spp_va','like',$va.'%')->orderBy('spp_va','desc')->first();
        if($check_va || $check_va_calon){
            if($check_va && $check_va_calon){
                if($check_va->spp_va > $check_va_calon->spp_va){
                    $nomor_urut = substr(substr($check_va->spp_va, -3, 2) + 101, -2);
                    $va = $va.$nomor_urut;
                    // dd('if if if');
                }else{
                    $nomor_urut = substr(substr($check_va_calon->spp_va, -3, 2) + 101, -2);
                    // dd($nomor_urut);
                    $va = $va.$nomor_urut;
                }
            }else if($check_va){
                $nomor_urut = substr(substr($check_va->spp_va, -3, 2) + 101, -2);
                $va = $va.$nomor_urut;
            }else{
                $nomor_urut = substr(substr($check_va_calon->spp_va, -3, 2) + 101, -2);
                $va = $va.$nomor_urut;
            }
        }else{
            $va = $va.'01';
        }
        $code = Wilayah::find($siswa->region_id);
        $city = Wilayah::where('code', substr($code->code,0,8))->first();
        
        /**
        $create_bms_inquiry = BsiService::registerInvoice(
            $siswa->student_name,
            $siswa->student_nickname."@sekolahauliya.sch.id",
            $city->name,
            $va.'1',
            "BMS",
            $calons->unit_id,
        );


        $create_spp_inquiry = BsiService::registerInvoice(
            $siswa->student_name,
            $siswa->student_nickname."@sekolahauliya.sch.id",
            $city->name,
            $va.'2',
            "SPP",
            $calons->unit_id,
        ); **/

        $va_siswa = VirtualAccountCalonSiswa::create([
            'unit_id' => $siswa->unit_id,
            'candidate_student_id' => $siswa->id,
            'spp_bank' => 'DIGIYOK',
            'spp_va' => $va.'2',
            'spp_trx_id' => $va.'21',
            'bms_bank' => 'DIGIYOK',
            'bms_va' => $va.'1',
            'bms_trx_id' => $va.'11',
        ]);
        // dd($request->bms_sisa_bms,$va_siswa);
        // $bms_nominalnya = $request->bms_sisa_bms + (int)str_replace('.','',$request->bms_daftar_ulang);
        // dd($bms_nominalnya);

        if($request->type_pembayaran == 1){
            $termin = 1; 
            $bms_nominalnya = $request->bms_sisa_bms[0] + (int)str_replace('.','',$request->bms_daftar_ulang);
        }else{
            if($request->unit_bms == 1){
                $termin = 2;
                $bms_nominalnya = $request->bms_sisa_bms[0] + (int)str_replace('.','',$request->bms_sisa_bms[1])+ (int)str_replace('.','',$request->bms_daftar_ulang);
            }else{
                $bms_nominalnya = $request->bms_sisa_bms[0] + (int)str_replace('.','',$request->bms_sisa_bms[1]) + (int)str_replace('.','',$request->bms_sisa_bms[2]) + (int)str_replace('.','',$request->bms_daftar_ulang);
                $termin = 3;
            }
        }

        $bms = BmsCalonSiswa::create([
            'unit_id' => $siswa->unit_id,
            'candidate_student_id' => $siswa->id,
            'register_nominal' => str_replace('.','',$request->bms_daftar_ulang),
            'register_remain' => str_replace('.','',$request->bms_daftar_ulang),
            'bms_nominal' => $bms_nominalnya,
            'bms_deduction' => str_replace('.','',$request->bms_potongan),
            'bms_remain' => $bms_nominalnya,
            'bms_type_id' => $request->type_pembayaran,
        ]);

        if($request->type_pembayaran == 1){
            $termin = 1; 
        }else{
            if($request->unit_bms == 1){
                $termin = 2;
            }else{
                $termin = 3;
            }
        }
        $index = 0;

        // $termin = $request->bms_termin;
        $year = $calons->tahunAjaran->academic_year_start;
        while($termin > $index){
            $tahun_ajaran = TahunAjaran::where('academic_year_start',$year)->first();
            if(!$tahun_ajaran){
                $tahun_ajaran = TahunAjaran::create([
                    'academic_year' => $year.'/'.($year+1),
                    'academic_year_start' => $year,
                    'academic_year_end' => $year+1,
                    'is_active' => 0,
                ]);

                //create semester ganjil
                Semester::create([
                    'semester_id' => $tahun_ajaran->academic_year.'-1',
                    'semester' => 'Ganjil',
                    'academic_year_id' => $tahun_ajaran->id,
                    'is_active' => 0,
                ]);
                
                //create semester genap
                Semester::create([
                    'semester_id' => $tahun_ajaran->academic_year.'-2',
                    'semester' => 'Genap',
                    'academic_year_id' => $tahun_ajaran->id,
                    'is_active' => 0,
                ]);
            }
            $bms_termin = BmsTermin::create([
                'bms_id' => $bms->id,
                'academic_year_id' => $tahun_ajaran->id,
                'is_student' => 0,
                'nominal' => str_replace('.','',$request->bms_sisa_bms[$index]),
                'remain' => str_replace('.','',$request->bms_sisa_bms[$index]),
            ]);
            
            $bms_plan = BmsPlan::where('academic_year_id',$tahun_ajaran->id)->where('unit_id',$calons->unit_id)->first();
            if($bms_plan){
                $bms_plan->total_plan = $bms_plan->total_plan + str_replace('.','',$request->bms_sisa_bms[$index]) + ($index==0?$bms->register_nominal:0);
                $bms_plan->remain = $bms_plan->total_plan - $bms_plan->total_get;
                $bms_plan->total_student += 1;
                $bms_plan->student_remain += 1;
                $bms_plan->percent = ($bms_plan->get / $bms_plan->total_plan)*100;
                $bms_plan->save();
            }else{
                $bms_plan = BmsPlan::create([
                    'unit_id' => $calons->unit_id,
                    'academic_year_id' => $tahun_ajaran->id,
                    'total_plan' => str_replace('.','',$request->bms_sisa_bms[$index]) + ($index==0?$bms->register_nominal:0),
                    'total_get' => 0,
                    'total_student' => 1,
                    'student_remain' => 1,
                    'remain' => str_replace('.','',$request->bms_sisa_bms[$index]) + ($index==0?$bms->register_nominal:0),
                    'percent' => 100,
                ]);
            }

            $bms_year_total = BmsYearTotal::where('academic_year_id',$tahun_ajaran->id)->where('unit_id',$calons->unit_id)->first();
            if($bms_year_total){
                $bms_year_total->nominal = $bms_year_total->nominal + str_replace('.','',$request->bms_sisa_bms[$index]);
                $bms_year_total->save();
            }else{
                $bms_year_total = BmsYearTotal::create([
                    'unit_id' => $calons->unit_id,
                    'academic_year_id' => $tahun_ajaran->id,
                    'nominal' => str_replace('.','',$request->bms_sisa_bms[$index]),
                ]);
            }

            if($index == 0){
                $bms_deduction_year = BmsDeductionYear::where('academic_year_id',$tahun_ajaran->id)->where('unit_id',$calons->unit_id)->first();
                if($bms_deduction_year){
                    $bms_deduction_year->nominal = $bms_deduction_year->nominal + str_replace('.','',$request->bms_potongan);
                    $bms_deduction_year->save();
                }else{
                    $bms_deduction_year = BmsDeductionYear::create([
                        'unit_id' => $calons->unit_id,
                        'academic_year_id' => $tahun_ajaran->id,
                        'nominal' => str_replace('.','',$request->bms_potongan),
                    ]);
                }
            }

            $index += 1;
            $year += 1;

        }

        RegisterCounterService::addCounter($calons->id,'interview');
        
        return redirect()->back()->with('success', 'Wawancara berhasil');
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
=======
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;

use App\Http\Services\BsiService;
use App\Http\Services\Psb\VirtualAccountGenerator;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsDeductionYear;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsYearTotal;
use App\Models\Pembayaran\VirtualAccountCalonSiswa;
use App\Models\Pembayaran\VirtualAccountSiswa;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\Siswa;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use stdClass;

class PenerimaanSiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // dd($request);
        // $request_2 = new stdClass();
        // $request_2->du_nominal = str_replace('.','',$request->du_nominal);
        // $request_2->bms_nominal = str_replace('.','',$request->bms_nominal);
        // $request_2->bms_deduction = str_replace('.','',$request->bms_deduction);
        // $request_2->bms_deduction = str_replace('.','',$request->bms_deduction);
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Update wawancara siswa gagal');
        // dd($calons);


        // $calons->status_id = 4;
        $calons->status_id = 3;
        $calons->save();

        $siswa = $calons;

        // Generator VA tanpa BMS
        // $va = VirtualAccountGenerator::VaGenerate($calons);

        $va = date('dmy', strtotime($calons->birth_date)).$calons->unit_id;
        $check_va = VirtualAccountSiswa::where('spp_va','like',$va.'%')->orderBy('spp_va','desc')->first();
        $check_va_calon = VirtualAccountCalonSiswa::where('spp_va','like',$va.'%')->orderBy('spp_va','desc')->first();
        if($check_va || $check_va_calon){
            if($check_va && $check_va_calon){
                if($check_va->spp_va > $check_va_calon->spp_va){
                    $nomor_urut = substr(substr($check_va->spp_va, -3, 2) + 101, -2);
                    $va = $va.$nomor_urut;
                    // dd('if if if');
                }else{
                    $nomor_urut = substr(substr($check_va_calon->spp_va, -3, 2) + 101, -2);
                    // dd($nomor_urut);
                    $va = $va.$nomor_urut;
                }
            }else if($check_va){
                $nomor_urut = substr(substr($check_va->spp_va, -3, 2) + 101, -2);
                $va = $va.$nomor_urut;
            }else{
                $nomor_urut = substr(substr($check_va_calon->spp_va, -3, 2) + 101, -2);
                $va = $va.$nomor_urut;
            }
        }else{
            $va = $va.'01';
        }
        $code = Wilayah::find($siswa->region_id);
        $city = Wilayah::where('code', substr($code->code,0,8))->first();
        
        /**
        $create_bms_inquiry = BsiService::registerInvoice(
            $siswa->student_name,
            $siswa->student_nickname."@sekolahauliya.sch.id",
            $city->name,
            $va.'1',
            "BMS",
            $calons->unit_id,
        );


        $create_spp_inquiry = BsiService::registerInvoice(
            $siswa->student_name,
            $siswa->student_nickname."@sekolahauliya.sch.id",
            $city->name,
            $va.'2',
            "SPP",
            $calons->unit_id,
        ); **/

        $va_siswa = VirtualAccountCalonSiswa::create([
            'unit_id' => $siswa->unit_id,
            'candidate_student_id' => $siswa->id,
            'spp_bank' => 'DIGIYOK',
            'spp_va' => $va.'2',
            'spp_trx_id' => $va.'21',
            'bms_bank' => 'DIGIYOK',
            'bms_va' => $va.'1',
            'bms_trx_id' => $va.'11',
        ]);
        // dd($request->bms_sisa_bms,$va_siswa);
        // $bms_nominalnya = $request->bms_sisa_bms + (int)str_replace('.','',$request->bms_daftar_ulang);
        // dd($bms_nominalnya);

        if($request->type_pembayaran == 1){
            $termin = 1; 
            $bms_nominalnya = $request->bms_sisa_bms[0] + (int)str_replace('.','',$request->bms_daftar_ulang);
        }else{
            if($request->unit_bms == 1){
                $termin = 2;
                $bms_nominalnya = $request->bms_sisa_bms[0] + (int)str_replace('.','',$request->bms_sisa_bms[1])+ (int)str_replace('.','',$request->bms_daftar_ulang);
            }else{
                $bms_nominalnya = $request->bms_sisa_bms[0] + (int)str_replace('.','',$request->bms_sisa_bms[1]) + (int)str_replace('.','',$request->bms_sisa_bms[2]) + (int)str_replace('.','',$request->bms_daftar_ulang);
                $termin = 3;
            }
        }

        $bms = BmsCalonSiswa::create([
            'unit_id' => $siswa->unit_id,
            'candidate_student_id' => $siswa->id,
            'register_nominal' => str_replace('.','',$request->bms_daftar_ulang),
            'register_remain' => str_replace('.','',$request->bms_daftar_ulang),
            'bms_nominal' => $bms_nominalnya,
            'bms_deduction' => str_replace('.','',$request->bms_potongan),
            'bms_remain' => $bms_nominalnya,
            'bms_type_id' => $request->type_pembayaran,
        ]);

        if($request->type_pembayaran == 1){
            $termin = 1; 
        }else{
            if($request->unit_bms == 1){
                $termin = 2;
            }else{
                $termin = 3;
            }
        }
        $index = 0;

        // $termin = $request->bms_termin;
        $year = $calons->tahunAjaran->academic_year_start;
        while($termin > $index){
            $tahun_ajaran = TahunAjaran::where('academic_year_start',$year)->first();
            if(!$tahun_ajaran){
                $tahun_ajaran = TahunAjaran::create([
                    'academic_year' => $year.'/'.($year+1),
                    'academic_year_start' => $year,
                    'academic_year_end' => $year+1,
                    'is_active' => 0,
                ]);

                //create semester ganjil
                Semester::create([
                    'semester_id' => $tahun_ajaran->academic_year.'-1',
                    'semester' => 'Ganjil',
                    'academic_year_id' => $tahun_ajaran->id,
                    'is_active' => 0,
                ]);
                
                //create semester genap
                Semester::create([
                    'semester_id' => $tahun_ajaran->academic_year.'-2',
                    'semester' => 'Genap',
                    'academic_year_id' => $tahun_ajaran->id,
                    'is_active' => 0,
                ]);
            }
            $bms_termin = BmsTermin::create([
                'bms_id' => $bms->id,
                'academic_year_id' => $tahun_ajaran->id,
                'is_student' => 0,
                'nominal' => str_replace('.','',$request->bms_sisa_bms[$index]),
                'remain' => str_replace('.','',$request->bms_sisa_bms[$index]),
            ]);
            
            $bms_plan = BmsPlan::where('academic_year_id',$tahun_ajaran->id)->where('unit_id',$calons->unit_id)->first();
            if($bms_plan){
                $bms_plan->total_plan = $bms_plan->total_plan + str_replace('.','',$request->bms_sisa_bms[$index]) + ($index==0?$bms->register_nominal:0);
                $bms_plan->remain = $bms_plan->total_plan - $bms_plan->total_get;
                $bms_plan->total_student += 1;
                $bms_plan->student_remain += 1;
                $bms_plan->percent = ($bms_plan->get / $bms_plan->total_plan)*100;
                $bms_plan->save();
            }else{
                $bms_plan = BmsPlan::create([
                    'unit_id' => $calons->unit_id,
                    'academic_year_id' => $tahun_ajaran->id,
                    'total_plan' => str_replace('.','',$request->bms_sisa_bms[$index]) + ($index==0?$bms->register_nominal:0),
                    'total_get' => 0,
                    'total_student' => 1,
                    'student_remain' => 1,
                    'remain' => str_replace('.','',$request->bms_sisa_bms[$index]) + ($index==0?$bms->register_nominal:0),
                    'percent' => 100,
                ]);
            }

            $bms_year_total = BmsYearTotal::where('academic_year_id',$tahun_ajaran->id)->where('unit_id',$calons->unit_id)->first();
            if($bms_year_total){
                $bms_year_total->nominal = $bms_year_total->nominal + str_replace('.','',$request->bms_sisa_bms[$index]);
                $bms_year_total->save();
            }else{
                $bms_year_total = BmsYearTotal::create([
                    'unit_id' => $calons->unit_id,
                    'academic_year_id' => $tahun_ajaran->id,
                    'nominal' => str_replace('.','',$request->bms_sisa_bms[$index]),
                ]);
            }

            if($index == 0){
                $bms_deduction_year = BmsDeductionYear::where('academic_year_id',$tahun_ajaran->id)->where('unit_id',$calons->unit_id)->first();
                if($bms_deduction_year){
                    $bms_deduction_year->nominal = $bms_deduction_year->nominal + str_replace('.','',$request->bms_potongan);
                    $bms_deduction_year->save();
                }else{
                    $bms_deduction_year = BmsDeductionYear::create([
                        'unit_id' => $calons->unit_id,
                        'academic_year_id' => $tahun_ajaran->id,
                        'nominal' => str_replace('.','',$request->bms_potongan),
                    ]);
                }
            }

            $index += 1;
            $year += 1;

        }

        RegisterCounterService::addCounter($calons->id,'interview');
        
        return redirect()->back()->with('success', 'Wawancara berhasil');
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

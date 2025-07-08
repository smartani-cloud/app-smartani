<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsTransaction;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\VirtualAccountCalonSiswa;
use App\Models\Pembayaran\VirtualAccountSiswa;
use App\Models\Psb\RegisterCounter;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\IdentitasSiswa;
use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;

class StatusPsbController extends Controller
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
        
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Penerimaan siswa gagal');
        if($calons->status_id != 5)return redirect()->back()->with('error', 'Penerimaan siswa gagal');
        
        
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        $tahun = substr($tahunsekarang->academic_year_start, -2).''.substr($tahunsekarang->academic_year_end, -2);
        
        $calons->level_id >= 10 ? $level = $calons->level_id : $level = '0'.$calons->level_id;
        
        $nipd = $calons->unit_id.''.$tahun.''.$level.''.$calons->gender_id;
        
        $check_nipd = Siswa::where('student_nis','like',$nipd.'%')->orderBy('student_nis','desc')->first();
        if($check_nipd){
            $nomor_urut = substr(substr($check_nipd->student_nis, -3) + 1001, -3);
            $nipd = $nipd.$nomor_urut;
        }else{
            $nipd = $nipd.'001';
        }
        
        $current_date = date('Y-m-d');

        $current_semester = Semester::where('is_active',1)->first();

        if($calons->student_id){
            $siswa = Siswa::create([
                'student_id' => $calons->student_id,
                'unit_id' => $calons->unit_id,
                'student_nis' => $nipd,
                'student_nisn' => $calons->student_nisn,
                'reg_number' => $calons->reg_number,
                
                'join_date' => $current_date,
                'semester_id' => $current_semester->id,
                'year_spp' => $calons->year_spp,
                'month_spp' => $calons->month_spp,
                'level_id' => $calons->level_id,
                'origin_school' => $calons->origin_school,
                'origin_school_address' => $calons->origin_school_address,
    
                'info_from' => $calons->info_from,
                'info_name' => $calons->info_name,
                'position' => $calons->position,
            ]);

        }else{

            $identitas_siswa = IdentitasSiswa::create([
                'unit_id' => $calons->unit_id,
                'student_nis' => $nipd,
                'student_nisn' => $calons->student_nisn,
                'nik' => $calons->nik,
                'student_name' => $calons->student_name,
                'student_nickname' => $calons->student_nickname,
                'reg_number' => $calons->reg_number,
    
                'birth_place' => $calons->birth_place,
                'birth_date' => $calons->birth_date,
                'gender_id' => $calons->gender_id,
                'religion_id' => $calons->religion_id,
                'child_of' => $calons->child_of,
                'family_status' => $calons->family_status,
                
                'join_date' => $current_date,
                'semester_id' => $current_semester->id,
                'year_spp' => $calons->year_spp,
                'month_spp' => $calons->month_spp,
                'level_id' => $calons->level_id,
                'address' => $calons->address,
                'address_number' => $calons->address_number,
                'rt' => $calons->rt,
                'rw' => $calons->rw,
                'region_id' => $calons->region_id,
    
                'origin_school' => $calons->origin_school,
                'origin_school_address' => $calons->origin_school_address,
    
                'info_from' => $calons->info_from,
                'info_name' => $calons->info_name,
                'position' => $calons->position,
    
                'parent_id' => $calons->parent_id,
            ]);
            
            $siswa = Siswa::create([
                'student_id' => $identitas_siswa->id,
                'unit_id' => $calons->unit_id,
                'student_nis' => $nipd,
                'student_nisn' => $calons->student_nisn,
                'reg_number' => $calons->reg_number,
                
                'join_date' => $current_date,
                'semester_id' => $current_semester->id,
                'year_spp' => $calons->year_spp,
                'month_spp' => $calons->month_spp,
                'level_id' => $calons->level_id,
                'origin_school' => $calons->origin_school,
                'origin_school_address' => $calons->origin_school_address,
    
                'info_from' => $calons->info_from,
                'info_name' => $calons->info_name,
                'position' => $calons->position,
            ]);
            
        }
        

        $va_calon = VirtualAccountCalonSiswa::where('candidate_student_id', $calons->id)->first();

        $va_student = VirtualAccountSiswa::create([
            'unit_id' => $va_calon->unit_id,
            'student_id' => $siswa->id,
            'spp_bank' => $va_calon->spp_bank,
            'spp_va' => $va_calon->spp_va,
            'spp_trx_id' => $va_calon->spp_trx_id,
            'bms_bank' => $va_calon->bms_bank,
            'bms_va' => $va_calon->bms_va,
            'bms_trx_id' => $va_calon->bms_trx_id,
        ]);

        $va_calon->delete();
        
        $bms_calon = BmsCalonSiswa::where('candidate_student_id', $calons->id)->first();

        $bms_student = BMS::create([
            'unit_id' => $bms_calon->unit_id,
            'student_id' => $siswa->id,
            'register_nominal' => $bms_calon->register_nominal,
            'register_paid' => $bms_calon->register_paid,
            'bms_nominal' => $bms_calon->bms_nominal,
            'bms_paid' => $bms_calon->bms_paid,
            'bms_deduction' => $bms_calon->bms_deduction,
            'bms_remain' => $bms_calon->bms_remain,
            'bms_type_id' => $bms_calon->bms_type_id,
        ]);

        $bms_termin = BmsTermin::where('bms_id',$bms_calon->id)->where('is_student',0)->get();
        foreach($bms_termin as $bms_term){
            $bms_term->is_student = 1;
            $bms_term->bms_id = $bms_student->id;
            $bms_term->save();
        }

        $bms_calon->delete();

        $bms_trx_calons = BmsTransactionCalonSiswa::where('candidate_student_id', $calons->id)->get();

        foreach($bms_trx_calons as $bms_trx_calon){
            $bms_trx = BmsTransaction::create([
                'unit_id' => $bms_trx_calon->unit_id,
                'student_id' => $siswa->id,
                'month' => $bms_trx_calon->month,
                'year' => $bms_trx_calon->year,
                'nominal' => $bms_trx_calon->nominal,
                'academic_year_id' => $bms_trx_calon->academic_year_id,
                'trx_id' => $bms_trx_calon->trx_id,
                'date' => $bms_trx_calon->date,
            ]);

            $bms_trx_calon->delete();
        }

        $spp = Spp::create([
            'unit_id' => $siswa->unit_id,
            'student_id' => $siswa->id,
        ]);
        
        RegisterCounterService::addCounter($calons->id,'stored');

        $calons->delete();

        return redirect()->back()->with('success', 'Calon siswa berhasil diresmikan');
        
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

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStartOfSpp(Request $request)
    {
        $calons = CalonSiswa::find($request->id);
        if(!$calons) return redirect()->back()->with('error', 'Ubah awal mula SPP calon siswa gagal');
        if($calons->status_id != 5) return redirect()->back()->with('error', 'Ubah awal mula SPP calon siswa gagal');
        $calons->year_spp = $request->year_spp;
        $calons->month_spp = $request->month_spp;
        $calons->save();

        return redirect()->back()->with('success', 'Awal mula SPP calon siswa berhasil diubah');
    }
}

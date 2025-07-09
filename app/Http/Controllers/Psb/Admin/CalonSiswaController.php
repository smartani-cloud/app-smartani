<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\CalonSiswa\ChangeYearPsb;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Agama;
use App\Models\JenisKelamin;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\Psb\RegisterCounter;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\OrangTua;
use App\Models\Siswa\StatusSiswa;
use App\Models\Unit;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class CalonSiswaController extends Controller
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
        $siswa = CalonSiswa::find($request->id);

        $old_ay_id = $siswa->academic_year_id;
        $new_ay_id = $request->tahun_akademik;
        ChangeYearPsb::change($siswa->id, $old_ay_id, $new_ay_id);

        $counter = RegisterCounter::where('academic_year_id',$old_ay_id)->where('unit_id',$siswa->unit_id)->where('student_status_id',$siswa->student_status_id)->first();
        $counterAlt = RegisterCounterService::checkCounter($new_ay_id,$siswa->unit_id,$siswa->student_status_id);

        // dd($counter, $counterAlt);
        $counterFrom = 'counter';
        $counterTo = 'counterAlt';

        $origin = $siswa->origin_school == 'SIT Auliya' ? 'intern' : 'extern';
                
        if($siswa->status_id >= 1){
            $status = 'register';
            $attrName = $status.'_'.$origin;
            $$counterFrom->{$attrName} -= 1;
            $$counterTo->{$attrName} += 1;
        }
        if($siswa->status_id >= 2){
            $status = 'saving_seat';
            $attrName = $status.'_'.$origin;
            $$counterFrom->{$attrName} -= 1;
            $$counterTo->{$attrName} += 1;
        }
        if($siswa->status_id >= 3){
            $status = 'interview';
            $attrName = $status.'_'.$origin;
            $$counterFrom->{$attrName} -= 1;
            $$counterTo->{$attrName} += 1;
        }
        if($siswa->status_id >= 4){
            if($siswa->status_id <= 7 && $siswa->status_id != 6){
                $status = 'accepted';
                $attrName = $status.'_'.$origin;
                $$counterFrom->{$attrName} -= 1;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id == 4){
                $status = 'before_reapply';
            }else if($siswa->status_id == 5){
                $status = 'reapply';
            }else if($siswa->status_id == 6){
                $status = 'reserved';
            }else if($siswa->status_id == 7){
                $status = 'canceled';
            }
            $attrName = $status.'_'.$origin;
            $$counterFrom->{$attrName} -= 1;
            $$counterTo->{$attrName} += 1;
        }

        $$counterFrom->save();
        $$counterTo->save();

        $siswa->academic_year_id = $request->tahun_akademik;
        $semester = Semester::where('academic_year_id',$request->tahun_akademik)->where('semester',$request->semester)->first();
        $siswa->semester_id = $semester->id;
        if($siswa->status_id == 5){
            $siswa->year_spp = $request->year_spp;
            $siswa->month_spp = $request->month_spp;
        }
        $siswa->save();
        
        return redirect()->back();
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
        $siswa = CalonSiswa::find($id);
        if($siswa->region_id == null){
            $provinsi = null;
            $kabupaten = null;
            $kecamatan = null;
            $desa = null;
        }else{
            // dd($siswa);
            $desadata = Wilayah::find($siswa->region_id);
    
            //pisah kode wilayah
            $prov_id = substr($desadata->code,0,2);
            $kab_id = substr($desadata->code,0,5);
            $kec_id = substr($desadata->code,0,8);
    
            //init data wilayah siswa
            $provinsidata = Wilayah::where('code',$prov_id)->first();
            $kabupatensdata = Wilayah::where('code',$kab_id)->first();
            $kecamatandata = Wilayah::where('code',$kec_id)->first();

            // masuk ke variable baru
            $provinsi = $provinsidata->name;
            $kabupaten = $kabupatensdata->name;
            $kecamatan = $kecamatandata->name;
            $desa = $desadata->name;
        }

        $pegawais = Pegawai::aktif()->get()->sortBy('name');

        $agamas = Agama::where('name','Islam')->get();
        $levels = Level::all();
        $units = Unit::all();
        $jeniskelamin = JenisKelamin::all();
        $tahunAjaran = TahunAjaran::orderBy('academic_year_start','desc')->get();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();
        $provinsis = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();
        return view('psb.admin.siswa.lihat',compact('siswa','provinsi','kabupaten','kecamatan','desa','agamas','levels','units','semesters','provinsis','tahunAjaran','pegawais'));
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
        $siswa = CalonSiswa::find($id);
        if($siswa->region_id == null){
            $provinsi = null;
            $kabupaten = null;
            $kecamatan = null;
            $desa = null;
        
            // dropdown list wilayah
            $listprovinsi = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();
            $listkabupaten = null;
            $listkecamatan = null;
            $listdesa = null;
        }else{
            // dd($siswa);
            $desadata = Wilayah::find($siswa->region_id);
    
            //pisah kode wilayah
            $prov_id = substr($desadata->code,0,2);
            $kab_id = substr($desadata->code,0,5);
            $kec_id = substr($desadata->code,0,8);
    
            //init data wilayah siswa
            $provinsidata = Wilayah::where('code',$prov_id)->first();
            $kabupatendata = Wilayah::where('code',$kab_id)->first();
            $kecamatandata = Wilayah::where('code',$kec_id)->first();

            // masuk ke variable baru
            $provinsi = $provinsidata->name;
            $kabupaten = $kabupatendata->name;
            $kecamatan = $kecamatandata->name;
            $desa = $desadata->name;
        
            // dropdown list wilayah
            $listprovinsi = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();
            $listkabupaten = Wilayah::where('code','LIKE',$prov_id.'%')->whereRaw('LENGTH(code) = 5')->orderBy('name', 'ASC')->get();
            $listkecamatan = Wilayah::where('code','LIKE',$kab_id.'%')->whereRaw('LENGTH(code) = 8')->orderBy('name', 'ASC')->get();
            $listdesa = Wilayah::where('code','LIKE',$kec_id.'%')->whereRaw('LENGTH(code) = 13')->orderBy('name', 'ASC')->get();
        }

        $agamas = Agama::where('name','Islam')->get();
        $studentStatusses = StatusSiswa::all();
        $levels = Level::where('unit_id',$siswa->unit->id)->get();
        $units = Unit::all();
        $jeniskelamin = JenisKelamin::all();
        $tahunAjaran = TahunAjaran::orderBy('academic_year_start','desc')->get();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();
        $provinsis = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();

        $pegawais = Pegawai::aktif()->get()->sortBy('name');

        return view('psb.admin.siswa.ubah',compact('siswa','provinsi','kabupaten','kecamatan','desa','agamas','studentStatusses','levels','units','semesters','provinsis','tahunAjaran','jeniskelamin','listprovinsi','listkabupaten','listkecamatan','listdesa','pegawais'));
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
        // dd($request);

        $request->validate([
            "siswa_baru" => "required",
            "nik" => "required",
            "nama" => "required",
            "tempat_lahir" => "required",
            "tanggal_lahir" => "required",
            "jenis_kelamin" => "required",
            "alamat" => "required",
            "rt" => "required",
            "rw" => "required",
            "provinsi" => "required",
            "kabupaten" => "required",
            "kecamatan" => "required",
            "desa" => "required",
            "kelas" => "required_if:siswa_baru,2",
            "asal_sekolah" => "required",
        ]);
        
        $agama = Agama::where('name','Islam')->first();

        $desa = Wilayah::where('code',$request->desa)->first();

        $siswa = CalonSiswa::find($id);

        if($request->siswa_baru == 1){
            $kelases = Level::where('unit_id',$siswa->unit_id)->first();
            $kelas = $kelases->id;
        }else{
            $kelas = $request->kelas;
        }

        $counter = RegisterCounter::where('unit_id',$siswa->unit_id)->where('academic_year_id',$siswa->academic_year_id)->where('student_status_id',$siswa->student_status_id)->first();

        $counterAlt = RegisterCounter::where('unit_id',$siswa->unit_id)->where('academic_year_id',$siswa->academic_year_id)->where('student_status_id',$request->siswa_baru)->first();

        if($siswa->origin_school != $request->asal_sekolah){
            if($siswa->student_status_id != $request->siswa_baru){
                $counterFrom = 'counter';
                $counterTo = 'counterAlt';
            }
            else{
                $counterFrom = $counterTo = 'counter';
            }

            $originFrom = $request->asal_sekolah == 'SIT Auliya' ? 'extern' : 'intern';
            $originTo = $request->asal_sekolah == 'SIT Auliya' ? 'intern' : 'extern';

            if($siswa->status_id >= 1){
                $status = 'register';
                $attrName = $status.'_'.$originFrom;
                $$counterFrom->{$attrName} -= 1;
                $attrName = $status.'_'.$originTo;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id >= 2){
                $status = 'saving_seat';
                $attrName = $status.'_'.$originFrom;
                $$counterFrom->{$attrName} -= 1;
                $attrName = $status.'_'.$originTo;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id >= 3){
                $status = 'interview';
                $attrName = $status.'_'.$originFrom;
                $$counterFrom->{$attrName} -= 1;
                $attrName = $status.'_'.$originTo;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id >= 4){
                if($siswa->status_id <= 7 && $siswa->status_id != 6){
                    $status = 'accepted';
                    $attrName = $status.'_'.$originFrom;
                    $$counterFrom->{$attrName} -= 1;
                    $attrName = $status.'_'.$originTo;
                    $$counterTo->{$attrName} += 1;
                }
                if($siswa->status_id == 4){
                    $status = 'before_reapply';
                }else if($siswa->status_id == 5){
                    $status = 'reapply';
                }else if($siswa->status_id == 6){
                    $status = 'reserved';
                }else if($siswa->status_id == 7){
                    $status = 'canceled';
                }
                $attrName = $status.'_'.$originFrom;
                $$counterFrom->{$attrName} -= 1;
                $attrName = $status.'_'.$originTo;
                $$counterTo->{$attrName} += 1;
            }

            $$counterFrom->save();
            $$counterTo->save();
        }
        elseif($siswa->student_status_id != $request->siswa_baru){    
            $counterFrom = 'counter';
            $counterTo = 'counterAlt';

            $origin = $siswa->origin_school == 'SIT Auliya' ? 'intern' : 'extern';

            if($siswa->status_id >= 1){
                $status = 'register';
                $attrName = $status.'_'.$origin;
                $$counterFrom->{$attrName} -= 1;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id >= 2){
                $status = 'saving_seat';
                $attrName = $status.'_'.$origin;
                $$counterFrom->{$attrName} -= 1;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id >= 3){
                $status = 'interview';
                $attrName = $status.'_'.$origin;
                $$counterFrom->{$attrName} -= 1;
                $$counterTo->{$attrName} += 1;
            }
            if($siswa->status_id >= 4){
                if($siswa->status_id <= 7 && $siswa->status_id != 6){
                    $status = 'accepted';
                    $attrName = $status.'_'.$origin;
                    $$counterFrom->{$attrName} -= 1;
                    $$counterTo->{$attrName} += 1;
                }
                if($siswa->status_id == 4){
                    $status = 'before_reapply';
                }else if($siswa->status_id == 5){
                    $status = 'reapply';
                }else if($siswa->status_id == 6){
                    $status = 'reserved';
                }else if($siswa->status_id == 7){
                    $status = 'canceled';
                }
                $attrName = $status.'_'.$origin;
                $$counterFrom->{$attrName} -= 1;
                $$counterTo->{$attrName} += 1;
            }

            $$counterFrom->save();
            $$counterTo->save();
        }

        $parentid = $siswa->parent_id;
        $siswa->student_nisn = $request->nisn;
        $siswa->student_name = $request->nama;
        $siswa->student_nickname = $request->nama_pendek;
        //$siswa->reg_number = $request->nomor_registrasi;
        $siswa->nik = $request->nik;
        $siswa->student_status_id = $request->siswa_baru;

        $siswa->birth_place = $request->tempat_lahir;
        $siswa->birth_date = $request->tanggal_lahir;
        $siswa->gender_id = $request->jenis_kelamin;
        $siswa->religion_id = $agama->id;
        $siswa->child_of = null;
        $siswa->family_status = null;
        
        //$siswa->join_date = $request->tanggal_masuk;
        $siswa->level_id = $kelas;
        $siswa->address = $request->alamat;
        $siswa->address_number = $request->no_rumah;
        $siswa->rt = $request->rt;
        $siswa->rw = $request->rw;
        $siswa->region_id = $desa->id;

        $siswa->origin_school = $request->asal_sekolah;
        $siswa->origin_school_address = $request->alamat_asal_sekolah;
        
        $siswa->sibling_name = $request->saudara_nama;
        $siswa->sibling_level_id = $request->saudara_kelas;

        $siswa->info_from = $request->info_dari;
        $siswa->info_name = $request->info_nama;
        $siswa->position = $request->posisi;
        $siswa->save();
        
        // dd($siswa);
        $ortu = OrangTua::find($parentid);
        $ortu->employee_id = $request->pegawai;
        $ortu->father_name = $request->nama_ayah;
        //$ortu->father_nik = $request->nik_ayah;
        $ortu->father_phone = $request->hp_ayah;
        //$ortu->father_email = $request->email_ayah;
        //$ortu->father_job = $request->pekerjaan_ayah;
        //$ortu->father_position= $request->jabatan_ayah; //jabatan ayah
        //$ortu->father_phone_office = $request->telp_kantor_ayah;
        //$ortu->father_job_address= $request->alamat_kantor_ayah; //alamat kantor ayah
        //$ortu->father_salary= $request->gaji_ayah; //gaji ayah

        //$ortu->mother_nik = $request->nik_ibu;
        $ortu->mother_name = $request->nama_ibu;
        $ortu->mother_phone = $request->hp_ibu;
        //$ortu->mother_email = $request->email_ibu;
        //$ortu->mother_job = $request->pekerjaan_ibu;
        //$ortu->mother_position= $request->jabatan_ibu; //jabatan ibu
        //$ortu->mother_phone_office = $request->telp_kantor_ibu;
        //$ortu->mother_job_address= $request->alamat_kantor_ibu; //alamat kantor ibu
        //$ortu->mother_salary= $request->gaji_ibu; //gaji ibu

        //$ortu->parent_address = $request->alamat_ortu;
        $ortu->parent_phone_number = $request->no_hp_ortu;

        $ortu->guardian_name = $request->nama_wali;
        //$ortu->guardian_nik = $request->nik_wali;
        $ortu->guardian_phone_number = $request->no_hp_wali;
        //$ortu->guardian_email = $request->email_wali;
        //$ortu->guardian_job = $request->pekerjaan_wali;
        //$ortu->guardian_position= $request->jabatan_wali; //jabatan wali
        //$ortu->guardian_phone_office = $request->telp_kantor_wali;
        //$ortu->guardian_job_address = $request->alamat_kantor_wali; //alamat kantor wali
        //$ortu->guardian_salary = $request->gaji_wali; //gaji wali
        //$ortu->guardian_address = $request->alamat_wali;
        $ortu->save();
        // dd('success');

        return redirect()->route('kependidikan.psb.calonsiswa.lihat',$id)->with('success','Ubah data calon siswa berhasil');
        // return redirect('/kependidikan/kbm/siswa')->with('sukses','Ubah Siswa Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $siswa = CalonSiswa::find($request->id);

        $counter = RegisterCounter::where('unit_id',$siswa->unit_id)->where('academic_year_id',$siswa->academic_year_id)->first();

        if($siswa->origin_school != 'SIT Auliya'){

            if($siswa->status_id == 1){
                $counter->register_extern -= 1;
            }else if($siswa->status_id == 2){
                $counter->register_extern -= 1;
                $counter->saving_seat_extern -= 1;
            }else if($siswa->status_id == 3){
                $counter->register_extern -= 1;
                $counter->saving_seat_extern -= 1;
                $counter->interview_extern -= 1;
            }else if($siswa->status_id == 4){
                $counter->register_extern -= 1;
                $counter->saving_seat_extern -= 1;
                $counter->interview_extern -= 1;
                $counter->accepted_extern -= 1;
            }else if($siswa->status_id == 4){
                $counter->register_extern -= 1;
                $counter->saving_seat_extern -= 1;
                $counter->interview_extern -= 1;
                $counter->accepted_extern -= 1;
            }else if($siswa->status_id == 5){
                $counter->register_extern -= 1;
                $counter->saving_seat_extern -= 1;
                $counter->interview_extern -= 1;
                $counter->accepted_extern -= 1;
                $counter->reapply_extern -= 1;
            }else if($siswa->status_id == 6){
                $counter->register_extern -= 1;
                $counter->saving_seat_extern -= 1;
                $counter->interview_extern -= 1;
                $counter->reserved_extern -= 1;
            }

        }else{

            if($siswa->status_id == 1){
                $counter->register_intern -= 1;
            }else if($siswa->status_id == 2){
                $counter->register_intern -= 1;
                $counter->saving_seat_intern -= 1;
            }else if($siswa->status_id == 3){
                $counter->register_intern -= 1;
                $counter->saving_seat_intern -= 1;
                $counter->interview_intern -= 1;
            }else if($siswa->status_id == 4){
                $counter->register_intern -= 1;
                $counter->saving_seat_intern -= 1;
                $counter->interview_intern -= 1;
                $counter->accepted_intern -= 1;
            }else if($siswa->status_id == 4){
                $counter->register_intern -= 1;
                $counter->saving_seat_intern -= 1;
                $counter->interview_intern -= 1;
                $counter->accepted_intern -= 1;
            }else if($siswa->status_id == 5){
                $counter->register_intern -= 1;
                $counter->saving_seat_intern -= 1;
                $counter->interview_intern -= 1;
                $counter->accepted_intern -= 1;
                $counter->reapply_intern -= 1;
            }else if($siswa->status_id == 6){
                $counter->register_intern -= 1;
                $counter->saving_seat_intern -= 1;
                $counter->interview_intern -= 1;
                $counter->reserved_intern -= 1;
            }

        }
        $counter->save();

        $siswa->delete();

        return redirect()->back()->with('success','Hapus calon siswa berhasil');

    }

    public function allPegawai()
    {
        $pegawais = Pegawai::aktif()->get()->sortBy('name')->pluck("name","id");
        return response()->json($pegawais);
    }
}

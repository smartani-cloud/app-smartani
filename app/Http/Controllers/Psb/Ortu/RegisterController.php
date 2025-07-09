<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psb\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Services\Psb\CodeGeneratorPsb;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Agama;
use App\Models\JenisKelamin;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\LoginUser;
use App\Models\Psb\RegisterCounter;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\IdentitasSiswa;
use App\Models\Unit;
use App\Models\Wilayah;

use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Siswa;

use DB;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('psb.unit');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $lock = DB::table('tm_settings')->where('name','psb_lock_status')->first();
        $units = Unit::select('name','whatsapp_unit')->sekolah()->get();
        return view('psb.pendaftaran',compact('lock','units'));
    }

    public function createSiswa(Request $request)
    {

        if( !auth()->user()->orangtua->father_phone
            || !auth()->user()->orangtua->mother_phone
            || !auth()->user()->orangtua->father_email
            || !auth()->user()->orangtua->mother_email
            ){
            return redirect()->route('psb.profil.edit')->with('success','Lengkapi profil terlebih dahulu sebelum melakukan pendaftaran baru');
        }

        $units = Unit::sekolah()->get();

        if($request->unit == 'TK'){
            $unit_id = 1;
            $unit_name = 'TK';
        }else if($request->unit == 'SD'){
            $unit_name = 'SD';
            $unit_id = 2;
        }else if($request->unit == 'SMP'){
            $unit_name = 'SMP';
            $unit_id = 3;
        }else if($request->unit == 'SMA'){
            $unit_name = 'SMA';
            $unit_id = 4;
        }else{
            return view('psb.ortu.unit',compact('units'));
        }

        $unit = Unit::select('id','name','psb_active','new_admission_active','transfer_admission_active')->where('name',$request->unit)->sekolah()->first();
        if(!$unit) return view('psb.ortu.unit',compact('units'));

        $agamas = Agama::where('name','Islam')->get();
        $kelases = Level::all();
        $levels = Level::where('unit_id',$unit_id)->get();
        $jeniskelamin = JenisKelamin::all();
        $listprovinsi = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();
        // $tahun_active = TahunAjaran::where('is_active',1)->first();
        // $tahunAjaran = TahunAjaran::orderBy('academic_year_start','desc')->where('academic_year_start','>=',$tahun_active->academic_year_start)->get();
        $semester_active = Semester::where('is_active',1)->first();
        $tahunAjaran = Semester::where('academic_year_id','>=',$semester_active->academic_year_id)->skip(0)->take(4)->get();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();

        return view('psb.ortu.register',compact(
            'agamas',
            'levels',
            'units',
            'unit',
            'jeniskelamin',
            'tahunAjaran',
            'semesters',
            'listprovinsi',
            'unit_name',
            'kelases',
            'unit_id',
        ));
    }

    public function storeSiswa(Request $request)
    {
        $request->validate([
            "siswa_baru" => "required",
            "existing" => "required",
        ]);

        $unit_id = $request->unit_id;
        $unit = Unit::find($unit_id);

        if($unit->psb_active == 1){
            if(($unit->new_admission_active == 1 && $unit->transfer_admission_active != 1 && $request->siswa_baru != 1) || ($unit->new_admission_active != 1 && $unit->transfer_admission_active == 1 && $request->siswa_baru != 2)){
                 Session::flash('danger','Pendaftaran calon siswa gagal. Saat ini pendaftaran masih ditutup.');
                 //return redirect()->route('psb.siswa.create',['unit' => $unit->name]);
                 return redirect()->route('psb.index');
            }
            if($request->siswa_baru == 1){
                $kelases = Level::where('unit_id',$unit_id)->first();
                $kelas = $kelases->id;
            }else{
                $kelas = $request->kelas;
            }
            $semester = Semester::find($request->tahun_ajaran);
            $registerCounter = $semester->tahunAjaran->psbRegisterCounter()->select('register_intern','register_extern')->where('unit_id',$unit_id)->where('student_status_id',$request->siswa_baru)->first();
            
            // bikin number register
            $register_number = CodeGeneratorPsb::RegisterNumber($unit_id,$semester->academic_year_id);

            // Sudah pernah di Auliya
            if($request->existing == "2"){
                $siswa = IdentitasSiswa::find($request->siswa_id);

                $isCandidateExist = auth()->user()->orangtua->calonSiswa()->count() > 0 && in_array($siswa->id,auth()->user()->orangtua->calonSiswa()->select('student_id')->get()->pluck('student_id')->toArray()) ? true : false;

                if(!$siswa || $siswa->parent_id != auth()->user()->user_id || $isCandidateExist){
                    return redirect()->route('psb.index')->with('danger','Pendaftaran calon siswa gagal. Mohon periksa kembali data putra/i Anda.');
                }

                $calon = CalonSiswa::create([
                    'unit_id' => $unit_id,
                    'student_id' => $siswa->id,
                    'student_nis' => $siswa->student_nis,
                    'student_nisn' => $siswa->student_nisn,
                    'student_name' => $siswa->student_name,
                    'student_nickname' => $siswa->student_nickname,
                    'nik' => $siswa->nik,
                    'student_status_id' => $request->siswa_baru,
                    'reg_number' => $register_number,
                    // 'reg_number' => $unit->name.$semester->tahunAjaran->academic_year_start.sprintf('%04d',($registerCounter ? ($registerCounter->register_intern+$registerCounter->register_extern)+1 : '1')),
                    'academic_year_id' => $semester->academic_year_id,
        
                    'birth_place' => $siswa->birth_place,
                    'birth_date' => $siswa->birth_date,
                    'gender_id' => $siswa->gender_id,
                    'religion_id' => $siswa->religion_id,
                    'child_of' => null,
                    'family_status' => null,
                    
                    'join_date' => null,
                    'semester_id' => $request->tahun_ajaran,
                    'level_id' => $kelas,
                    'address' => $siswa->address,
                    'address_number' => $siswa->address_number,
                    'rt' => $siswa->rt,
                    'rw' => $siswa->rw,
                    'region_id' => $siswa->region_id,
        
                    'origin_school' => $request->asal_sekolah,
                    'origin_school_address' => '',
                    
                    'sibling_name' => $siswa->sibling_name,
                    'sibling_level_id' => $siswa->sibling_level_id,
        
                    'info_from' => $siswa->info_from,
                    'info_name' => $siswa->info_name,
                    'position' => $siswa->position,
                    'status_id' => 1,
        
                    'parent_id' => auth()->user()->user_id,
                ]);
            }else{
                $agama = Agama::where('name','Islam')->first();
                $desa = Wilayah::where('code',$request->desa)->first();
                $level = Level::find($request->kelas);
                $request->validate([
                    "nik" => "required",
                    "nama" => "required",
                    "tempat_lahir" => "required",
                    "tanggal_lahir" => "required",
                    "jenis_kelamin" => "required",
                    "agama" => "required",
                    "alamat" => "required",
                    "rt" => "required",
                    "rw" => "required",
                    "desa" => "required",
                    "kelas" => "required",
                ]);
                $calon = CalonSiswa::create([
                    'unit_id' => $unit_id,
                    'student_nis' => $request->nis,
                    'student_nisn' => $request->nisn,
                    'student_name' => $request->nama,
                    'student_nickname' => $request->nama_pendek,
                    'nik' => $request->nik,
                    'student_status_id' => $request->siswa_baru,
                    'reg_number' => $register_number,
                    // 'reg_number' => $unit->name.$semester->tahunAjaran->academic_year_start.sprintf('%04d',($registerCounter ? ($registerCounter->register_intern+$registerCounter->register_extern)+1 : '1')),
                    'academic_year_id' => $semester->academic_year_id,
        
                    'birth_place' => $request->tempat_lahir,
                    'birth_date' => $request->tanggal_lahir,
                    'gender_id' => $request->jenis_kelamin,
                    'religion_id' => $agama->id,
                    'child_of' => null,
                    'family_status' => null,
                    
                    'join_date' => null,
                    'semester_id' => $request->tahun_ajaran,
                    'level_id' => $kelas,
                    'address' => $request->alamat,
                    'address_number' => $request->no_rumah,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'region_id' => $desa->id,
        
                    'origin_school' => $request->asal_sekolah,
                    'origin_school_address' => $request->alamat_asal_sekolah,
                    
                    'sibling_name' => $request->saudara_nama,
                    'sibling_level_id' => $request->saudara_kelas,
        
                    'info_from' => $request->info_dari,
                    'info_name' => $request->info_nama,
                    'position' => $request->posisi,
                    'status_id' => 1,
        
                    'parent_id' => auth()->user()->user_id,
                ]);
            }

            RegisterCounterService::addCounter($calon->id,'register');

            Session::flash('success','Pendaftaran calon siswa berhasil. Selanjutnya, silakan klik fitur nama calon siswa.');
        }
        else Session::flash('danger','Pendaftaran calon siswa gagal. Saat ini pendaftaran masih ditutup.');

        return redirect()->route('psb.index');
    }

    public function storeOrtu(Request $request)
    {

        // $rules = array(
        //     'username' => 'required|unique:login_user|min:6|max:255',
        //     'password' => 'required|min:8',
        // );

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails())
        // {
        //     return redirect()->back()->withInput()->withErrors($validator)->with('danger', 'Username telah digunakan');
        // }

        // dd($request);

        if($request->siswa == 0){
            $messages = [
                'father_name.required' => 'Mohon masukkan nama ayah',
                'father_phone.required' => 'Mohon masukkan nomor telepon ayah yang valid',
                'father_phone.max' => 'Mohon periksa kembali nomor telepon ayah',
                'father_email.required' => 'Mohon masukkan alamat email ayah',
                'father_email.email' => 'Mohon periksa kembali alamat email ayah',
                'mother_name.required' => 'Mohon masukkan nama ibu',
                'mother_phone.required' => 'Mohon masukkan nomor telepon ibu yang valid',
                'mother_phone.max' => 'Mohon periksa kembali nomor telepon ibu',
                'mother_email.required' => 'Mohon masukkan alamat email ibu',
                'mother_email.email' => 'Mohon periksa kembali alamat email ibu',
            ];

            $this->validate($request, [
                'father_name' => 'required',
                'father_phone' => 'required|max:15',
                'father_email' => 'required|email',
                'mother_name' => 'required',
                'mother_phone' => 'required|max:15',
                'mother_email' => 'required|email',
            ], $messages);
          
            if(OrangTua::where('father_email',$request->father_email)->count() < 1){
                $ortu = OrangTua::create([
                    'father_name' => $request->father_name,
                    'father_phone' => $request->father_phone,
                    'father_email' => $request->father_email,
                    'mother_name' => $request->mother_name,
                    'mother_phone' => $request->mother_phone,
                    'mother_email' => $request->mother_email,
                ]);
                $ortu_id = $ortu->id;
    
                $user = LoginUser::create([
                    'username' => $request->father_email,
                    'password' => bcrypt($request->father_phone),
                    'active_status_id' => 1,
                    'role_id' => 36,
                    'user_id' => $ortu_id,
                ]);
            }
            elseif(isset($request->father_email) && (OrangTua::where('father_email',$request->father_email)->count() > 0)){
                return redirect()->back()->with('exist', 'Mohon maaf, email sudah terdaftar');
            }
            else{
                return redirect()->back()->with('danger', 'Maaf, harap isikan semua data dengan lengkap');
            }
        }else{
            $siswa = Siswa::where('student_nis',$request->nipd)->first();
            if(!$siswa) return redirect()->back()->with('danger', 'Data Siswa tidak ditemukan');
            // dd($siswa);
            
            $ortu = $siswa->identitas->orangtua;
            $ortu_id = $ortu->id;
            
            $check_account = LoginUser::where('user_id',$ortu_id)->where('role_id',36)->first();
            if($check_account) return redirect()->back()->with('danger', 'Akun Orang Tua Telah Terdaftar');

            if(!$ortu->father_email || !$ortu->father_phone) return redirect()->back()->with('danger', 'Data orang tua terdaftar belum lengkap, mohon hubungi staf administrasi Auliya untuk informasi lebih lanjut');
            
            $user = LoginUser::create([
                'username' => $ortu->father_email,
                'password' => bcrypt($ortu->father_phone),
                'active_status_id' => 1,
                'role_id' => 36,
                'user_id' => $ortu_id,
            ]);
        }

        $credentials = [
            'username' => $ortu->father_email,
            'password' => $ortu->father_phone
        ];
        
        if (Auth::attempt($credentials)) {
            return redirect('/psb/index');
        }else{
            return redirect()->back()->with('danger', 'Username atau password salah');
        }
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
        
        $parent_id = false;

        if( $request->nip ){
            $check_parent = OrangTua::where('employee_id',$request->nip)->first();
            if($check_parent){
                $parent_id = $check_parent->id;
            }
        }else if( $request->father_ktp ){
            $check_parent = OrangTua::where('father_nik',$request->father_ktp)->first();
            if($check_parent){
                $parent_id = $check_parent->id;
            }
        }else if( $request->mother_ktp ){
            $check_parent = OrangTua::where('mother_nik',$request->mother_ktp)->first();
            if($check_parent){
                $parent_id = $check_parent->id;
            }
        }

        if($parent_id){
            // dd($parent_id);
        }else{
            $orangTua = OrangTua::create([
                'employee_id' => $request->nip,
                'father_name' => $request->father_name,
                'father_nik' => $request->father_ktp,
                'father_phone' => $request->father_phone,
                'father_email' => $request->father_email,
                'father_job' => $request->father_job,
                'father_position'=> $request->father_position, //jabatan ayah
                'father_phone_office' => $request->father_job_phone,
                'father_job_address'=> $request->father_job_address, //alamat kantor ayah
                'father_salary'=> $request->father_salary, //gaji ayah
    
                'mother_name' => $request->mother_name,
                'mother_nik' => $request->mother_ktp,
                'mother_phone' => $request->mother_phone,
                'mother_email' => $request->mother_email,
                'mother_job' => $request->mother_job,
                'mother_position'=> $request->mother_position, //jabatan ayah
                'mother_phone_office' => $request->mother_job_phone,
                'mother_job_address'=> $request->mother_job_address, //alamat kantor ayah
                'mother_salary'=> $request->mother_salary, //gaji ayah
    
                'parent_address' => $request->alamat_ortu,
                'parent_phone_number' => $request->no_hp_ortu,
    
                'guardian_name' => $request->nama_wali,
                'guardian_nik' => $request->nik_wali,
                'guardian_phone_number' => $request->no_hp_wali,
                'guardian_email' => $request->email_wali,
                'guardian_job' => $request->pekerjaan_wali,
                'guardian_position'=> $request->jabatan_wali, //jabatan wali
                'guardian_phone_office' => $request->telp_kantor_wali,
                'guardian_job_address'=> $request->alamat_kantor_wali, //alamat kantor wali
                'guardian_salary'=> $request->gaji_wali, //gaji wali
                'guardian_address' => $request->alamat_wali,
            ]);

            $parent_id = $orangTua->id;
        }


        $desa = Wilayah::where('code',$request->desa)->first();

        $siswa = CalonSiswa::create([
            'unit_id' => $request->unit_id,
            // 'student_nis' => $request->nis, // generate jika diterima
            'student_nis' => $request->nis, // generate jika diterima
            'student_nisn' => $request->nisn,   // harusnya diisi
            'student_name' => $request->name,
            'student_nickname' => $request->nickname,
            'academic_year_id' => $request->academic_year_id,
            // 'reg_number' => $request->nomor_registrasi, // gimana yaa
            'reg_number' => 0, // gimana yaa

            'birth_place' => $request->born_place,
            'birth_date' => $request->born_date,
            'gender_id' => $request->gender_id,
            // 'religion_id' => $request->religion_id,
            'religion_id' => 1,
            'child_of' => 1,  //$request->anak_ke,    // diisi
            'family_status' => 'Anak Kandung', //$request->status_anak, // diisi
            
            'join_date' => $request->join_date, // saat diterima
            'level_id' => $request->level_id, // kelas
            'address' => $request->address,
            'address_number' => $request->address_number,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'region_id' => $desa->id,

            'origin_school' => $request->asal_sekolah,  
            'origin_school_address' => $request->alamat_asal_sekolah, 
            
            'sibling_name' => $request->saudara_nama,   // diisi
            'sibling_level_id' => $request->saudara_kelas, // diisi

            'info_from' => $request->info_dari, // diisi
            'info_name' => $request->info_nama, // diisi
            'position' => $request->posisi, // diisi

            'parent_id' => $parent_id,

            'status_id' => 1,
        ]);
        
        $user = LoginUser::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'active_status_id' => 1,
            'role_id' => 36,
            'user_id' => $parent_id,
        ]);
        // return $user;

        $counter = RegisterCounter::where('unit_id',$siswa->unit_id)->where('academic_year_id',$siswa->academic_year_id)->first();

        if($counter){
            if($siswa->asal_sekolah == 'SIT Auliya'){
                $counter->register_intern = $counter->register_intern + 1;
            }else{
                $counter->register_extern = $counter->register_extern + 1;
            }
        }else{
            if($siswa->asal_sekolah == 'SIT Auliya'){
                $counter = RegisterCounter::create([
                    'academic_year_id' => $siswa->academic_year_id,
                    'unit_id' => $siswa->unit_id,
                    'register_intern' => 1,
                ]);
            }else{
                $counter = RegisterCounter::create([
                    'academic_year_id' => $siswa->academic_year_id,
                    'unit_id' => $siswa->unit_id,
                    'register_extern' => 1,
                ]);
            }

        }

        return redirect('/psb')->with('success','Pendaftaran Calon Siswa Baru Berhasil');
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
=======
<?php

namespace App\Http\Controllers\Psb\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Services\Psb\CodeGeneratorPsb;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Agama;
use App\Models\JenisKelamin;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\LoginUser;
use App\Models\Psb\RegisterCounter;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\IdentitasSiswa;
use App\Models\Unit;
use App\Models\Wilayah;

use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Siswa;

use DB;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('psb.unit');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $lock = DB::table('tm_settings')->where('name','psb_lock_status')->first();
        $units = Unit::select('name','whatsapp_unit')->sekolah()->get();
        return view('psb.pendaftaran',compact('lock','units'));
    }

    public function createSiswa(Request $request)
    {

        if( !auth()->user()->orangtua->father_phone
            || !auth()->user()->orangtua->mother_phone
            || !auth()->user()->orangtua->father_email
            || !auth()->user()->orangtua->mother_email
            ){
            return redirect()->route('psb.profil.edit')->with('success','Lengkapi profil terlebih dahulu sebelum melakukan pendaftaran baru');
        }

        $units = Unit::sekolah()->get();

        if($request->unit == 'TK'){
            $unit_id = 1;
            $unit_name = 'TK';
        }else if($request->unit == 'SD'){
            $unit_name = 'SD';
            $unit_id = 2;
        }else if($request->unit == 'SMP'){
            $unit_name = 'SMP';
            $unit_id = 3;
        }else if($request->unit == 'SMA'){
            $unit_name = 'SMA';
            $unit_id = 4;
        }else{
            return view('psb.ortu.unit',compact('units'));
        }

        $unit = Unit::select('id','name','psb_active','new_admission_active','transfer_admission_active')->where('name',$request->unit)->sekolah()->first();
        if(!$unit) return view('psb.ortu.unit',compact('units'));

        $agamas = Agama::where('name','Islam')->get();
        $kelases = Level::all();
        $levels = Level::where('unit_id',$unit_id)->get();
        $jeniskelamin = JenisKelamin::all();
        $listprovinsi = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();
        // $tahun_active = TahunAjaran::where('is_active',1)->first();
        // $tahunAjaran = TahunAjaran::orderBy('academic_year_start','desc')->where('academic_year_start','>=',$tahun_active->academic_year_start)->get();
        $semester_active = Semester::where('is_active',1)->first();
        $tahunAjaran = Semester::where('academic_year_id','>=',$semester_active->academic_year_id)->skip(0)->take(4)->get();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();

        return view('psb.ortu.register',compact(
            'agamas',
            'levels',
            'units',
            'unit',
            'jeniskelamin',
            'tahunAjaran',
            'semesters',
            'listprovinsi',
            'unit_name',
            'kelases',
            'unit_id',
        ));
    }

    public function storeSiswa(Request $request)
    {
        $request->validate([
            "siswa_baru" => "required",
            "existing" => "required",
        ]);

        $unit_id = $request->unit_id;
        $unit = Unit::find($unit_id);

        if($unit->psb_active == 1){
            if(($unit->new_admission_active == 1 && $unit->transfer_admission_active != 1 && $request->siswa_baru != 1) || ($unit->new_admission_active != 1 && $unit->transfer_admission_active == 1 && $request->siswa_baru != 2)){
                 Session::flash('danger','Pendaftaran calon siswa gagal. Saat ini pendaftaran masih ditutup.');
                 //return redirect()->route('psb.siswa.create',['unit' => $unit->name]);
                 return redirect()->route('psb.index');
            }
            if($request->siswa_baru == 1){
                $kelases = Level::where('unit_id',$unit_id)->first();
                $kelas = $kelases->id;
            }else{
                $kelas = $request->kelas;
            }
            $semester = Semester::find($request->tahun_ajaran);
            $registerCounter = $semester->tahunAjaran->psbRegisterCounter()->select('register_intern','register_extern')->where('unit_id',$unit_id)->where('student_status_id',$request->siswa_baru)->first();
            
            // bikin number register
            $register_number = CodeGeneratorPsb::RegisterNumber($unit_id,$semester->academic_year_id);

            // Sudah pernah di Auliya
            if($request->existing == "2"){
                $siswa = IdentitasSiswa::find($request->siswa_id);

                $isCandidateExist = auth()->user()->orangtua->calonSiswa()->count() > 0 && in_array($siswa->id,auth()->user()->orangtua->calonSiswa()->select('student_id')->get()->pluck('student_id')->toArray()) ? true : false;

                if(!$siswa || $siswa->parent_id != auth()->user()->user_id || $isCandidateExist){
                    return redirect()->route('psb.index')->with('danger','Pendaftaran calon siswa gagal. Mohon periksa kembali data putra/i Anda.');
                }

                $calon = CalonSiswa::create([
                    'unit_id' => $unit_id,
                    'student_id' => $siswa->id,
                    'student_nis' => $siswa->student_nis,
                    'student_nisn' => $siswa->student_nisn,
                    'student_name' => $siswa->student_name,
                    'student_nickname' => $siswa->student_nickname,
                    'nik' => $siswa->nik,
                    'student_status_id' => $request->siswa_baru,
                    'reg_number' => $register_number,
                    // 'reg_number' => $unit->name.$semester->tahunAjaran->academic_year_start.sprintf('%04d',($registerCounter ? ($registerCounter->register_intern+$registerCounter->register_extern)+1 : '1')),
                    'academic_year_id' => $semester->academic_year_id,
        
                    'birth_place' => $siswa->birth_place,
                    'birth_date' => $siswa->birth_date,
                    'gender_id' => $siswa->gender_id,
                    'religion_id' => $siswa->religion_id,
                    'child_of' => null,
                    'family_status' => null,
                    
                    'join_date' => null,
                    'semester_id' => $request->tahun_ajaran,
                    'level_id' => $kelas,
                    'address' => $siswa->address,
                    'address_number' => $siswa->address_number,
                    'rt' => $siswa->rt,
                    'rw' => $siswa->rw,
                    'region_id' => $siswa->region_id,
        
                    'origin_school' => $request->asal_sekolah,
                    'origin_school_address' => '',
                    
                    'sibling_name' => $siswa->sibling_name,
                    'sibling_level_id' => $siswa->sibling_level_id,
        
                    'info_from' => $siswa->info_from,
                    'info_name' => $siswa->info_name,
                    'position' => $siswa->position,
                    'status_id' => 1,
        
                    'parent_id' => auth()->user()->user_id,
                ]);
            }else{
                $agama = Agama::where('name','Islam')->first();
                $desa = Wilayah::where('code',$request->desa)->first();
                $level = Level::find($request->kelas);
                $request->validate([
                    "nik" => "required",
                    "nama" => "required",
                    "tempat_lahir" => "required",
                    "tanggal_lahir" => "required",
                    "jenis_kelamin" => "required",
                    "agama" => "required",
                    "alamat" => "required",
                    "rt" => "required",
                    "rw" => "required",
                    "desa" => "required",
                    "kelas" => "required",
                ]);
                $calon = CalonSiswa::create([
                    'unit_id' => $unit_id,
                    'student_nis' => $request->nis,
                    'student_nisn' => $request->nisn,
                    'student_name' => $request->nama,
                    'student_nickname' => $request->nama_pendek,
                    'nik' => $request->nik,
                    'student_status_id' => $request->siswa_baru,
                    'reg_number' => $register_number,
                    // 'reg_number' => $unit->name.$semester->tahunAjaran->academic_year_start.sprintf('%04d',($registerCounter ? ($registerCounter->register_intern+$registerCounter->register_extern)+1 : '1')),
                    'academic_year_id' => $semester->academic_year_id,
        
                    'birth_place' => $request->tempat_lahir,
                    'birth_date' => $request->tanggal_lahir,
                    'gender_id' => $request->jenis_kelamin,
                    'religion_id' => $agama->id,
                    'child_of' => null,
                    'family_status' => null,
                    
                    'join_date' => null,
                    'semester_id' => $request->tahun_ajaran,
                    'level_id' => $kelas,
                    'address' => $request->alamat,
                    'address_number' => $request->no_rumah,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'region_id' => $desa->id,
        
                    'origin_school' => $request->asal_sekolah,
                    'origin_school_address' => $request->alamat_asal_sekolah,
                    
                    'sibling_name' => $request->saudara_nama,
                    'sibling_level_id' => $request->saudara_kelas,
        
                    'info_from' => $request->info_dari,
                    'info_name' => $request->info_nama,
                    'position' => $request->posisi,
                    'status_id' => 1,
        
                    'parent_id' => auth()->user()->user_id,
                ]);
            }

            RegisterCounterService::addCounter($calon->id,'register');

            Session::flash('success','Pendaftaran calon siswa berhasil. Selanjutnya, silakan klik fitur nama calon siswa.');
        }
        else Session::flash('danger','Pendaftaran calon siswa gagal. Saat ini pendaftaran masih ditutup.');

        return redirect()->route('psb.index');
    }

    public function storeOrtu(Request $request)
    {

        // $rules = array(
        //     'username' => 'required|unique:login_user|min:6|max:255',
        //     'password' => 'required|min:8',
        // );

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails())
        // {
        //     return redirect()->back()->withInput()->withErrors($validator)->with('danger', 'Username telah digunakan');
        // }

        // dd($request);

        if($request->siswa == 0){
            $messages = [
                'father_name.required' => 'Mohon masukkan nama ayah',
                'father_phone.required' => 'Mohon masukkan nomor telepon ayah yang valid',
                'father_phone.max' => 'Mohon periksa kembali nomor telepon ayah',
                'father_email.required' => 'Mohon masukkan alamat email ayah',
                'father_email.email' => 'Mohon periksa kembali alamat email ayah',
                'mother_name.required' => 'Mohon masukkan nama ibu',
                'mother_phone.required' => 'Mohon masukkan nomor telepon ibu yang valid',
                'mother_phone.max' => 'Mohon periksa kembali nomor telepon ibu',
                'mother_email.required' => 'Mohon masukkan alamat email ibu',
                'mother_email.email' => 'Mohon periksa kembali alamat email ibu',
            ];

            $this->validate($request, [
                'father_name' => 'required',
                'father_phone' => 'required|max:15',
                'father_email' => 'required|email',
                'mother_name' => 'required',
                'mother_phone' => 'required|max:15',
                'mother_email' => 'required|email',
            ], $messages);
          
            if(OrangTua::where('father_email',$request->father_email)->count() < 1){
                $ortu = OrangTua::create([
                    'father_name' => $request->father_name,
                    'father_phone' => $request->father_phone,
                    'father_email' => $request->father_email,
                    'mother_name' => $request->mother_name,
                    'mother_phone' => $request->mother_phone,
                    'mother_email' => $request->mother_email,
                ]);
                $ortu_id = $ortu->id;
    
                $user = LoginUser::create([
                    'username' => $request->father_email,
                    'password' => bcrypt($request->father_phone),
                    'active_status_id' => 1,
                    'role_id' => 36,
                    'user_id' => $ortu_id,
                ]);
            }
            elseif(isset($request->father_email) && (OrangTua::where('father_email',$request->father_email)->count() > 0)){
                return redirect()->back()->with('exist', 'Mohon maaf, email sudah terdaftar');
            }
            else{
                return redirect()->back()->with('danger', 'Maaf, harap isikan semua data dengan lengkap');
            }
        }else{
            $siswa = Siswa::where('student_nis',$request->nipd)->first();
            if(!$siswa) return redirect()->back()->with('danger', 'Data Siswa tidak ditemukan');
            // dd($siswa);
            
            $ortu = $siswa->identitas->orangtua;
            $ortu_id = $ortu->id;
            
            $check_account = LoginUser::where('user_id',$ortu_id)->where('role_id',36)->first();
            if($check_account) return redirect()->back()->with('danger', 'Akun Orang Tua Telah Terdaftar');

            if(!$ortu->father_email || !$ortu->father_phone) return redirect()->back()->with('danger', 'Data orang tua terdaftar belum lengkap, mohon hubungi staf administrasi Auliya untuk informasi lebih lanjut');
            
            $user = LoginUser::create([
                'username' => $ortu->father_email,
                'password' => bcrypt($ortu->father_phone),
                'active_status_id' => 1,
                'role_id' => 36,
                'user_id' => $ortu_id,
            ]);
        }

        $credentials = [
            'username' => $ortu->father_email,
            'password' => $ortu->father_phone
        ];
        
        if (Auth::attempt($credentials)) {
            return redirect('/psb/index');
        }else{
            return redirect()->back()->with('danger', 'Username atau password salah');
        }
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
        
        $parent_id = false;

        if( $request->nip ){
            $check_parent = OrangTua::where('employee_id',$request->nip)->first();
            if($check_parent){
                $parent_id = $check_parent->id;
            }
        }else if( $request->father_ktp ){
            $check_parent = OrangTua::where('father_nik',$request->father_ktp)->first();
            if($check_parent){
                $parent_id = $check_parent->id;
            }
        }else if( $request->mother_ktp ){
            $check_parent = OrangTua::where('mother_nik',$request->mother_ktp)->first();
            if($check_parent){
                $parent_id = $check_parent->id;
            }
        }

        if($parent_id){
            // dd($parent_id);
        }else{
            $orangTua = OrangTua::create([
                'employee_id' => $request->nip,
                'father_name' => $request->father_name,
                'father_nik' => $request->father_ktp,
                'father_phone' => $request->father_phone,
                'father_email' => $request->father_email,
                'father_job' => $request->father_job,
                'father_position'=> $request->father_position, //jabatan ayah
                'father_phone_office' => $request->father_job_phone,
                'father_job_address'=> $request->father_job_address, //alamat kantor ayah
                'father_salary'=> $request->father_salary, //gaji ayah
    
                'mother_name' => $request->mother_name,
                'mother_nik' => $request->mother_ktp,
                'mother_phone' => $request->mother_phone,
                'mother_email' => $request->mother_email,
                'mother_job' => $request->mother_job,
                'mother_position'=> $request->mother_position, //jabatan ayah
                'mother_phone_office' => $request->mother_job_phone,
                'mother_job_address'=> $request->mother_job_address, //alamat kantor ayah
                'mother_salary'=> $request->mother_salary, //gaji ayah
    
                'parent_address' => $request->alamat_ortu,
                'parent_phone_number' => $request->no_hp_ortu,
    
                'guardian_name' => $request->nama_wali,
                'guardian_nik' => $request->nik_wali,
                'guardian_phone_number' => $request->no_hp_wali,
                'guardian_email' => $request->email_wali,
                'guardian_job' => $request->pekerjaan_wali,
                'guardian_position'=> $request->jabatan_wali, //jabatan wali
                'guardian_phone_office' => $request->telp_kantor_wali,
                'guardian_job_address'=> $request->alamat_kantor_wali, //alamat kantor wali
                'guardian_salary'=> $request->gaji_wali, //gaji wali
                'guardian_address' => $request->alamat_wali,
            ]);

            $parent_id = $orangTua->id;
        }


        $desa = Wilayah::where('code',$request->desa)->first();

        $siswa = CalonSiswa::create([
            'unit_id' => $request->unit_id,
            // 'student_nis' => $request->nis, // generate jika diterima
            'student_nis' => $request->nis, // generate jika diterima
            'student_nisn' => $request->nisn,   // harusnya diisi
            'student_name' => $request->name,
            'student_nickname' => $request->nickname,
            'academic_year_id' => $request->academic_year_id,
            // 'reg_number' => $request->nomor_registrasi, // gimana yaa
            'reg_number' => 0, // gimana yaa

            'birth_place' => $request->born_place,
            'birth_date' => $request->born_date,
            'gender_id' => $request->gender_id,
            // 'religion_id' => $request->religion_id,
            'religion_id' => 1,
            'child_of' => 1,  //$request->anak_ke,    // diisi
            'family_status' => 'Anak Kandung', //$request->status_anak, // diisi
            
            'join_date' => $request->join_date, // saat diterima
            'level_id' => $request->level_id, // kelas
            'address' => $request->address,
            'address_number' => $request->address_number,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'region_id' => $desa->id,

            'origin_school' => $request->asal_sekolah,  
            'origin_school_address' => $request->alamat_asal_sekolah, 
            
            'sibling_name' => $request->saudara_nama,   // diisi
            'sibling_level_id' => $request->saudara_kelas, // diisi

            'info_from' => $request->info_dari, // diisi
            'info_name' => $request->info_nama, // diisi
            'position' => $request->posisi, // diisi

            'parent_id' => $parent_id,

            'status_id' => 1,
        ]);
        
        $user = LoginUser::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'active_status_id' => 1,
            'role_id' => 36,
            'user_id' => $parent_id,
        ]);
        // return $user;

        $counter = RegisterCounter::where('unit_id',$siswa->unit_id)->where('academic_year_id',$siswa->academic_year_id)->first();

        if($counter){
            if($siswa->asal_sekolah == 'SIT Auliya'){
                $counter->register_intern = $counter->register_intern + 1;
            }else{
                $counter->register_extern = $counter->register_extern + 1;
            }
        }else{
            if($siswa->asal_sekolah == 'SIT Auliya'){
                $counter = RegisterCounter::create([
                    'academic_year_id' => $siswa->academic_year_id,
                    'unit_id' => $siswa->unit_id,
                    'register_intern' => 1,
                ]);
            }else{
                $counter = RegisterCounter::create([
                    'academic_year_id' => $siswa->academic_year_id,
                    'unit_id' => $siswa->unit_id,
                    'register_extern' => 1,
                ]);
            }

        }

        return redirect('/psb')->with('success','Pendaftaran Calon Siswa Baru Berhasil');
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
}
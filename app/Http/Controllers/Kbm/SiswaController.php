<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kbm\SiswaDatatableCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;

use App\Models\Kbm\Semester;

use App\Models\Rekrutmen\Pegawai;

use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Siswa;

use App\Models\Level;
use App\Models\Unit;
use App\Models\Wilayah;
use App\Models\Agama;
use App\Models\JenisKelamin;

use App\Http\Resources\Siswa\SiswaCollection;
use App\Http\Resources\Siswa\SiswaResource;

use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class SiswaController extends Controller
{
    
    public function index()
    {

        ini_set('max_execution_time', 0);
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';
        // dd(Auth::user()->pegawai->unit_id);
        return view('kbm.siswa.index',compact('levels','level'));
    }
    
    public function indexAlumni()
    {

        ini_set('max_execution_time', 0);
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $siswas = Siswa::where('is_lulus',1)->with('identitas')->get()->sortBy('identitas.student_name');
            $levels = Level::all();
        }else{
            $siswas = Siswa::where('is_lulus',1)->where('unit_id',$unit)->with('identitas')->get()->sortBy('identitas.student_name');
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';
        // dd(Auth::user()->pegawai->unit_id);
        return view('kbm.siswa.index',compact('siswas','levels','level'));
    }

    public function filter(Request $request)
    {
        if($request->level=='semua'){
            return redirect('/kependidikan/kbm/siswa');
        }
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = $request->level;
        $siswas = Siswa::where('level_id',$level)->where('is_lulus',0)->with('identitas')->get()->sortBy('identitas.student_name');
        return view('kbm.siswa.index',compact('siswas','levels','level'));
    }

    public function filterAlumni(Request $request)
    {
        ini_set('max_execution_time', 0);
        if($request->level=='semua'){
            return redirect('/kependidikan/kbm/siswa/alumni');
        }
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = $request->level;
        $siswas = Siswa::where('level_id',$level)->where('is_lulus',1)->with('identitas')->get()->sortBy('identitas.student_name');
        return view('kbm.siswa.index',compact('siswas','levels','level'));
    }
    
    public function create()
    {
        //
        $agamas = Agama::all();
        $levels = Level::all();
        $units = Unit::all();
        $jeniskelamin = JenisKelamin::all();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();
        $provinsis = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();
        // dd($provinsis);
        return view('kbm.siswa.tambah',compact('provinsis','semesters','levels','agamas','jeniskelamin','units'));
    }
    
    public function store(Request $request)
    {
        //
        // dd($request);

        $request->validate([
            "nis" => "required",
            "nama" => "required",
            "nisn" => "required",
            "tempat_lahir" => "required",
            "tanggal_lahir" => "required",
            "jenis_kelamin" => "required",
            "agama" => "required",
            "anak_ke" => "required",
            "status_anak" => "required",
            "alamat" => "required",
            "rt" => "required",
            "rw" => "required",
            "provinsi" => "required",
            "kabupaten" => "required",
            "kecamatan" => "required",
            "desa" => "required",
            "tanggal_masuk" => "required",
            "semester_masuk" => "required",
            "kelas" => "required",
            "asal_sekolah" => "required",
            "alamat_asal_sekolah" => "required",
        ]);
            
        // dd($request);
        $region = $request->desa;

        $orangTua = OrangTua::create([
            'employee_id' => $request->kode_pegawai,
            'father_name' => $request->nama_ayah,
            'father_nik' => $request->nik_ayah,
            'father_phone' => $request->hp_ayah,
            'father_email' => $request->email_ayah,
            'father_job' => $request->pekerjaan_ayah,
            'father_position'=> $request->jabatan_ayah, //jabatan ayah
            'father_phone_office' => $request->telp_kantor_ayah,
            'father_job_address'=> $request->alamat_kantor_ayah, //alamat kantor ayah
            'father_salary'=> $request->gaji_ayah, //gaji ayah

            'mother_nik' => $request->nik_ibu,
            'mother_name' => $request->nama_ibu,
            'mother_phone' => $request->hp_ibu,
            'mother_email' => $request->email_ibu,
            'mother_job' => $request->pekerjaan_ibu,
            'mother_position'=> $request->jabatan_ibu, //jabatan ibu
            'mother_phone_office' => $request->telp_kantor_ibu,
            'mother_job_address'=> $request->alamat_kantor_ibu, //alamat kantor ibu
            'mother_salary'=> $request->gaji_ibu, //gaji ibu

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

        $desa = Wilayah::where('code',$request->desa)->first();

        Siswa::create([
            'unit_id' => $request->unit,
            'student_nis' => $request->nis,
            'student_nisn' => $request->nisn,
            'student_name' => $request->nama,
            'student_nickname' => $request->nama_pendek,
            'reg_number' => $request->nomor_registrasi,

            'birth_place' => $request->tempat_lahir,
            'birth_date' => $request->tanggal_lahir,
            'gender_id' => $request->jenis_kelamin,
            'religion_id' => $request->agama,
            'child_of' => $request->anak_ke,
            'family_status' => $request->status_anak,
            
            'join_date' => $request->tanggal_masuk,
            'semester_id' => $request->semester_masuk,
            'level_id' => $request->kelas,
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

            'parent_id' => $orangTua->id,
        ]);
        

        return redirect('/kependidikan/kbm/siswa')->with('success','Tambah Siswa Berhasil');
    }
    
    public function show($id)
    {
        //get data siswa
        $siswa = Siswa::with('identitas')->get()->find($id);
        
        if($siswa->identitas->region_id == null){
            $provinsi = null;
            $kabupaten = null;
            $kecamatan = null;
            $desa = null;
        }else{
            // dd($siswa);
            $desadata = Wilayah::find($siswa->identitas->region_id);
    
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


        $agamas = Agama::all();
        $levels = Level::all();
        $units = Unit::all();
        $jeniskelamin = JenisKelamin::all();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();
        $provinsis = Wilayah::whereRaw('LENGTH(code) = 2')->orderBy('name', 'ASC')->get();

        // dd($siswa->region_id);
        return view('kbm.siswa.lihat',compact('siswa','provinsi','kabupaten','kecamatan','desa','agamas','levels','units','semesters','provinsis'));
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
        //get data siswa
        $siswa = Siswa::with('identitas')->get()->find($id);
        
        if($siswa->identitas->region_id == null){
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
            $desadata = Wilayah::find($siswa->identitas->region_id);
    
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

        //untuk dropdown
        $agamas = Agama::all();
        $levels = Level::all();
        $semesters = Semester::orderBy('semester_id', 'ASC')->get();
        $jeniskelamin = JenisKelamin::all();
        $units = Unit::all();

        $pegawais = Pegawai::where(function($q){
            $q->whereHas('statusPernikahan',function($q){
                $q->where('status','menikah');
            })->aktif();
        });
        if($siswa && $siswa->identitas && $siswa->identitas->orangtua)
            $pegawais = $pegawais->orWhere('id',$siswa->identitas->orangtua->employee_id);
        $pegawais = $pegawais->orderBy('name','asc')->pluck('name','id');

        // dd($listkabupaten);
        return view(
            'kbm.siswa.ubah',
            compact(
                'siswa',
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'listprovinsi',
                'listkabupaten',
                'listkecamatan',
                'listdesa',
                'agamas',
                'levels',
                'units',
                'semesters',
                'jeniskelamin',
                'pegawais'
            )
        );
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
        $messages = [
            'kelas.required' => 'Mohon pilih salah satu tingkat kelas',
        ];

        $request->validate([
            "nik" => "required",
            "nis" => "required",
            "nama" => "required",
            "nisn" => "required",
            "tempat_lahir" => "required",
            "tanggal_lahir" => "required",
            "jenis_kelamin" => "required",
            "agama" => "required",
            "alamat" => "required",
            "rt" => "required",
            "rw" => "required",
            "provinsi" => "required",
            "kabupaten" => "required",
            "kecamatan" => "required",
            "desa" => "required",
            "semester_masuk" => "required",
            "asal_sekolah" => "required",
        ]);

        $role = auth()->user()->role->name;

        $desa = Wilayah::where('code',$request->desa)->first();

        $siswa = Siswa::find($id);

        if($siswa && $siswa->is_lulus != 1){
            $validator = Validator::make($request->all(), [
                "kelas" => "required",
            ], $messages);

            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $idensis = $siswa->identitas;
        $parentid = $idensis->parent_id;
        $siswa->unit_id = $request->unit;
        $siswa->student_nis = $request->nis;
        $siswa->student_nisn = $request->nisn;
        $idensis->nik = $request->nik;
        $idensis->student_name = $request->nama;
        $idensis->student_nickname = $request->nama_pendek;
        $siswa->reg_number = $request->nomor_registrasi;

        $idensis->birth_place = $request->tempat_lahir;
        $idensis->birth_date = $request->tanggal_lahir;
        $idensis->gender_id = $request->jenis_kelamin;
        $idensis->religion_id = $request->agama;
        $idensis->child_of = $request->anak_ke;
        $idensis->family_status = $request->status_anak;
        
        $siswa->join_date = $request->tanggal_masuk;
        $siswa->semester_id = $request->semester_masuk;
        if($siswa->is_lulus != 1){
            $siswa->level_id = $request->kelas;
        }
        $idensis->address = $request->alamat;
        $idensis->address_number = $request->no_rumah;
        $idensis->rt = $request->rt;
        $idensis->rw = $request->rw;
        $idensis->region_id = $desa->id;

        $siswa->origin_school = $request->asal_sekolah;
        $siswa->origin_school_address = $request->alamat_asal_sekolah;
        
        $idensis->sibling_name = $request->saudara_nama;
        $idensis->sibling_level_id = $request->saudara_kelas;

        $siswa->info_from = $request->info_dari;
        $siswa->info_name = $request->info_nama;
        $siswa->position = $request->posisi;
        $siswa->save();
        $idensis->save();

        $pegawai = Pegawai::select('id')->where('id',$request->employee)->where(function($q){
            $q->whereHas('statusPernikahan',function($q){
                $q->where('status','menikah');
            })->aktif();
        });
        if($siswa && $siswa->identitas && $siswa->identitas->orangtua)
            $pegawai = $pegawai->orWhere('id',$siswa->identitas->orangtua->employee_id);
        $pegawai = $pegawai->first();
        
        $ortu = OrangTua::find($parentid);
        //$ortu->employee_id = $request->kode_pegawai;
        if($pegawai) $ortu->employee_id = $request->employee;
        elseif(!$pegawai && $request->employeeOpt == 'no') $ortu->employee_id = null;

        if(in_array($role,['sek'])){
            $ortu->father_name = $request->nama_ayah;
            $ortu->father_phone = $request->hp_ayah;
    
            $ortu->mother_name = $request->nama_ibu;
            $ortu->mother_phone = $request->hp_ibu;
    
            $ortu->parent_address = $request->alamat_ortu;
            $ortu->parent_phone_number = $request->no_hp_ortu;
    
            $ortu->guardian_name = $request->nama_wali;
            $ortu->guardian_phone_number = $request->no_hp_wali;
            $ortu->save();
        }
        elseif(in_array($role,['admin','aspv','as'])){
            $ortu->father_name = $request->nama_ayah;
            $ortu->father_nik = $request->nik_ayah;
            $ortu->father_phone = $request->hp_ayah;
            $ortu->father_email = $request->email_ayah;
            $ortu->father_job = $request->pekerjaan_ayah;
            $ortu->father_position= $request->jabatan_ayah; //jabatan ayah
            $ortu->father_phone_office = $request->telp_kantor_ayah;
            $ortu->father_job_address= $request->alamat_kantor_ayah; //alamat kantor ayah
            $ortu->father_salary= $request->gaji_ayah; //gaji ayah

            $ortu->mother_nik = $request->nik_ibu;
            $ortu->mother_name = $request->nama_ibu;
            $ortu->mother_phone = $request->hp_ibu;
            $ortu->mother_email = $request->email_ibu;
            $ortu->mother_job = $request->pekerjaan_ibu;
            $ortu->mother_position= $request->jabatan_ibu; //jabatan ibu
            $ortu->mother_phone_office = $request->telp_kantor_ibu;
            $ortu->mother_job_address= $request->alamat_kantor_ibu; //alamat kantor ibu
            $ortu->mother_salary= $request->gaji_ibu; //gaji ibu

            $ortu->parent_address = $request->alamat_ortu;
            $ortu->parent_phone_number = $request->no_hp_ortu;

            $ortu->guardian_name = $request->nama_wali;
            $ortu->guardian_nik = $request->nik_wali;
            $ortu->guardian_phone_number = $request->no_hp_wali;
            $ortu->guardian_email = $request->email_wali;
            $ortu->guardian_job = $request->pekerjaan_wali;
            $ortu->guardian_position= $request->jabatan_wali; //jabatan wali
            $ortu->guardian_phone_office = $request->telp_kantor_wali;
            $ortu->guardian_job_address = $request->alamat_kantor_wali; //alamat kantor wali
            $ortu->guardian_salary = $request->gaji_wali; //gaji wali
            $ortu->guardian_address = $request->alamat_wali;
            $ortu->save();
        }

        return redirect('/kependidikan/kbm/siswa/aktif')->with('success','Ubah Siswa Berhasil');
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStartOfSpp(Request $request)
    {
        $siswa = Siswa::find($request->id);
        if(!$siswa) return redirect()->back()->with('error', 'Ubah awal mula SPP siswa gagal');
        $siswa->year_spp = $request->year_spp;
        $siswa->month_spp = $request->month_spp;
        $siswa->save();

        return redirect()->back()->with('success', 'Awal mula SPP siswa berhasil diubah');
    }

    public function updateNisn(Request $request)
    {
        # code...
        $request->validate([
            "nisn" => "required",
        ]);

        $siswa = Siswa::find($request->id);

        $siswa->student_nisn = $request->nisn;

        $siswa->save();
        return redirect('/kependidikan/kbm/siswa')->with('success','Ubah Siswa Berhasil');

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
        $siswa = Siswa::find($id);
        // $kelas->delete();
        $siswa->is_lulus = 2;
        $siswa->save();
        return redirect('/kependidikan/kbm/siswa')->with('success','Hapus Siswa Berhasil!');
    }

    public function importView()
    {        
        // $ortu = OrangTua::where('father_nik','=','12')->first();
        // dd($ortu);
        return view('kbm.siswa.import');
    }

    public function import(Request $request) 
    {        

        ini_set('max_execution_time', 0);
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        // dd("masuk");
        // menangkap file excel
        $file = $request->file('file');
 
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
 
        // upload ke folder file_siswa di dalam folder public
        $file->move('file_siswa',$nama_file);
 
        // dd("masuk");
        // import data
        Excel::import(new SiswaImport, public_path('/file_siswa/'.$nama_file));
 
 
        // alihkan halaman kembali
        return redirect('/siswa/import')->with('success','Import Siswa Berhasil!');
    }


    public function newIndex()
    {

        ini_set('max_execution_time', 0);
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';
        // dd(Auth::user()->pegawai->unit_id);
        return view('kbm.siswa.new',compact('levels','level'));
    }

    public function onLoadIndex()
    {
        ini_set('max_execution_time', 0);
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $siswas = Siswa::orderBy('id', 'ASC')->get();
        }else{
            $siswas = Siswa::orderBy('id', 'ASC')->where('unit_id',$unit)->get();
        }

        return response()->json($siswas);
    }

    public function test()
    {
        ini_set('max_execution_time', 0);
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $siswas = Siswa::orderBy('id', 'ASC')->get();
        }else{
            $siswas = Siswa::orderBy('id', 'ASC')->where('unit_id',$unit)->get();
        }

        return new SiswaCollection($siswas);
    }

    public function datatablesSiswa(Request $request)
    {
        # code...
        // dd($request->search['value']);
        // dd($request->order[0]["column"].$request->order[0]["dir"]);
        $unit = Auth::user()->pegawai->unit_id;

        if($request->order[0]["column"] == 0){
            $ordered_column = 'student_nis';
        }else if($request->order[0]["column"] == 1){
            $ordered_column = 'student_nisn';
        }else if($request->order[0]["column"] == 2){
            $ordered_column = 'identitas.student_name';
        }else if($request->order[0]["column"] == 3){
            $ordered_column = 'identitas.birth_date';
        }else if($request->order[0]["column"] == 4){
            $ordered_column = 'identitas.gender_id';
        }else{
            $ordered_column = 'student_nis';
        }

        if($request->filter=='semua'){
            if($unit == 5){
                $siswas = Siswa::where('is_lulus',0)->with('identitas')
                ->where(
                    function ($q) use ($request){
                        $q->orWhere('student_nis','like','%'.$request->search['value'].'%')
                        ->orWhere('student_nisn','like','%'.$request->search['value'].'%')
                        ->orWhereHas('identitas',function ($q) use ($request){
                            $q->where('student_name','like','%'.$request->search['value'].'%');
                        });
                    }
                )->get();
                $total = Siswa::where('is_lulus',0)->with('identitas')->count();
                $levels = Level::all();
            }else{
                $siswas = Siswa::where('is_lulus',0)->where('unit_id',$unit)
                ->where(
                    function ($q) use ($request){
                        $q->orWhere('student_nis','like','%'.$request->search['value'].'%')
                        ->orWhere('student_nisn','like','%'.$request->search['value'].'%')
                        ->orWhereHas('identitas',function ($q) use ($request){
                            $q->where('student_name','like','%'.$request->search['value'].'%');
                        });
                    }
                )->with('identitas')->get();
                $total = Siswa::where('is_lulus',0)->where('unit_id',$unit)->with('identitas')->get()->sortBy('identitas.student_name')->count();
                $levels = Level::where('unit_id',$unit)->get();
            }

        }else{
            if($unit == 5){
                $siswas = Siswa::where('is_lulus',0)->where('level_id',$request->filter)->with('identitas')
                ->where(
                    function ($q) use ($request){
                        $q->orWhere('student_nis','like','%'.$request->search['value'].'%')
                        ->orWhere('student_nisn','like','%'.$request->search['value'].'%')
                        ->orWhereHas('identitas',function ($q) use ($request){
                            $q->where('student_name','like','%'.$request->search['value'].'%');
                        });
                    }
                )->get();
                // dd($siswas[0]->level_id,$request->filter);
                $total = Siswa::where('is_lulus',0)->where('level_id',$request->filter)->with('identitas')->count();
                $levels = Level::all();
            }else{
                $siswas = Siswa::where('is_lulus',0)->where('level_id',$request->filter)->where('unit_id',$unit)->with('identitas')
                ->where(
                    function ($q) use ($request){
                        $q->orWhere('student_nis','like','%'.$request->search['value'].'%')
                        ->orWhere('student_nisn','like','%'.$request->search['value'].'%')
                        ->orWhereHas('identitas',function ($q) use ($request){
                            $q->where('student_name','like','%'.$request->search['value'].'%');
                        });
                    }
                )->get();
                $total = Siswa::where('is_lulus',0)->where('level_id',$request->filter)->where('unit_id',$unit)->with('identitas')->get()->sortBy('identitas.student_name')->count();
                $levels = Level::where('unit_id',$unit)->get();
            }
        }

        if($request->order[0]["dir"] == 'asc'){
            $siswas = $siswas->sortBy($ordered_column)->skip($request->start)->take($request->length);
        }else{
            $siswas = $siswas->sortByDesc($ordered_column)->skip($request->start)->take($request->length);
        }

        $data = new SiswaDatatableCollection($siswas);

        $object = new stdClass();
        $object->draw = $request->draw;
        $object->recordsTotal = $total;
        $object->recordsFiltered = $total;
        $object->data = $data;
        
        return response()->json($object,200);
    }

    public function datatablesAlumni(Request $request)
    {
        # code...
        $unit = Auth::user()->pegawai->unit_id;
        if($request->order[0]["column"] == 0){
            $ordered_column = 'student_nis';
        }else if($request->order[0]["column"] == 1){
            $ordered_column = 'student_nisn';
        }else if($request->order[0]["column"] == 2){
            $ordered_column = 'identitas.student_name';
        }else if($request->order[0]["column"] == 3){
            $ordered_column = 'identitas.birth_date';
        }else if($request->order[0]["column"] == 4){
            $ordered_column = 'identitas.gender_id';
        }else{
            $ordered_column = 'student_nis';
        }

        if($unit == 5){
            $siswas = Siswa::where('is_lulus',1)->with('identitas')
            ->where(
                function ($q) use ($request){
                    $q->orWhere('student_nis','like','%'.$request->search['value'].'%')
                    ->orWhere('student_nisn','like','%'.$request->search['value'].'%')
                    ->orWhereHas('identitas',function ($q) use ($request){
                        $q->where('student_name','like','%'.$request->search['value'].'%');
                    });
                }
            )->get();
            $total = Siswa::where('is_lulus',1)->with('identitas')->count();
            $levels = Level::all();
        }else{
            $siswas = Siswa::where('is_lulus',1)->where('unit_id',$unit)->with('identitas')
            ->where(
                function ($q) use ($request){
                    $q->orWhere('student_nis','like','%'.$request->search['value'].'%')
                    ->orWhere('student_nisn','like','%'.$request->search['value'].'%')
                    ->orWhereHas('identitas',function ($q) use ($request){
                        $q->where('student_name','like','%'.$request->search['value'].'%');
                    });
                }
            )->get();
            $total = Siswa::where('is_lulus',1)->where('unit_id',$unit)->with('identitas')->count();
            $levels = Level::where('unit_id',$unit)->get();
        }

        if($request->order[0]["dir"] == 'asc'){
            $siswas = $siswas->sortBy($ordered_column)->skip($request->start)->take($request->length);
        }else{
            $siswas = $siswas->sortByDesc($ordered_column)->skip($request->start)->take($request->length);
        }

        $data = new SiswaDatatableCollection($siswas);

        $object = new stdClass();
        $object->draw = $request->draw;
        $object->recordsTotal = $total;
        $object->recordsFiltered = $total;
        $object->data = $data;
        
        return response()->json($object,200);
    }

    public function downloadSiswa(Request $request)
    {
        $unit = Auth::user()->pegawai->unit_id;

        if($request->filter=='semua'){
            if($unit == 5){
                $siswas = Siswa::where('is_lulus',0)->with('identitas')->get()->sortBy('identitas.student_name');
                $title = 'Daftar Semua Siswa SIT AULIYA';
            }else{
                $siswas = Siswa::where('is_lulus',0)->where('unit_id',$unit)->get()->sortBy('identitas.student_name');
                $unitnya = Unit::find($unit);
                $title = 'Daftar Semua Siswa '.$unitnya->short_desc;
            }

        }else{
            if($unit == 5){
                $siswas = Siswa::where('is_lulus',0)->where('level_id',$request->filter)->with('identitas')->get()->sortBy('identitas.student_name');
            }else{
                $siswas = Siswa::where('is_lulus',0)->where('level_id',$request->filter)->where('unit_id',$unit)->with('identitas')->get()->sortBy('identitas.student_name');
            }
            $levelnya = Level::find($request->filter);
            $title = 'Daftar Siswa Kelas '.$levelnya->level.' '.$levelnya->unit->short_desc;
        }
        // dd($siswas);

        // init sheet
        $spreadsheet = new Spreadsheet;

        // init column & row
        $column_init = 'A';
        $row_init = 1;
        $sheet = 0;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheet);

        $list_judul = [
            'ID',
            'No Pendaftaran',
            'Program',
            'Tanggal Daftar',
            'Tahun Ajaran',
            'Tingkat Kelas',
            'NIPD',
            'NISN',
            'NIK',
            'Nama',
            'Nama Panggilan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Anak Ke',
            'Status Anak',
            'Alamat',
            'No',
            'RT',
            'RW',
            'Wilayah',
            'Nama Ayah',
            'NIK Ayah',
            'HP Ayah',
            'Email Ayah',
            'Pekerjaan Ayah',
            'Jabatan Ayah',
            'Telp Kantor Ayah',
            'Alamat Kantor Ayah',
            'Gaji Ayah',
            'Nama Ibu',
            'NIK Ibu',
            'HP Ibu',
            'HP Email Ibu',
            'Pekerjaan Ibu',
            'Jabatan Ibu',
            'Telp Kantor Ibu',
            'Alamat Kantor Ibu',
            'Gaji Ibu',
            'NIP (Orang tua yang bekerja di Auliya)',
            'Alamat Orang Tua',
            'HP Alternatif',
            'Nama Wali',
            'NIK Wali',
            'HP Wali',
            'HP Email Wali',
            'Pekerjaan Wali',
            'Jabatan Wali',
            'Telp Kantor Wali',
            'Alamat Kantor Wali',
            'Gaji Wali',
            'Alamat Wali',
            'Asal Sekolah',
            'Saudara Kandung',
            'Nama Saudara',
            'Info Dari',
            'Nama',
            'Posisi',
            'Kelas',
        ];

        $row = $row_init;
        $column = $column_init;
        foreach($list_judul as $judul){
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $judul);
            $column++;
        }

        foreach($siswas as $siswa){
            $row++;
            $column = $column_init;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->id);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->reg_number);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->unit->name);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->join_date);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->semester_id?$siswa->semester->semester_id:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->level_id?$siswa->level->level:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nis);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nisn);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValueExplicit($column.$row, $siswa->identitas->nik ? strval($siswa->identitas->nik) : '', DataType::TYPE_STRING);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->student_name);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->student_nickname);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->birth_place);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->birth_date);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->gender_id?ucwords($siswa->identitas->jeniskelamin->name):'');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->religion_id?$siswa->agama->name:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->child_of);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->family_status);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->address_number);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->rt);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->rw);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->wilayah ? $siswa->identitas->wilayah->name.', '.$siswa->identitas->wilayah->kecamatanName().', '.$siswa->identitas->wilayah->kabupatenName().', '.$siswa->identitas->wilayah->provinsiName() : '');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_name);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->father_nik)>120?decrypt($siswa->identitas->orangtua->father_nik):$siswa->identitas->orangtua->father_nik;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->father_phone)>120?decrypt($siswa->identitas->orangtua->father_phone):$siswa->identitas->orangtua->father_phone;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_email);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_job);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_phone_office);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_job_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_salary);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_name);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->mother_nik)>120?decrypt($siswa->identitas->orangtua->mother_nik):$siswa->identitas->orangtua->mother_nik;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->mother_phone)>120?decrypt($siswa->identitas->orangtua->mother_phone):$siswa->identitas->orangtua->mother_phone;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_email);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_job);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_phone_office);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_job_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_salary);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->employee_id);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->parent_address)>120?decrypt($siswa->identitas->orangtua->parent_address):$siswa->identitas->orangtua->parent_address;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->parent_phone_number)>120?decrypt($siswa->identitas->orangtua->parent_phone_number):$siswa->identitas->orangtua->parent_phone_number;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_name);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->guardian_nik)>120?decrypt($siswa->identitas->orangtua->guardian_nik):$siswa->identitas->orangtua->guardian_nik;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->guardian_phone_number)>120?decrypt($siswa->identitas->orangtua->guardian_phone_number):$siswa->identitas->orangtua->guardian_phone_number;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_email);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_job);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_phone_office);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_job_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_salary);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->origin_school!="Sekolah Islam Terpadu Auliya"?$siswa->origin_school_address:"Sekolah Islam Terpadu Auliya");
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->sibling_name?$siswa->identitas->sibling_name:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, ($siswa->identitas->sibling_level_id)?$siswa->identitas->levelsaudara->level:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->info_from);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->info_name);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->kelas ? $siswa->kelas->levelName : '');
        }
        $writer = new Xls($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$title.'.xls"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function downloadAlumni(Request $request)
    {
        $unit = Auth::user()->pegawai->unit_id;

        if($unit == 5){
            $siswas = Siswa::where('is_lulus',1)->with('identitas')->get()->sortBy('identitas.student_name');
            $title = 'Daftar Semua Siswa SIT AULIYA';
        }else{
            $siswas = Siswa::where('is_lulus',1)->where('unit_id',$unit)->with('identitas')->get()->sortBy('identitas.student_name');
            $unitnya = Unit::find($unit);
            $title = 'Daftar Semua Siswa '.$unitnya->short_desc;
        }

        // init sheet
        $spreadsheet = new Spreadsheet;

        // init column & row
        $column_init = 'A';
        $row_init = 1;
        $sheet = 0;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheet);

        $list_judul = [
            'ID',
            'No Pendaftaran',
            'Program',
            'Tanggal Daftar',
            'Tahun Ajaran',
            'Tingkat Kelas',
            'NIPD',
            'NISN',
            'NIK',
            'Nama',
            'Nama Panggilan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Anak Ke',
            'Status Anak',
            'Alamat',
            'No',
            'RT',
            'RW',
            'Wilayah',
            'Nama Ayah',
            'NIK Ayah',
            'HP Ayah',
            'Email Ayah',
            'Pekerjaan Ayah',
            'Jabatan Ayah',
            'Telp Kantor Ayah',
            'Alamat Kantor Ayah',
            'Gaji Ayah',
            'Nama Ibu',
            'NIK Ibu',
            'HP Ibu',
            'HP Email Ibu',
            'Pekerjaan Ibu',
            'Jabatan Ibu',
            'Telp Kantor Ibu',
            'Alamat Kantor Ibu',
            'Gaji Ibu',
            'NIP (Orang tua yang bekerja di Auliya)',
            'Alamat Orang Tua',
            'HP Alternatif',
            'Nama Wali',
            'NIK Wali',
            'HP Wali',
            'HP Email Wali',
            'Pekerjaan Wali',
            'Jabatan Wali',
            'Telp Kantor Wali',
            'Alamat Kantor Wali',
            'Gaji Wali',
            'Alamat Wali',
            'Asal Sekolah',
            'Saudara Kandung',
            'Nama Saudara',
            'Info Dari',
            'Nama',
            'Posisi',
        ];

        $row = $row_init;
        $column = $column_init;
        foreach($list_judul as $judul){
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $judul);
            $column++;
        }

        foreach($siswas as $siswa){
            $row++;
            $column = $column_init;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->id);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->reg_number);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->unit->name);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->join_date);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->semester_id?$siswa->semester->semester_id:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->level_id?$siswa->level->level:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nis);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nisn);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValueExplicit($column.$row, $siswa->nik ? strval($siswa->nik) : '', DataType::TYPE_STRING);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->student_name);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->student_nickname);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->birth_place);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->birth_date);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->gender_id?ucwords($siswa->identitas->jeniskelamin->name):'');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->religion_id?$siswa->agama->name:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->child_of);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->family_status);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->address_number);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->rt);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->rw);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->region_id);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_name);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->father_nik)>120?decrypt($siswa->identitas->orangtua->father_nik):$siswa->identitas->orangtua->father_nik;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->father_phone)>120?decrypt($siswa->identitas->orangtua->father_phone):$siswa->identitas->orangtua->father_phone;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_email);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_job);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_phone_office);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_job_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->father_salary);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_name);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->mother_nik)>120?decrypt($siswa->identitas->orangtua->mother_nik):$siswa->identitas->orangtua->mother_nik;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->mother_phone)>120?decrypt($siswa->identitas->orangtua->mother_phone):$siswa->identitas->orangtua->mother_phone;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_email);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_job);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_phone_office);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_job_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->mother_salary);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->employee_id);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->parent_address)>120?decrypt($siswa->identitas->orangtua->parent_address):$siswa->identitas->orangtua->parent_address;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->parent_phone_number)>120?decrypt($siswa->identitas->orangtua->parent_phone_number):$siswa->identitas->orangtua->parent_phone_number;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_name);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->guardian_nik)>120?decrypt($siswa->identitas->orangtua->guardian_nik):$siswa->identitas->orangtua->guardian_nik;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $value = strlen($siswa->identitas->orangtua->guardian_phone_number)>120?decrypt($siswa->identitas->orangtua->guardian_phone_number):$siswa->identitas->orangtua->guardian_phone_number;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_email);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_job);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_position);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_phone_office);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_job_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_salary);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->orangtua->guardian_address);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->origin_school!="Sekolah Islam Terpadu Auliya"?$siswa->origin_school_address:"Sekolah Islam Terpadu Auliya");
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->identitas->sibling_name?$siswa->identitas->sibling_name:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, ($siswa->identitas->sibling_level_id)?$siswa->identitas->levelsaudara->level:'-');
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->info_from);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->info_name);
            
            $column++;
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->position);
        }
        $writer = new Xls($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$title.'.xls"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}

<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use File;
use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Penempatan\PenempatanPegawai;
use App\Models\Penempatan\PenempatanPegawaiDetail;
use App\Models\Rekrutmen\CalonPegawai;
use App\Models\Rekrutmen\CalonPegawaiUnit;
use App\Models\Rekrutmen\EvaluasiPegawai;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiUnit;
use App\Models\Rekrutmen\LatarBidangStudi;
use App\Models\Rekrutmen\PendidikanTerakhir;
use App\Models\Rekrutmen\Spk;
use App\Models\Rekrutmen\StatusPegawai;
use App\Models\Rekrutmen\StatusPenerimaan;
use App\Models\Rekrutmen\StatusPernikahan;
use App\Models\Rekrutmen\Universitas;
use App\Models\JenisKelamin;
use App\Models\LoginUser;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\Wilayah;

class CalonPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek'])){
            $calon = CalonPegawai::whereHas('units',function($query) use($request){
                $query->where('unit_id',$request->user()->pegawai->unit_id);
            })->orderBy('created_at','desc')->get();
        }
        else{
            $calon = CalonPegawai::orderBy('created_at','desc')->get();
        }

        if(in_array($role,['etl','etm','sdms']))
            $folder = $role;
        else $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.calon_pegawai_index', compact('calon'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jeniskelamin = JenisKelamin::all();
        $pernikahan = StatusPernikahan::all();
        $provinsi = Wilayah::provinsi()->orderBy('name')->get();
        $pendidikan = PendidikanTerakhir::orderBy('id')->get();
        $latar = LatarBidangStudi::orderBy('name')->get();
        $universitas = Universitas::orderBy('name')->get();
        $penerimaan = StatusPenerimaan::all();
        $unit = Unit::all();
        $jabatan = JabatanUnit::all();
        $status = StatusPegawai::pegawaiAktif()->get();

        return view('kepegawaian.etm.calon_pegawai_tambah', compact('jeniskelamin','pernikahan','provinsi','pendidikan','latar','universitas','penerimaan','unit','jabatan','status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Mohon tuliskan nama lengkap dan gelar',
            'nickname.required' => 'Mohon tuliskan nama panggilan',
            'photo.file' => 'Pastikan foto adalah berkas yang valid',
            'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 5 MB',
            'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, atau .png',
            'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 100x200 px',
            'nik.required' => 'Mohon tuliskan NIK',
            'nik.numeric' => 'Pastikan NIK hanya mengandung angka',
            'npwp.numeric' => 'Pastikan NPWP hanya mengandung angka',
            'nuptk.numeric' => 'Pastikan NUPTK hanya mengandung angka',
            'nrg.numeric' => 'Pastikan NRG hanya mengandung angka',
            'gender.required' => 'Mohon pilih salah satu jenis kelamin',
            'birth_place.required' => 'Mohon tuliskan tempat lahir',
            'marriage_status.required' => 'Mohon pilih salah satu status perkawinan',
            'provinsi.required' => 'Mohon pilih salah satu provinsi',
            'kabupaten.required' => 'Mohon pilih salah satu kabupaten/kota',
            'kecamatan.required' => 'Mohon pilih salah satu kecamatan',
            'desa.required' => 'Mohon pilih salah satu desa/kelurahan',
            'address.required' => 'Mohon tuliskan alamat',
            'rt.required' => 'Mohon masukkan RT',
            'rt.numeric' => 'Pastikan RT hanya mengandung angka',
            'rw.required' => 'Mohon masukkan RW',
            'rw.numeric' => 'Pastikan RW hanya mengandung angka',
            'phone_number.required' => 'Mohon tuliskan nomor seluler',
            'phone_number.numeric' => 'Pastikan nomor seluler hanya mengandung angka',
            'email.required' => 'Mohon tuliskan alamat email',
            'recent_education.required' => 'Mohon pilih salah satu pendidikan terakhir',
            'academic_background.required' => 'Mohon pilih salah satu latar bidang studi',
            'competency.required' => 'Mohon tuliskan hasil tes kompetensi',
            'psychological.required' => 'Mohon tuliskan hasil tes psikologi',
            'acceptance_status.required' => 'Mohon pilih salah satu rekomendasi penerimaan',
            'unit.required_if' => 'Mohon pilih minimal salah satu unit penempatan yang direkomendasikan',
            'employee_status.required_if' => 'Mohon pilih salah satu status pegawai',
            'period_start.required_if' => 'Mohon tentukan awal masa kerja. ',
            'period_end.required_if' => 'Mohon tentukan akhir masa kerja',
            'period_end.after' => 'Pastikan akhir masa kerja berbeda dengan awal masa kerja',
        ];

        $this->validate($request, [
            'name' => 'required',
            'nickname' => 'required',
            'photo' => 'file|max:5120|mimes:jpg,jpeg,png|dimensions:min_width=100,min_height=200',
            'nik' => 'required|numeric',
            'npwp' => 'nullable|numeric',
            'nuptk' => 'nullable|numeric',
            'nrg' => 'nullable|numeric',
            'gender' => 'required',
            'birth_place' => 'required',
            'birthday_year' => 'required|numeric',
            'birthday_month' => 'required|numeric',
            'birthday_day' => 'required|numeric',
            'marriage_status' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'address' => 'required',
            'rt' => 'required|numeric',
            'rw' => 'required|numeric',
            'phone_number' => 'required|numeric',
            'email' => 'required',
            'recent_education' => 'required',
            'academic_background' => 'required',
            'competency' => 'required',
            'psychological' => 'required',
            'acceptance_status' => 'required',
            'unit' => 'required_if:acceptance_status,1',
            'employee_status' => 'required_if:acceptance_status,1',
            'period_start' => 'required_if:acceptance_status,1|date',
            'period_end' => 'required_if:acceptance_status,1|date|after:period_start'
        ], $messages);

        $region = Wilayah::where('code',$request->desa)->first();

        if($request->file('photo') && $request->file('photo')->isValid()) {
            // Pindah foto calon ke folder public
            $file = $request->file('photo');
            $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
            $file->move('img/photo/calon/',  $photo);
        }

        $calon = new CalonPegawai();
        $calon->name = $request->name;
        $calon->nickname = $request->nickname;
        $calon->photo = isset($photo) ? $photo : null;
        $calon->nik = $request->nik;
        $calon->npwp = isset($request->npwp) ? $request->npwp : null;
        $calon->nuptk = isset($request->nuptk) ? $request->nuptk : null;
        $calon->nrg = isset($request->nrg) ? $request->nrg : null;
        $calon->gender_id = $request->gender;
        $calon->birth_place = $request->birth_place;
        $calon->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
        $calon->marriage_status_id = $request->marriage_status;
        $calon->address = $request->address;
        $calon->rt = $request->rt;
        $calon->rw = $request->rw;
        $calon->region_id = isset($region) ? $region->id : null;
        $calon->phone_number = $request->phone_number;
        $calon->email = $request->email;
        $calon->recent_education_id = $request->recent_education;
        $recent_education = PendidikanTerakhir::find($request->recent_education);
        if(in_array($recent_education->name,["D1","D2","D3","S1","S2","S3"])){
            $calon->university_id = $request->university;
        }
        else{
            $calon->university_id = null;
        }
        $calon->academic_background_id = $request->academic_background;
        $calon->competency_test = $request->competency;
        $calon->psychological_test = $request->psychological;
        $calon->acceptance_status_id = $request->acceptance_status;
        if($request->acceptance_status == 1){
            $calon->unit_id = $request->unit[0];
            $calon->employee_status_id = $request->employee_status;
            $calon->period_start = Date::parse($request->period_start);
            $calon->period_end = Date::parse($request->period_end);
        }
        else{
            $calon->unit_id = null;
            $calon->employee_status_id =  null;
            $calon->period_start =  null;
            $calon->period_end =  null;
        }

        $calon->save();

        $calon->fresh();

        if($request->acceptance_status == 1 && count($request->unit) > 0){
            $calon->units()->attach($request->unit);
        }
        if($request->acceptance_status == 1 && isset($request->position) && count($request->position) > 0){
            $calon->jabatans()->attach($request->position);
        }

        Session::flash('success','Data '. $request->name .' berhasil ditambahkan');
        return redirect()->route('calon.index');
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
        
        if(in_array($role,['kepsek','wakasek'])){
            $calon = CalonPegawai::where('id', $id)->whereHas('units',function($query) use($request){
                $query->where('unit_id',$request->user()->pegawai->unit_id);
            })->first();
        }
        else{
            $calon = CalonPegawai::find($id);
        }

        if(in_array($role,['etl','etm','sdms']))
            $folder = $role;
        else $folder = 'read-only';

        if($calon){
            return view('kepegawaian.'.$folder.'.calon_pegawai_detail', compact('calon'));
        }

        return redirect()->route('calon.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $calon = CalonPegawai::find($id);

        if($calon && !$calon->education_acc_status_id){
            $jeniskelamin = JenisKelamin::all();
            $pernikahan = StatusPernikahan::all();
            $provinsi = Wilayah::provinsi()->orderBy('name')->get();
            $kabupaten = Wilayah::kabupatenFilter($calon->alamat->code)->orderBy('name')->get();
            $kecamatan = Wilayah::kecamatanFilter($calon->alamat->code)->orderBy('name')->get();
            $desa = Wilayah::desaFilter($calon->alamat->code)->orderBy('name')->get();
            $pendidikan = PendidikanTerakhir::orderBy('id')->get();
            $latar = LatarBidangStudi::orderBy('name')->get();
            $universitas = Universitas::orderBy('name')->get();
            $penerimaan = StatusPenerimaan::all();
            $unit = Unit::all();
            $jabatan = JabatanUnit::all();
            $status = StatusPegawai::pegawaiAktif()->get();

            return view('kepegawaian.etm.calon_pegawai_ubah', compact('calon','jeniskelamin','pernikahan','provinsi','kabupaten','kecamatan','desa','pendidikan','latar','universitas','penerimaan','unit','jabatan','status'));
        }

        return redirect()->route('calon.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRecommend(Request $request)
    {
        $calon = CalonPegawai::find($request->id);

        if($calon && !$calon->education_acc_status_id){
            $penerimaan = StatusPenerimaan::all();
            $unit = Unit::all();
            $jabatan = JabatanUnit::all();
            $status = StatusPegawai::pegawaiAktif()->get();

            return view('kepegawaian.etl.calon_pegawai_ubah', compact('calon','penerimaan','unit','jabatan','status'));
        }

        return redirect()->route('calon.index');
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
        $calon = CalonPegawai::find($request->id);

        if($calon && !$calon->education_acc_status_id){
            if(in_array($request->user()->role->name,['etm','sdms'])){
                $messages = [
                    'name.required' => 'Mohon tuliskan nama lengkap dan gelar',
                    'nickname.required' => 'Mohon tuliskan nama panggilan',
                    'photo.file' => 'Pastikan foto adalah berkas yang valid',
                    'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 5 MB',
                    'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, atau .png',
                    'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 100x200 px',
                    'nik.required' => 'Mohon tuliskan NIK',
                    'nik.numeric' => 'Pastikan NIK hanya mengandung angka',
                    'npwp.numeric' => 'Pastikan NPWP hanya mengandung angka',
                    'nuptk.numeric' => 'Pastikan NUPTK hanya mengandung angka',
                    'nrg.numeric' => 'Pastikan NRG hanya mengandung angka',
                    'gender.required' => 'Mohon pilih salah satu jenis kelamin',
                    'birth_place.required' => 'Mohon tuliskan tempat lahir',
                    'marriage_status.required' => 'Mohon pilih salah satu status perkawinan',
                    'provinsi.required' => 'Mohon pilih salah satu provinsi',
                    'kabupaten.required' => 'Mohon pilih salah satu kabupaten/kota',
                    'kecamatan.required' => 'Mohon pilih salah satu kecamatan',
                    'desa.required' => 'Mohon pilih salah satu desa/kelurahan',
                    'address.required' => 'Mohon tuliskan alamat',
                    'rt.required' => 'Mohon masukkan RT',
                    'rt.numeric' => 'Pastikan RT hanya mengandung angka',
                    'rw.required' => 'Mohon masukkan RW',
                    'rw.numeric' => 'Pastikan RW hanya mengandung angka',
                    'phone_number.required' => 'Mohon tuliskan nomor seluler',
                    'phone_number.regex' => 'Pastikan nomor seluler hanya mengandung angka',
                    'email.required' => 'Mohon tuliskan alamat email',
                    'recent_education.required' => 'Mohon pilih salah satu pendidikan terakhir',
                    'academic_background.required' => 'Mohon pilih salah satu latar bidang studi',
                    'competency.required' => 'Mohon tuliskan hasil tes kompetensi',
                    'psychological.required' => 'Mohon tuliskan hasil tes psikologi',
                    'acceptance_status.required' => 'Mohon pilih salah satu rekomendasi penerimaan',
                    'unit.required_if' => 'Mohon pilih salah satu unit penempatan yang direkomendasikan',
                    'employee_status.required_if' => 'Mohon pilih salah satu status pegawai',
                    'period_start.required_if' => 'Mohon tentukan awal masa kerja. ',
                    'period_end.required_if' => 'Mohon tentukan akhir masa kerja',
                    'period_end.after' => 'Pastikan akhir masa kerja berbeda dengan awal masa kerja',
                ];

                $this->validate($request, [
                    'name' => 'required',
                    'nickname' => 'required',
                    'photo' => 'file|max:5120|mimes:jpg,jpeg,png|dimensions:min_width=100,min_height=200',
                    'nik' => 'required|numeric',
                    'npwp' => 'nullable|numeric',
                    'nuptk' => 'nullable|numeric',
                    'nrg' => 'nullable|numeric',
                    'gender' => 'required',
                    'birth_place' => 'required',
                    'birthday_year' => 'required|numeric',
                    'birthday_month' => 'required|numeric',
                    'birthday_day' => 'required|numeric',
                    'marriage_status' => 'required',
                    'provinsi' => 'required',
                    'kabupaten' => 'required',
                    'kecamatan' => 'required',
                    'desa' => 'required',
                    'address' => 'required',
                    'rt' => 'required|numeric',
                    'rw' => 'required|numeric',
                    'phone_number' => 'required|regex:/^[0-9]+$/',
                    'email' => 'required',
                    'recent_education' => 'required',
                    'academic_background' => 'required',
                    'competency' => 'required',
                    'psychological' => 'required',
                    'acceptance_status' => 'required',
                    'unit' => 'required_if:acceptance_status,1',
                    'employee_status' => 'required_if:acceptance_status,1',
                    'period_start' => 'required_if:acceptance_status,1|date',
                    'period_end' => 'required_if:acceptance_status,1|date|after:period_start'
                ], $messages);

                $region = Wilayah::where('code',$request->desa)->first();

                if($request->file('photo') && $request->file('photo')->isValid()) {
                // Hapus file foto calon di folder public
                    if(File::exists($calon->photoPath)) File::delete($calon->photoPath);

                // Pindah foto calon ke folder public
                    $file = $request->file('photo');
                    $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
                    $file->move('img/photo/calon/',  $photo);
                }

                $calon->name = $request->name;
                $calon->nickname = $request->nickname;
                $calon->photo = isset($photo) ? $photo : $calon->photo;
                $calon->nik = $request->nik;
                $calon->npwp = isset($request->npwp) ? $request->npwp : $calon->npwp;
                $calon->nuptk = isset($request->nuptk) ? $request->nuptk : null;
                $calon->nrg = isset($request->nrg) ? $request->nrg : null;
                $calon->gender_id = $request->gender;
                $calon->birth_place = $request->birth_place;
                $calon->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
                $calon->marriage_status_id = $request->marriage_status;
                $calon->address = $request->address;
                $calon->rt = $request->rt;
                $calon->rw = $request->rw;
                $calon->region_id = isset($region) ? $region->id : $calon->region_id;
                $calon->phone_number = $request->phone_number;
                $calon->email = $request->email;
                $calon->recent_education_id = $request->recent_education;
                $recent_education = PendidikanTerakhir::find($request->recent_education);
                if(in_array($recent_education->name,["D1","D2","D3","S1","S2","S3"])){
                    $calon->university_id = $request->university;
                }
                else{
                    $calon->university_id = null;
                }
                $calon->academic_background_id = $request->academic_background;
                $calon->competency_test = $request->competency;
                $calon->psychological_test = $request->psychological;
                $calon->acceptance_status_id = $request->acceptance_status;
                if($request->acceptance_status == 1){
                    $calon->unit_id = $request->unit[0];
                    $calon->employee_status_id = $request->employee_status;
                    $calon->period_start = Date::parse($request->period_start);
                    $calon->period_end = Date::parse($request->period_end);
                }
                else{
                    $calon->unit_id = null;
                    $calon->employee_status_id =  null;
                    $calon->period_start = null;
                    $calon->period_end = null;
                }

                $calon->save();

                $calon->fresh();

                if($request->acceptance_status == 1 && count($request->unit) > 0){
                    $calon->units()->sync($request->unit);
                }
                elseif($request->acceptance_status != 1){
                    $calon->units()->detach();
                }
                if($request->acceptance_status == 1 && isset($request->position) && count($request->position) > 0){
                    $calon->jabatans()->sync($request->position);
                }
                elseif($request->acceptance_status != 1 || !isset($request->position) || count($request->position) < 1){
                    $calon->jabatans()->detach();
                }

                Session::flash('success','Data '. $request->name .' berhasil diubah');
            }
            elseif($request->user()->role->name == 'etl'){
                $messages = [
                    'acceptance_status.required' => 'Mohon pilih salah satu rekomendasi penerimaan',
                    'unit.required_if' => 'Mohon pilih salah satu unit penempatan yang direkomendasikan',
                    'employee_status.required_if' => 'Mohon pilih salah satu status pegawai',
                ];

                $this->validate($request, [
                    'acceptance_status' => 'required',
                    'unit' => 'required_if:acceptance_status,1',
                    'employee_status' => 'required_if:acceptance_status,1'
                ], $messages);

                $calon->acceptance_status_id = $request->acceptance_status;
                if($request->acceptance_status == 1){
                    $calon->unit_id = $request->unit[0];
                    $calon->employee_status_id = $request->employee_status;
                    $calon->period_start = Date::parse($request->period_start);
                    $calon->period_end = Date::parse($request->period_end);
                }
                else{
                    $calon->unit_id = null;
                    $calon->employee_status_id =  null;
                    $calon->period_start = null;
                    $calon->period_end = null;
                }

                $calon->save();

                $calon->fresh();

                if($request->acceptance_status == 1 && count($request->unit) > 0){
                    $calon->units()->sync($request->unit);
                }
                elseif($request->acceptance_status != 1){
                    $calon->units()->detach();
                }
                if($request->acceptance_status == 1 && isset($request->position) && count($request->position) > 0){
                    $calon->jabatans()->sync($request->position);
                }
                elseif($request->acceptance_status != 1 || !isset($request->position) || count($request->position) < 1){
                    $calon->jabatans()->detach();
                }

                Session::flash('success','Data rekomendasi '. $calon->name .' berhasil diubah');
            }
        }

        return redirect()->route('calon.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nama = null;
        $calon = CalonPegawai::find($id);

        if($calon){
            $nama = $calon->name;
            if(!$calon->education_acc_status_id){
                // Hapus file foto calon di folder public
                if(File::exists($calon->photoPath)) File::delete($calon->photoPath);

                if($calon->units()->count() > 0) $calon->units()->detach();

                $calon->delete();

                Session::flash('success','Data '. $nama .' berhasil dihapus');
                return redirect()->route('calon.index');
            }
        }

        return redirect()->route('calon.index');
    }

    /**
     * Accept the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request,$id)
    {
        $nama = null;
        $calon = CalonPegawai::find($id);

        if($calon){
            $nama = $calon->name;
            if(!$calon->education_acc_status_id){
                if($calon->rekomendasiPenerimaan->status == "diterima"){

                    if(File::exists($calon->photoPath)){
                        File::copy($calon->photoPath,'img/photo/pegawai/'.$calon->photo);
                        // File::delete($calon->photoPath);
                    }

                    $counter = Setting::where('name','employee_counter')->first();
                    $max_counter = Setting::where('name','max_employee_counter')->first()->value;
                    $employee_counter = ($counter->value)+1;
                    if($employee_counter > $max_counter){
                        $employee_counter = 1;
                        $counter_reset = Setting::where('name','employee_counter_reset')->first();
                        $counter_reset->value++;
                        $counter_reset->save();
                    }

                    $pegawai = new Pegawai();
                    $pegawai->name = $calon->name;
                    $pegawai->nickname = $calon->nickname;
                    $pegawai->photo = isset($calon->photo) ? $calon->photo : null;
                    $pegawai->nip = Date::now('Asia/Jakarta')->format('y').Date::parse($calon->birth_date)->format('dmy').$calon->gender_id.sprintf('%03d',$employee_counter);
                    $pegawai->nik = $calon->nik;
                    $pegawai->npwp = isset($calon->npwp) ? $calon->npwp : null;
                    $pegawai->nuptk = isset($calon->nuptk) ? $calon->nuptk : null;
                    $pegawai->nrg = isset($calon->nrg) ? $calon->nrg : null;
                    $pegawai->gender_id = $calon->gender_id;
                    $pegawai->birth_place = $calon->birth_place;
                    $pegawai->birth_date = $calon->birth_date;
                    $pegawai->marriage_status_id = $calon->marriage_status_id;
                    $pegawai->address = $calon->address;
                    $pegawai->rt = $calon->rt;
                    $pegawai->rw = $calon->rw;
                    $pegawai->region_id = $calon->region_id;
                    $pegawai->phone_number = $calon->phone_number;
                    $pegawai->email = $calon->email;
                    $pegawai->recent_education_id = $calon->recent_education_id;
                    $pegawai->academic_background_id = $calon->academic_background_id;
                    $pegawai->university_id = $calon->university_id;
                    $pegawai->unit_id = $calon->unit_id;
                    $pegawai->employee_status_id = $calon->employee_status_id;
                    $pegawai->join_date = Date::now('Asia/Jakarta');
                    $pegawai->join_badge_status_id = 1;
                    $pegawai->active_status_id = 1;

                    $pegawai->save();

                    $counter->value = $employee_counter;
                    $counter->save();

                    $pegawai = Pegawai::where('nik',$calon->nik)->latest()->first();

                    $direktur = Pegawai::where('position_id','17')->first();

                    $spk = new Spk();
                    $spk->party_1_name = $direktur ? $direktur->name : 'Dr. Kumiasih Mufidayati, M.Si.';
                    $spk->party_1_position = 'Direktur Sekolah Islam Terpadu Auliya';
                    $spk->party_1_address = 'Jalan Raya Jombang No. 1, Jombang, Tangerang Selatan';

                    $spk->employee_name = $pegawai->name;
                    $spk->employee_address = $pegawai->address . ', RT ' . sprintf('%03d',$pegawai->rt) . ' RW ' . sprintf('%03d',$pegawai->rw) . ', ' . $pegawai->alamat->name.', '.$pegawai->alamat->kecamatanName().', '.$pegawai->alamat->kabupatenName().', '.$pegawai->alamat->provinsiName();
                    $spk->employee_status = $pegawai->statusPegawai->status;
                    $spk->period_start = Date::parse($calon->period_start);
                    $spk->period_end = Date::parse($calon->period_end);
                    $spk->status_id = 1;

                    $pegawai->spk()->save($spk);

                    $pegawai->evaluasi()->save(new EvaluasiPegawai());

                    $activeRole = null;

                    if($calon->units()->count() > 0){
                        foreach($calon->units()->pluck('unit_id') as $u){
                            $pegawaiUnit = new PegawaiUnit();
                            $pegawaiUnit->unit_id = $u;

                            $pegawai->units()->save($pegawaiUnit);
                        }
                        if($calon->jabatans()->count() > 0){
                            $tahunAjaran = TahunAjaran::aktif()->latest()->first();
                            foreach($calon->units()->pluck('unit_id') as $u){
                                $penempatan = $calon->jabatans()->where('unit_id',$u)->get();
                                if($penempatan && count($penempatan) > 0){
                                    $pegawaiUnit = $pegawai->units()->where('unit_id',$u)->first();
                                    if($pegawaiUnit){
                                        $accPosition = array();

                                        // Insert into Placement Table
                                        foreach($penempatan as $p){
                                            $penempatanPegawai = PenempatanPegawai::where([
                                                'placement_id' => $p->jabatan->kategoriPenempatan->id,
                                                'academic_year_id' => $tahunAjaran->id,
                                                'unit_id'=> $u
                                            ])->orderBy('created_at')->first();

                                            if(!$penempatanPegawai){
                                                $penempatanPegawai = new PenempatanPegawai();
                                                $penempatanPegawai->academic_year_id = $tahunAjaran->id;
                                                $penempatanPegawai->unit_id = $u;
                                                $penempatanPegawai->placement_id = $p->jabatan->kategoriPenempatan->id;
                                                $penempatanPegawai->status_id = 1;
                                                $penempatanPegawai->save();

                                                $penempatanPegawai->fresh();
                                            }

                                            $detail = new PenempatanPegawaiDetail();
                                            $detail->employee_id = $pegawai->id;
                                            $detail->position_id = $p->jabatan->id;
                                            $detail->period_start = Date::parse($calon->period_start);
                                            $detail->period_end = Date::parse($calon->period_end);
                                            $detail->acc_position_id = $p->jabatan->acc_position_id;
                                            if($p->jabatan->acc_position_id == $request->user()->pegawai->position_id){
                                                $detail->placement_date = Date::parse($calon->period_start);
                                                $detail->acc_employee_id = $request->user()->pegawai->id;
                                                $detail->acc_status_id = 1;
                                                $detail->acc_time = Date::now('Asia/Jakarta');
                                                $accPosition[] = $p->jabatan->id;
                                            }

                                            $penempatanPegawai->detail()->save($detail);
                                        }

                                        // Sync Positions
                                        if(count($accPosition) > 0){
                                            $pegawaiUnit->jabatans()->sync($penempatan->whereIn('position_id',$accPosition)->pluck('position_id'));
                                            if(!$pegawai->position_id){
                                                $activePosition = $calon->jabatans()->where('unit_id',$u)->get()->whereIn('position_id',$accPosition)->first();
                                                if($activePosition){
                                                    $pegawai->position_id = $activePosition->position_id;
                                                    $pegawai->save();
    
                                                    $activePosition = (Object) $activePosition;
                                                    $activeRole = $activePosition->jabatan->role->id;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $user = new LoginUser();
                    $user->username = $pegawai->email;
                    $user->password = bcrypt(Date::parse($pegawai->birth_date)->format('dmY'));
                    $user->user_id = $pegawai->id;
                    $user->role_id = $activeRole ? $activeRole : 37;
                    $user->active_status_id = 1;
                    $user->save();
                }

                $calon->education_acc_id = $request->user()->pegawai->id;
                $calon->education_acc_status_id = 1;
                $calon->education_acc_time = Date::now('Asia/Jakarta');
                $calon->save();

                Session::flash('success','Data '. $nama .' berhasil disetujui');
                return redirect()->route('calon.index');
            }
        }

        return redirect()->route('calon.index');
    }
}
=======
<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use File;
use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Penempatan\PenempatanPegawai;
use App\Models\Penempatan\PenempatanPegawaiDetail;
use App\Models\Rekrutmen\CalonPegawai;
use App\Models\Rekrutmen\CalonPegawaiUnit;
use App\Models\Rekrutmen\EvaluasiPegawai;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiUnit;
use App\Models\Rekrutmen\LatarBidangStudi;
use App\Models\Rekrutmen\PendidikanTerakhir;
use App\Models\Rekrutmen\Spk;
use App\Models\Rekrutmen\StatusPegawai;
use App\Models\Rekrutmen\StatusPenerimaan;
use App\Models\Rekrutmen\StatusPernikahan;
use App\Models\Rekrutmen\Universitas;
use App\Models\JenisKelamin;
use App\Models\LoginUser;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\Wilayah;

class CalonPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek'])){
            $calon = CalonPegawai::whereHas('units',function($query) use($request){
                $query->where('unit_id',$request->user()->pegawai->unit_id);
            })->orderBy('created_at','desc')->get();
        }
        else{
            $calon = CalonPegawai::orderBy('created_at','desc')->get();
        }

        if(in_array($role,['etl','etm','sdms']))
            $folder = $role;
        else $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.calon_pegawai_index', compact('calon'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jeniskelamin = JenisKelamin::all();
        $pernikahan = StatusPernikahan::all();
        $provinsi = Wilayah::provinsi()->orderBy('name')->get();
        $pendidikan = PendidikanTerakhir::orderBy('id')->get();
        $latar = LatarBidangStudi::orderBy('name')->get();
        $universitas = Universitas::orderBy('name')->get();
        $penerimaan = StatusPenerimaan::all();
        $unit = Unit::all();
        $jabatan = JabatanUnit::all();
        $status = StatusPegawai::pegawaiAktif()->get();

        return view('kepegawaian.etm.calon_pegawai_tambah', compact('jeniskelamin','pernikahan','provinsi','pendidikan','latar','universitas','penerimaan','unit','jabatan','status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Mohon tuliskan nama lengkap dan gelar',
            'nickname.required' => 'Mohon tuliskan nama panggilan',
            'photo.file' => 'Pastikan foto adalah berkas yang valid',
            'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 5 MB',
            'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, atau .png',
            'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 100x200 px',
            'nik.required' => 'Mohon tuliskan NIK',
            'nik.numeric' => 'Pastikan NIK hanya mengandung angka',
            'npwp.numeric' => 'Pastikan NPWP hanya mengandung angka',
            'nuptk.numeric' => 'Pastikan NUPTK hanya mengandung angka',
            'nrg.numeric' => 'Pastikan NRG hanya mengandung angka',
            'gender.required' => 'Mohon pilih salah satu jenis kelamin',
            'birth_place.required' => 'Mohon tuliskan tempat lahir',
            'marriage_status.required' => 'Mohon pilih salah satu status perkawinan',
            'provinsi.required' => 'Mohon pilih salah satu provinsi',
            'kabupaten.required' => 'Mohon pilih salah satu kabupaten/kota',
            'kecamatan.required' => 'Mohon pilih salah satu kecamatan',
            'desa.required' => 'Mohon pilih salah satu desa/kelurahan',
            'address.required' => 'Mohon tuliskan alamat',
            'rt.required' => 'Mohon masukkan RT',
            'rt.numeric' => 'Pastikan RT hanya mengandung angka',
            'rw.required' => 'Mohon masukkan RW',
            'rw.numeric' => 'Pastikan RW hanya mengandung angka',
            'phone_number.required' => 'Mohon tuliskan nomor seluler',
            'phone_number.numeric' => 'Pastikan nomor seluler hanya mengandung angka',
            'email.required' => 'Mohon tuliskan alamat email',
            'recent_education.required' => 'Mohon pilih salah satu pendidikan terakhir',
            'academic_background.required' => 'Mohon pilih salah satu latar bidang studi',
            'competency.required' => 'Mohon tuliskan hasil tes kompetensi',
            'psychological.required' => 'Mohon tuliskan hasil tes psikologi',
            'acceptance_status.required' => 'Mohon pilih salah satu rekomendasi penerimaan',
            'unit.required_if' => 'Mohon pilih minimal salah satu unit penempatan yang direkomendasikan',
            'employee_status.required_if' => 'Mohon pilih salah satu status pegawai',
            'period_start.required_if' => 'Mohon tentukan awal masa kerja. ',
            'period_end.required_if' => 'Mohon tentukan akhir masa kerja',
            'period_end.after' => 'Pastikan akhir masa kerja berbeda dengan awal masa kerja',
        ];

        $this->validate($request, [
            'name' => 'required',
            'nickname' => 'required',
            'photo' => 'file|max:5120|mimes:jpg,jpeg,png|dimensions:min_width=100,min_height=200',
            'nik' => 'required|numeric',
            'npwp' => 'nullable|numeric',
            'nuptk' => 'nullable|numeric',
            'nrg' => 'nullable|numeric',
            'gender' => 'required',
            'birth_place' => 'required',
            'birthday_year' => 'required|numeric',
            'birthday_month' => 'required|numeric',
            'birthday_day' => 'required|numeric',
            'marriage_status' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'address' => 'required',
            'rt' => 'required|numeric',
            'rw' => 'required|numeric',
            'phone_number' => 'required|numeric',
            'email' => 'required',
            'recent_education' => 'required',
            'academic_background' => 'required',
            'competency' => 'required',
            'psychological' => 'required',
            'acceptance_status' => 'required',
            'unit' => 'required_if:acceptance_status,1',
            'employee_status' => 'required_if:acceptance_status,1',
            'period_start' => 'required_if:acceptance_status,1|date',
            'period_end' => 'required_if:acceptance_status,1|date|after:period_start'
        ], $messages);

        $region = Wilayah::where('code',$request->desa)->first();

        if($request->file('photo') && $request->file('photo')->isValid()) {
            // Pindah foto calon ke folder public
            $file = $request->file('photo');
            $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
            $file->move('img/photo/calon/',  $photo);
        }

        $calon = new CalonPegawai();
        $calon->name = $request->name;
        $calon->nickname = $request->nickname;
        $calon->photo = isset($photo) ? $photo : null;
        $calon->nik = $request->nik;
        $calon->npwp = isset($request->npwp) ? $request->npwp : null;
        $calon->nuptk = isset($request->nuptk) ? $request->nuptk : null;
        $calon->nrg = isset($request->nrg) ? $request->nrg : null;
        $calon->gender_id = $request->gender;
        $calon->birth_place = $request->birth_place;
        $calon->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
        $calon->marriage_status_id = $request->marriage_status;
        $calon->address = $request->address;
        $calon->rt = $request->rt;
        $calon->rw = $request->rw;
        $calon->region_id = isset($region) ? $region->id : null;
        $calon->phone_number = $request->phone_number;
        $calon->email = $request->email;
        $calon->recent_education_id = $request->recent_education;
        $recent_education = PendidikanTerakhir::find($request->recent_education);
        if(in_array($recent_education->name,["D1","D2","D3","S1","S2","S3"])){
            $calon->university_id = $request->university;
        }
        else{
            $calon->university_id = null;
        }
        $calon->academic_background_id = $request->academic_background;
        $calon->competency_test = $request->competency;
        $calon->psychological_test = $request->psychological;
        $calon->acceptance_status_id = $request->acceptance_status;
        if($request->acceptance_status == 1){
            $calon->unit_id = $request->unit[0];
            $calon->employee_status_id = $request->employee_status;
            $calon->period_start = Date::parse($request->period_start);
            $calon->period_end = Date::parse($request->period_end);
        }
        else{
            $calon->unit_id = null;
            $calon->employee_status_id =  null;
            $calon->period_start =  null;
            $calon->period_end =  null;
        }

        $calon->save();

        $calon->fresh();

        if($request->acceptance_status == 1 && count($request->unit) > 0){
            $calon->units()->attach($request->unit);
        }
        if($request->acceptance_status == 1 && isset($request->position) && count($request->position) > 0){
            $calon->jabatans()->attach($request->position);
        }

        Session::flash('success','Data '. $request->name .' berhasil ditambahkan');
        return redirect()->route('calon.index');
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
        
        if(in_array($role,['kepsek','wakasek'])){
            $calon = CalonPegawai::where('id', $id)->whereHas('units',function($query) use($request){
                $query->where('unit_id',$request->user()->pegawai->unit_id);
            })->first();
        }
        else{
            $calon = CalonPegawai::find($id);
        }

        if(in_array($role,['etl','etm','sdms']))
            $folder = $role;
        else $folder = 'read-only';

        if($calon){
            return view('kepegawaian.'.$folder.'.calon_pegawai_detail', compact('calon'));
        }

        return redirect()->route('calon.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $calon = CalonPegawai::find($id);

        if($calon && !$calon->education_acc_status_id){
            $jeniskelamin = JenisKelamin::all();
            $pernikahan = StatusPernikahan::all();
            $provinsi = Wilayah::provinsi()->orderBy('name')->get();
            $kabupaten = Wilayah::kabupatenFilter($calon->alamat->code)->orderBy('name')->get();
            $kecamatan = Wilayah::kecamatanFilter($calon->alamat->code)->orderBy('name')->get();
            $desa = Wilayah::desaFilter($calon->alamat->code)->orderBy('name')->get();
            $pendidikan = PendidikanTerakhir::orderBy('id')->get();
            $latar = LatarBidangStudi::orderBy('name')->get();
            $universitas = Universitas::orderBy('name')->get();
            $penerimaan = StatusPenerimaan::all();
            $unit = Unit::all();
            $jabatan = JabatanUnit::all();
            $status = StatusPegawai::pegawaiAktif()->get();

            return view('kepegawaian.etm.calon_pegawai_ubah', compact('calon','jeniskelamin','pernikahan','provinsi','kabupaten','kecamatan','desa','pendidikan','latar','universitas','penerimaan','unit','jabatan','status'));
        }

        return redirect()->route('calon.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRecommend(Request $request)
    {
        $calon = CalonPegawai::find($request->id);

        if($calon && !$calon->education_acc_status_id){
            $penerimaan = StatusPenerimaan::all();
            $unit = Unit::all();
            $jabatan = JabatanUnit::all();
            $status = StatusPegawai::pegawaiAktif()->get();

            return view('kepegawaian.etl.calon_pegawai_ubah', compact('calon','penerimaan','unit','jabatan','status'));
        }

        return redirect()->route('calon.index');
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
        $calon = CalonPegawai::find($request->id);

        if($calon && !$calon->education_acc_status_id){
            if(in_array($request->user()->role->name,['etm','sdms'])){
                $messages = [
                    'name.required' => 'Mohon tuliskan nama lengkap dan gelar',
                    'nickname.required' => 'Mohon tuliskan nama panggilan',
                    'photo.file' => 'Pastikan foto adalah berkas yang valid',
                    'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 5 MB',
                    'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, atau .png',
                    'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 100x200 px',
                    'nik.required' => 'Mohon tuliskan NIK',
                    'nik.numeric' => 'Pastikan NIK hanya mengandung angka',
                    'npwp.numeric' => 'Pastikan NPWP hanya mengandung angka',
                    'nuptk.numeric' => 'Pastikan NUPTK hanya mengandung angka',
                    'nrg.numeric' => 'Pastikan NRG hanya mengandung angka',
                    'gender.required' => 'Mohon pilih salah satu jenis kelamin',
                    'birth_place.required' => 'Mohon tuliskan tempat lahir',
                    'marriage_status.required' => 'Mohon pilih salah satu status perkawinan',
                    'provinsi.required' => 'Mohon pilih salah satu provinsi',
                    'kabupaten.required' => 'Mohon pilih salah satu kabupaten/kota',
                    'kecamatan.required' => 'Mohon pilih salah satu kecamatan',
                    'desa.required' => 'Mohon pilih salah satu desa/kelurahan',
                    'address.required' => 'Mohon tuliskan alamat',
                    'rt.required' => 'Mohon masukkan RT',
                    'rt.numeric' => 'Pastikan RT hanya mengandung angka',
                    'rw.required' => 'Mohon masukkan RW',
                    'rw.numeric' => 'Pastikan RW hanya mengandung angka',
                    'phone_number.required' => 'Mohon tuliskan nomor seluler',
                    'phone_number.regex' => 'Pastikan nomor seluler hanya mengandung angka',
                    'email.required' => 'Mohon tuliskan alamat email',
                    'recent_education.required' => 'Mohon pilih salah satu pendidikan terakhir',
                    'academic_background.required' => 'Mohon pilih salah satu latar bidang studi',
                    'competency.required' => 'Mohon tuliskan hasil tes kompetensi',
                    'psychological.required' => 'Mohon tuliskan hasil tes psikologi',
                    'acceptance_status.required' => 'Mohon pilih salah satu rekomendasi penerimaan',
                    'unit.required_if' => 'Mohon pilih salah satu unit penempatan yang direkomendasikan',
                    'employee_status.required_if' => 'Mohon pilih salah satu status pegawai',
                    'period_start.required_if' => 'Mohon tentukan awal masa kerja. ',
                    'period_end.required_if' => 'Mohon tentukan akhir masa kerja',
                    'period_end.after' => 'Pastikan akhir masa kerja berbeda dengan awal masa kerja',
                ];

                $this->validate($request, [
                    'name' => 'required',
                    'nickname' => 'required',
                    'photo' => 'file|max:5120|mimes:jpg,jpeg,png|dimensions:min_width=100,min_height=200',
                    'nik' => 'required|numeric',
                    'npwp' => 'nullable|numeric',
                    'nuptk' => 'nullable|numeric',
                    'nrg' => 'nullable|numeric',
                    'gender' => 'required',
                    'birth_place' => 'required',
                    'birthday_year' => 'required|numeric',
                    'birthday_month' => 'required|numeric',
                    'birthday_day' => 'required|numeric',
                    'marriage_status' => 'required',
                    'provinsi' => 'required',
                    'kabupaten' => 'required',
                    'kecamatan' => 'required',
                    'desa' => 'required',
                    'address' => 'required',
                    'rt' => 'required|numeric',
                    'rw' => 'required|numeric',
                    'phone_number' => 'required|regex:/^[0-9]+$/',
                    'email' => 'required',
                    'recent_education' => 'required',
                    'academic_background' => 'required',
                    'competency' => 'required',
                    'psychological' => 'required',
                    'acceptance_status' => 'required',
                    'unit' => 'required_if:acceptance_status,1',
                    'employee_status' => 'required_if:acceptance_status,1',
                    'period_start' => 'required_if:acceptance_status,1|date',
                    'period_end' => 'required_if:acceptance_status,1|date|after:period_start'
                ], $messages);

                $region = Wilayah::where('code',$request->desa)->first();

                if($request->file('photo') && $request->file('photo')->isValid()) {
                // Hapus file foto calon di folder public
                    if(File::exists($calon->photoPath)) File::delete($calon->photoPath);

                // Pindah foto calon ke folder public
                    $file = $request->file('photo');
                    $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
                    $file->move('img/photo/calon/',  $photo);
                }

                $calon->name = $request->name;
                $calon->nickname = $request->nickname;
                $calon->photo = isset($photo) ? $photo : $calon->photo;
                $calon->nik = $request->nik;
                $calon->npwp = isset($request->npwp) ? $request->npwp : $calon->npwp;
                $calon->nuptk = isset($request->nuptk) ? $request->nuptk : null;
                $calon->nrg = isset($request->nrg) ? $request->nrg : null;
                $calon->gender_id = $request->gender;
                $calon->birth_place = $request->birth_place;
                $calon->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
                $calon->marriage_status_id = $request->marriage_status;
                $calon->address = $request->address;
                $calon->rt = $request->rt;
                $calon->rw = $request->rw;
                $calon->region_id = isset($region) ? $region->id : $calon->region_id;
                $calon->phone_number = $request->phone_number;
                $calon->email = $request->email;
                $calon->recent_education_id = $request->recent_education;
                $recent_education = PendidikanTerakhir::find($request->recent_education);
                if(in_array($recent_education->name,["D1","D2","D3","S1","S2","S3"])){
                    $calon->university_id = $request->university;
                }
                else{
                    $calon->university_id = null;
                }
                $calon->academic_background_id = $request->academic_background;
                $calon->competency_test = $request->competency;
                $calon->psychological_test = $request->psychological;
                $calon->acceptance_status_id = $request->acceptance_status;
                if($request->acceptance_status == 1){
                    $calon->unit_id = $request->unit[0];
                    $calon->employee_status_id = $request->employee_status;
                    $calon->period_start = Date::parse($request->period_start);
                    $calon->period_end = Date::parse($request->period_end);
                }
                else{
                    $calon->unit_id = null;
                    $calon->employee_status_id =  null;
                    $calon->period_start = null;
                    $calon->period_end = null;
                }

                $calon->save();

                $calon->fresh();

                if($request->acceptance_status == 1 && count($request->unit) > 0){
                    $calon->units()->sync($request->unit);
                }
                elseif($request->acceptance_status != 1){
                    $calon->units()->detach();
                }
                if($request->acceptance_status == 1 && isset($request->position) && count($request->position) > 0){
                    $calon->jabatans()->sync($request->position);
                }
                elseif($request->acceptance_status != 1 || !isset($request->position) || count($request->position) < 1){
                    $calon->jabatans()->detach();
                }

                Session::flash('success','Data '. $request->name .' berhasil diubah');
            }
            elseif($request->user()->role->name == 'etl'){
                $messages = [
                    'acceptance_status.required' => 'Mohon pilih salah satu rekomendasi penerimaan',
                    'unit.required_if' => 'Mohon pilih salah satu unit penempatan yang direkomendasikan',
                    'employee_status.required_if' => 'Mohon pilih salah satu status pegawai',
                ];

                $this->validate($request, [
                    'acceptance_status' => 'required',
                    'unit' => 'required_if:acceptance_status,1',
                    'employee_status' => 'required_if:acceptance_status,1'
                ], $messages);

                $calon->acceptance_status_id = $request->acceptance_status;
                if($request->acceptance_status == 1){
                    $calon->unit_id = $request->unit[0];
                    $calon->employee_status_id = $request->employee_status;
                    $calon->period_start = Date::parse($request->period_start);
                    $calon->period_end = Date::parse($request->period_end);
                }
                else{
                    $calon->unit_id = null;
                    $calon->employee_status_id =  null;
                    $calon->period_start = null;
                    $calon->period_end = null;
                }

                $calon->save();

                $calon->fresh();

                if($request->acceptance_status == 1 && count($request->unit) > 0){
                    $calon->units()->sync($request->unit);
                }
                elseif($request->acceptance_status != 1){
                    $calon->units()->detach();
                }
                if($request->acceptance_status == 1 && isset($request->position) && count($request->position) > 0){
                    $calon->jabatans()->sync($request->position);
                }
                elseif($request->acceptance_status != 1 || !isset($request->position) || count($request->position) < 1){
                    $calon->jabatans()->detach();
                }

                Session::flash('success','Data rekomendasi '. $calon->name .' berhasil diubah');
            }
        }

        return redirect()->route('calon.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nama = null;
        $calon = CalonPegawai::find($id);

        if($calon){
            $nama = $calon->name;
            if(!$calon->education_acc_status_id){
                // Hapus file foto calon di folder public
                if(File::exists($calon->photoPath)) File::delete($calon->photoPath);

                if($calon->units()->count() > 0) $calon->units()->detach();

                $calon->delete();

                Session::flash('success','Data '. $nama .' berhasil dihapus');
                return redirect()->route('calon.index');
            }
        }

        return redirect()->route('calon.index');
    }

    /**
     * Accept the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request,$id)
    {
        $nama = null;
        $calon = CalonPegawai::find($id);

        if($calon){
            $nama = $calon->name;
            if(!$calon->education_acc_status_id){
                if($calon->rekomendasiPenerimaan->status == "diterima"){

                    if(File::exists($calon->photoPath)){
                        File::copy($calon->photoPath,'img/photo/pegawai/'.$calon->photo);
                        // File::delete($calon->photoPath);
                    }

                    $counter = Setting::where('name','employee_counter')->first();
                    $max_counter = Setting::where('name','max_employee_counter')->first()->value;
                    $employee_counter = ($counter->value)+1;
                    if($employee_counter > $max_counter){
                        $employee_counter = 1;
                        $counter_reset = Setting::where('name','employee_counter_reset')->first();
                        $counter_reset->value++;
                        $counter_reset->save();
                    }

                    $pegawai = new Pegawai();
                    $pegawai->name = $calon->name;
                    $pegawai->nickname = $calon->nickname;
                    $pegawai->photo = isset($calon->photo) ? $calon->photo : null;
                    $pegawai->nip = Date::now('Asia/Jakarta')->format('y').Date::parse($calon->birth_date)->format('dmy').$calon->gender_id.sprintf('%03d',$employee_counter);
                    $pegawai->nik = $calon->nik;
                    $pegawai->npwp = isset($calon->npwp) ? $calon->npwp : null;
                    $pegawai->nuptk = isset($calon->nuptk) ? $calon->nuptk : null;
                    $pegawai->nrg = isset($calon->nrg) ? $calon->nrg : null;
                    $pegawai->gender_id = $calon->gender_id;
                    $pegawai->birth_place = $calon->birth_place;
                    $pegawai->birth_date = $calon->birth_date;
                    $pegawai->marriage_status_id = $calon->marriage_status_id;
                    $pegawai->address = $calon->address;
                    $pegawai->rt = $calon->rt;
                    $pegawai->rw = $calon->rw;
                    $pegawai->region_id = $calon->region_id;
                    $pegawai->phone_number = $calon->phone_number;
                    $pegawai->email = $calon->email;
                    $pegawai->recent_education_id = $calon->recent_education_id;
                    $pegawai->academic_background_id = $calon->academic_background_id;
                    $pegawai->university_id = $calon->university_id;
                    $pegawai->unit_id = $calon->unit_id;
                    $pegawai->employee_status_id = $calon->employee_status_id;
                    $pegawai->join_date = Date::now('Asia/Jakarta');
                    $pegawai->join_badge_status_id = 1;
                    $pegawai->active_status_id = 1;

                    $pegawai->save();

                    $counter->value = $employee_counter;
                    $counter->save();

                    $pegawai = Pegawai::where('nik',$calon->nik)->latest()->first();

                    $direktur = Pegawai::where('position_id','17')->first();

                    $spk = new Spk();
                    $spk->party_1_name = $direktur ? $direktur->name : 'Dr. Kumiasih Mufidayati, M.Si.';
                    $spk->party_1_position = 'Direktur Sekolah Islam Terpadu Auliya';
                    $spk->party_1_address = 'Jalan Raya Jombang No. 1, Jombang, Tangerang Selatan';

                    $spk->employee_name = $pegawai->name;
                    $spk->employee_address = $pegawai->address . ', RT ' . sprintf('%03d',$pegawai->rt) . ' RW ' . sprintf('%03d',$pegawai->rw) . ', ' . $pegawai->alamat->name.', '.$pegawai->alamat->kecamatanName().', '.$pegawai->alamat->kabupatenName().', '.$pegawai->alamat->provinsiName();
                    $spk->employee_status = $pegawai->statusPegawai->status;
                    $spk->period_start = Date::parse($calon->period_start);
                    $spk->period_end = Date::parse($calon->period_end);
                    $spk->status_id = 1;

                    $pegawai->spk()->save($spk);

                    $pegawai->evaluasi()->save(new EvaluasiPegawai());

                    $activeRole = null;

                    if($calon->units()->count() > 0){
                        foreach($calon->units()->pluck('unit_id') as $u){
                            $pegawaiUnit = new PegawaiUnit();
                            $pegawaiUnit->unit_id = $u;

                            $pegawai->units()->save($pegawaiUnit);
                        }
                        if($calon->jabatans()->count() > 0){
                            $tahunAjaran = TahunAjaran::aktif()->latest()->first();
                            foreach($calon->units()->pluck('unit_id') as $u){
                                $penempatan = $calon->jabatans()->where('unit_id',$u)->get();
                                if($penempatan && count($penempatan) > 0){
                                    $pegawaiUnit = $pegawai->units()->where('unit_id',$u)->first();
                                    if($pegawaiUnit){
                                        $accPosition = array();

                                        // Insert into Placement Table
                                        foreach($penempatan as $p){
                                            $penempatanPegawai = PenempatanPegawai::where([
                                                'placement_id' => $p->jabatan->kategoriPenempatan->id,
                                                'academic_year_id' => $tahunAjaran->id,
                                                'unit_id'=> $u
                                            ])->orderBy('created_at')->first();

                                            if(!$penempatanPegawai){
                                                $penempatanPegawai = new PenempatanPegawai();
                                                $penempatanPegawai->academic_year_id = $tahunAjaran->id;
                                                $penempatanPegawai->unit_id = $u;
                                                $penempatanPegawai->placement_id = $p->jabatan->kategoriPenempatan->id;
                                                $penempatanPegawai->status_id = 1;
                                                $penempatanPegawai->save();

                                                $penempatanPegawai->fresh();
                                            }

                                            $detail = new PenempatanPegawaiDetail();
                                            $detail->employee_id = $pegawai->id;
                                            $detail->position_id = $p->jabatan->id;
                                            $detail->period_start = Date::parse($calon->period_start);
                                            $detail->period_end = Date::parse($calon->period_end);
                                            $detail->acc_position_id = $p->jabatan->acc_position_id;
                                            if($p->jabatan->acc_position_id == $request->user()->pegawai->position_id){
                                                $detail->placement_date = Date::parse($calon->period_start);
                                                $detail->acc_employee_id = $request->user()->pegawai->id;
                                                $detail->acc_status_id = 1;
                                                $detail->acc_time = Date::now('Asia/Jakarta');
                                                $accPosition[] = $p->jabatan->id;
                                            }

                                            $penempatanPegawai->detail()->save($detail);
                                        }

                                        // Sync Positions
                                        if(count($accPosition) > 0){
                                            $pegawaiUnit->jabatans()->sync($penempatan->whereIn('position_id',$accPosition)->pluck('position_id'));
                                            if(!$pegawai->position_id){
                                                $activePosition = $calon->jabatans()->where('unit_id',$u)->get()->whereIn('position_id',$accPosition)->first();
                                                if($activePosition){
                                                    $pegawai->position_id = $activePosition->position_id;
                                                    $pegawai->save();
    
                                                    $activePosition = (Object) $activePosition;
                                                    $activeRole = $activePosition->jabatan->role->id;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $user = new LoginUser();
                    $user->username = $pegawai->email;
                    $user->password = bcrypt(Date::parse($pegawai->birth_date)->format('dmY'));
                    $user->user_id = $pegawai->id;
                    $user->role_id = $activeRole ? $activeRole : 37;
                    $user->active_status_id = 1;
                    $user->save();
                }

                $calon->education_acc_id = $request->user()->pegawai->id;
                $calon->education_acc_status_id = 1;
                $calon->education_acc_time = Date::now('Asia/Jakarta');
                $calon->save();

                Session::flash('success','Data '. $nama .' berhasil disetujui');
                return redirect()->route('calon.index');
            }
        }

        return redirect()->route('calon.index');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

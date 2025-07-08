<?php

namespace Modules\HR\Http\Controllers\EmployeeManagement\Recruitment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use File;
use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Imports\PegawaiImport;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Penempatan\PenempatanPegawaiDetail;
use Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee;
use Modules\HR\Models\EmployeeManagement\Evaluation\EmployeeEvaluation;
use Modules\HR\Models\EmployeeManagement\References\EmployeeCategory;
use Modules\HR\Models\EmployeeManagement\References\AcademicBackground;
use Modules\HR\Models\EmployeeManagement\Recruitment\Employee;
use Modules\HR\Models\EmployeeManagement\Recruitment\EmployeeUnit;
use Modules\HR\Models\EmployeeManagement\Recruitment\EmployeeUnitPosition;
use Modules\Core\Models\References\EducationLevel;
use Modules\HR\Models\EmployeeManagement\References\EmployeeStatus;
use Modules\Core\Models\References\University;
use Modules\Core\Models\References\Gender;
use App\Models\User;
use App\Models\Setting;
use Modules\Core\Models\References\Status;
use Modules\Core\Models\Unit;
use Modules\Core\Models\References\Region;

use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        // Setttings
        $importable = false;
        $exportable = in_array($role,['etl','aspv']);
        $filterable = in_array($role,['admin','pembinayys','ketuayys','direktur','etl','etm','am','aspv']);
        $viewYayasan = in_array($role,['admin','pembinayys','ketuayys','direktur','etl','etm','fam','am']);

        $category = isset($request->category) ? $request->category : null;

        $checkCategory = null;
        if($category){
            $checkCategory = EmployeeCategory::where('name',ucwords($category));
            if(!$viewYayasan)
                $checkCategory = $checkCategory->where('name','!=','Yayasan');
            $checkCategory = $checkCategory->first();
        }
            
        if($category && !$checkCategory) return redirect()->route('pegawai.index');

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = User::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');

        $filterJabatan = null;

        if(in_array($role,['kepsek','wakasek'])){
            $pegawai = Employee::select('id','name','photo','nip','birth_place','birth_date','join_date','employee_status_id','join_badge_status_id','disjoin_badge_status_id','active_status_id')->whereHas('units',function($query)use($request){
                $query->where('unit_id',$request->user()->pegawai->unit_id);
            })->orderBy('created_at','desc');
        }
        else{
            if($filterable && isset($request->jabatan)){
                $filterJabatan = JabatanUnit::select('id','unit_id','position_id')->whereIn('id',$request->jabatan)->get();
                $pegawai = null;
            }
            else{
                $pegawai = Employee::select('id','name','photo','nip','birth_place','birth_date','join_date','employee_status_id','join_badge_status_id','disjoin_badge_status_id','active_status_id')->orderBy('created_at','desc');
            }
        }
        $status = 'aktif';

        if(isset($request->status) && $request->status != 'aktif'){
            $checkStatus = Status::where('category', 'active_status')->where('code',$request->status)->count();
            if($checkStatus > 0) $status = $request->status;
        }

        if(!$viewYayasan && $pegawai){
            $pegawai = $pegawai->where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai));
        }

        if($pegawai){
            $pegawai = $pegawai->whereHas('activeStatus',function($q) use ($status){
                $q->where('name',$status);
            });
            if($category){
                $pegawai = $pegawai->whereHas('employeeStatus.employeeCategory',function($q)use($category){
                    $q->where('name',$category);
                });
            }
            $pegawai = $pegawai->get();
        }

        if($filterable){
            $unit = Unit::all();

            if(!$viewYayasan) $jabatan = JabatanUnit::whereNotIn('position_id',['15','16','17'])->get();
            else{
                $jabatan = JabatanUnit::all();
            }
            
            if($filterJabatan){
                $unit = $filterJabatan->pluck('unit_id')->unique();
                $i = 1;
                foreach($unit as $u){
                    $posisi = $filterJabatan->where('unit_id',$u)->pluck('position_id');
                    $pegawais = Employee::select('id','name','photo','nip','birth_place','birth_date','join_date','employee_status_id','join_badge_status_id','disjoin_badge_status_id','active_status_id')->whereHas('units',function($q)use($u,$posisi){
                        $q->where('unit_id',$u)->whereHas('positions',function($q)use($posisi){
                            $q->whereIn('position_id',$posisi);
                        });
                    })->whereHas('activeStatus',function($q) use ($status){
                        $q->where('name',$status);
                    });
                    
                    if($role != 'admin'){
                        if($viewYayasan){
                            $pegawais = $pegawais->where('nip','!=','0')->whereNotIn('id',$nonpegawai);
                        }
                        else{
                            $pegawais = $pegawais->where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai));
                        }
                    }
                    
                    $pegawais = $pegawais->orderBy('created_at','desc')->get();
                    
                    if($i == 1)
                        $pegawaiFiltered = $pegawais;
                    else
                        $pegawaiFiltered = $pegawaiFiltered->concat($pegawais);

                    $i++;
                }

                $pegawai = $pegawaiFiltered->unique()->all();
            }
        }
        else{
            $unit = $jabatan = null;
        }

        if(in_array($role,['admin','etm','faspv','aspv']))
            $folder = $role;
        else $folder = 'read-only';
        // Override
        $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.pegawai_index', compact('role','importable','exportable','filterable','category','pegawai','status','unit','jabatan','filterJabatan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jeniskelamin = Gender::all();
        $pernikahan = MarriageStatus::all();
        $provinsi = Region::provinsi()->orderBy('name')->get();
        $pendidikan = EducationLevel::orderBy('id')->get();
        $latar = AcademicBackground::orderBy('name')->get();
        $universitas = University::orderBy('name')->get();
        $unit = Unit::all();
        $status = EmployeeStatus::active()->get();

        return view('kepegawaian.etm.pegawai_tambah', compact('jeniskelamin','pernikahan','provinsi','pendidikan','latar','universitas','unit','status'));
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
            'unit.required' => 'Mohon pilih salah satu unit pegawai',
            'position.required' => 'Mohon pilih salah satu jabatan pegawai',
            'employee_status.required' => 'Mohon pilih salah satu status pegawai',
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
            'unit' => 'required',
            'position' => 'required',
            'employee_status' => 'required',
        ], $messages);

        $region = Region::where('code',$request->desa)->first();
        $position = Jabatan::where('code',$request->position)->first();

        if($request->file('photo') && $request->file('photo')->isValid()) {
            // Pindah foto calon ke folder public
            $file = $request->file('photo');
            $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
            $file->move('img/photo/employee/',  $photo);
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

        $pegawai = new Employee();
        $pegawai->name = $request->name;
        $pegawai->nickname = $request->nickname;
        $pegawai->photo = isset($photo) ? $photo : null;
        $pegawai->nip = Date::now('Asia/Jakarta')->format('y').Date::parse($pegawai->birth_date)->format('dmy').$request->gender_id.sprintf('%03d',$employee_counter);
        $pegawai->nik = $request->nik;
        $pegawai->npwp = isset($request->npwp) ? $request->npwp : null;
        $pegawai->gender_id = $request->gender;
        $pegawai->birth_place = $request->birth_place;
        $pegawai->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
        $pegawai->marital_status_id = $request->marriage_status;
        $pegawai->address = $request->address;
        $pegawai->rt = $request->rt;
        $pegawai->rw = $request->rw;
        $pegawai->region_id = isset($region) ? $region->id : null;
        $pegawai->phone_number = $request->phone_number;
        $pegawai->email = $request->email;
        $pegawai->education_level_id = $request->recent_education;
        $pegawai->academic_background_id = $request->academic_background;
        $pegawai->university_id = isset($request->university) ? $request->university : null;
        $pegawai->unit_id = $request->unit;
        $pegawai->position_id = isset($position) ? $position->id : null;
        $pegawai->employee_status_id = $request->employee_status;
        $pegawai->join_date = Date::now('Asia/Jakarta');
        $pegawai->active_status_id = 1;

        $pegawai->save();

        $counter->value = $employee_counter;
        $counter->save();

        $pegawai = Employee::where('nik',$request->nik)->latest()->first();

        if($request->employee_status == '3' || $request->employee_status == '4'){
            $pegawai->evaluations()->save(new EmployeeEvaluation());
        }

        $user = new User();
        $user->username = $pegawai->nip;
        $user->password = bcrypt(Date::parse($pegawai->birth_date)->format('dmY'));
        $user->user_id = $pegawai->id;
        $user->role_id = $position->role->id;
        $user->active_status_id = 1;
        $user->save();

        Session::flash('success','Data '. $request->name .' berhasil ditambahkan');
        return redirect()->route('pegawai.index');
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
        
        // Setttings
        $viewYayasan = in_array($role,['admin','pembinayys','ketuayys','direktur','etl','etm','fam','am']);

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = User::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');
        
        if(in_array($role,['kepsek','wakasek'])){
            $pegawai = Employee::where('id',$id)->whereHas('units',function($query) use($request){
                    $query->where('unit_id',$request->user()->pegawai->unit_id);
                })->where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai))->first();
        }
        else{
            if($role == 'admin'){
                $pegawai = Employee::find($id);
            }
            elseif($viewYayasan){
                $pegawai = Employee::where('id',$id)->where('nip','!=','0')->whereNotIn('id',$nonpegawai)->first();
            }
            else{
                $pegawai = Employee::where('id',$id)->where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai))->first();
            }
        }

        if(in_array($role,['pembinayys','ketuayys','kepsek','etl','etm','faspv']))
            $folder = $role;
        else $folder = 'read-only';

        if($pegawai){
            if(in_array($role,['wakasek','direktur']))
                return view('kepegawaian.'.$folder.'.pegawai_detail_pelatihan', compact('pegawai'));
            else
                return view('kepegawaian.'.$folder.'.pegawai_detail', compact('pegawai'));
        }

        return redirect()->route('pegawai.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $role = $request->user()->role->name;
        
        // Setttings
        $viewYayasan = in_array($role,['admin','pembinayys','ketuayys','direktur','etl','etm','fam','am']);

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = User::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');
        
        if($role == 'admin'){
            $pegawai = Employee::find($id);
        }
        elseif($viewYayasan){
            $pegawai = Employee::where('id',$id)->where('nip','!=','0')->whereNotIn('id',$nonpegawai)->first();
        }
        else{
            $pegawai = Employee::where('id',$id)->where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai))->first();
        }

        if($pegawai){
            $jeniskelamin = JenisKelamin::all();
            $pernikahan = StatusPernikahan::all();
            $provinsi = Wilayah::provinsi()->orderBy('name')->get();
            $kabupaten = Wilayah::kabupatenFilter($pegawai->alamat->code)->orderBy('name')->get();
            $kecamatan = Wilayah::kecamatanFilter($pegawai->alamat->code)->orderBy('name')->get();
            $desa = Wilayah::desaFilter($pegawai->alamat->code)->orderBy('name')->get();
            $pendidikan = PendidikanTerakhir::orderBy('id')->get();
            $latar = LatarBidangStudi::orderBy('name')->get();
            $universitas = Universitas::orderBy('name')->get();
            $penerimaan = StatusPenerimaan::all();
            $unit = Unit::all();
            $status = StatusPegawai::statusCalonPegawai()->get();

            return view('kepegawaian.etm.pegawai_ubah', compact('pegawai','jeniskelamin','pernikahan','provinsi','kabupaten','kecamatan','desa','pendidikan','latar','universitas','penerimaan','unit','status'));
        }

        return redirect()->route('pegawai.index');
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
        $role = $request->user()->role->name;
        $id = $request->id;
        
        // Setttings
        $viewYayasan = in_array($role,['admin','pembinayys','ketuayys','direktur','etl','etm','fam','am']);

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = User::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');
        
        if($role == 'admin'){
            $pegawai = Employee::find($id);
        }
        elseif($viewYayasan){
            $pegawai = Employee::where('id',$id)->where('nip','!=','0')->whereNotIn('id',$nonpegawai)->first();
        }
        else{
            $pegawai = Employee::where('id',$id)->where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai))->first();
        }

        if($pegawai){
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
                'unit.required' => 'Mohon pilih minimal salah satu unit penempatan yang direkomendasikan',
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
                'unit' => 'required',
            ], $messages);

            if($pegawai->employee_status_id != 1){
                $messages = [
                    'employee_status.required' => 'Mohon pilih salah satu status pegawai',
                ];

                $this->validate($request, [
                    'employee_status' => 'required'
                ], $messages);
            }

            $region = Wilayah::where('code',$request->desa)->first();

            if($request->file('photo') && $request->file('photo')->isValid()) {
                // Hapus file foto pegawai di folder public
                if(File::exists($pegawai->photoPath)) File::delete($pegawai->photoPath);

                // Pindah foto pegawai ke folder public
                $file = $request->file('photo');
                $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
                $file->move('img/photo/employee/',  $photo);
            }

            $pegawai->name = $request->name;
            $pegawai->nickname = $request->nickname;
            $pegawai->photo = isset($photo) ? $photo : $pegawai->photo;
            $pegawai->nik = $request->nik;
            $pegawai->npwp = isset($request->npwp) ? $request->npwp : null;
            $pegawai->nuptk = isset($request->nuptk) ? $request->nuptk : null;
            $pegawai->nrg = isset($request->nrg) ? $request->nrg : null;
            $pegawai->gender_id = $request->gender;
            $pegawai->birth_place = $request->birth_place;
            $pegawai->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
            $pegawai->marriage_status_id = $request->marriage_status;
            $pegawai->address = $request->address;
            $pegawai->rt = $request->rt;
            $pegawai->rw = $request->rw;
            $pegawai->region_id = isset($region) ? $region->id : $pegawai->region_id;
            $pegawai->phone_number = $request->phone_number;
            $pegawai->email = $request->email;
            $pegawai->recent_education_id = $request->recent_education;
            $pegawai->academic_background_id = $request->academic_background;
            $pegawai->university_id = isset($request->university) ? $request->university : null;
            if($pegawai->employee_status_id != 1) $pegawai->employee_status_id = $request->employee_status;

            // Unit changes detector
            $diff = $add = null;
            if($pegawai->units()->count() > 0){
                if($pegawai->units()->pluck('unit_id')->toArray() != $request->unit){
                    // Bisa berkurang, bisa bertambah
                    $diff = array_diff($pegawai->units()->pluck('unit_id')->toArray(), $request->unit);
                    $add = array_diff($request->unit, $pegawai->units()->pluck('unit_id')->toArray());
                }
            }else{
                $add = $request->unit;
            }

            if($diff){
                $tahunAktif = TahunAjaran::select('id')->where('is_active',1)->latest()->first();
                $semesterAktif = $tahunAktif->semester()->where('is_active',1)->latest()->first();
                $user = $pegawai->login;
                if(in_array($pegawai->unit_id,$pegawai->units()->pluck('unit_id')->toArray())){
                    $pegawai->unit_id = null;
                }
                foreach($diff as $u){
                    $pegawaiUnit = $pegawai->units()->where('unit_id',$u)->first();
                    // Remove relations

                    // Detail Penempatan
                    $pegawai->penempatan()->whereHas('penempatanPegawai',function($q)use($tahunAktif,$pegawaiUnit){
                        $q->where([
                            'academic_year_id' => $tahunAktif->id,
                            'unit_id' => $pegawaiUnit->unit_id
                        ]);
                    })->whereNull('acc_status_id')->delete();

                    // Detail SKBM
                    $pegawai->skbmDetail()->whereHas('skbm',function($q)use($tahunAktif,$pegawaiUnit){
                        $q->where([
                            'academic_year_id' => $tahunAktif->id,
                            'unit_id' => $pegawaiUnit->unit_id
                        ]);
                    })->delete();

                    // Nilai PSC
                    $pscScores = $pegawai->pscScore()->where('unit_id', $pegawaiUnit->unit_id)->whereNull('acc_status_id')->get();
                    foreach($pscScores as $p){
                        foreach($p->detail as $d){
                            $d->penilai()->delete();
                        }
                        $p->detail()->delete();
                    }
                    $pegawai->pscScore()->where('unit_id', $pegawaiUnit->unit_id)->whereNull('acc_status_id')->delete();

                    // Jadwal Pelajaran
                    $pegawai->jadwalPelajarans()->where('semester_id',$semesterAktif->id)->whereIn('level_id',$pegawaiUnit->unit->levels()->select('id')->get()->pluck('id')->toArray())->delete();

                    // Wali Kelas
                    $pegawai->kelas()->where([
                        'academic_year_id' => $tahunAktif->id,
                        'unit_id' => $pegawaiUnit->unit_id
                    ])->where('status','!=',3)->update([
                        'teacher_id' => null
                    ]);

                    if(in_array($pegawai->position_id,$pegawaiUnit->jabatans->pluck('id')->toArray())){
                        $pegawai->position_id = null;
                        $pegawai->save();
                        $user->role_id = 37;
                        $user->save();
                    }
                    
                    $pegawaiUnit->jabatans()->detach();

                    // Substitute or remove
                    if($add && count($add) > 0){
                        $pegawaiUnit->unit_id = array_shift($add);
                        $pegawaiUnit->save();
                    }
                    else{
                        $pegawaiUnit->delete();
                    }
                }
                $firstUnit = $pegawai->units()->has('jabatans')->first();
                $firstPosition = null;
                if($firstUnit) $firstPosition = $firstUnit->jabatans()->first();
                else $firstUnit = $pegawai->units()->first();
                if(!$pegawai->unit_id){
                    $pegawai->unit_id = $firstUnit->unit_id;
                    $pegawai->save();
                }
                if(!$pegawai->position_id && $firstPosition){
                    $pegawai->position_id = $firstPosition->id;
                    $pegawai->save();
                    $user->role_id = $firstPosition->role_id;
                    $user->save();
                }
            }
            if($add && count($add) > 0){
                foreach($add as $u){
                    $pegawaiUnit = new PegawaiUnit();
                    $pegawaiUnit->unit_id = $u;

                    $pegawai->units()->save($pegawaiUnit);
                }
            }

            $pegawai->save();
            
            $pegawai->fresh();
            
            if($pegawai->login){
                $user = $pegawai->login;
                $user->username = $pegawai->email;
                $user->save();
            }

            Session::flash('success','Data '. $request->name .' berhasil diubah');
            return redirect()->route('pegawai.index');
        }

        return redirect()->route('pegawai.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*
    public function destroy($id)
    {
        $nama = null;
        $pegawai = Pegawai::find($id);

        if($pegawai){
            $nama = $pegawai->name;
            if(!$pegawai->education_acc_status_id){
                // Hapus file foto calon di folder public
                if(File::exists($pegawai->photoPath)) File::delete($pegawai->photoPath);

                $pegawai->delete();

                Session::flash('success','Data '. $nama .' berhasil dihapus');
                return redirect()->route('pegawai.index');
            }
        }

        return redirect()->route('pegawai.index');
    }*/

    /**
     * Accept the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept($id)
    {
        $nama = null;
        $status = null;
        $pegawai = Employee::find($id);

        if($pegawai){
            $nama = $pegawai->name;

            if($pegawai->join_badge_status_id == 1 && !$pegawai->disjoin_badge_status_id){
                $pegawai->join_badge_status_id = 2;
                $status = 'baru';
            }
            elseif($pegawai->disjoin_badge_status_id == 1){
                $pegawai->disjoin_badge_status_id = 2;
                $status = 'phk';
            }

            $pegawai->save();

            Session::flash('success','Data '. $nama .' berhasil dikonfirmasi');
            if($status == 'phk') return redirect()->route('pegawai.index',['status' => 'nonaktif']);
        }

        return redirect()->route('pegawai.index');
    }

    /**
     * Reset password the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reset($id){
        $pegawai = Employee::find($id);

        if($pegawai){
            $user = $pegawai->login;
            $user->password = bcrypt(Date::parse($pegawai->birth_date)->format('dmY'));
            $user->save();

            Session::flash('success','Sandi '.$pegawai->name.' berhasil di-reset ke pengaturan awal');
        }
        else Session::flash('danger','Akun tidak ditemukan. Sandi gagal di-reset.');

        return redirect()->route('pegawai.index');
    }

    /**
     * Import the resources to storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request) 
    {
        if($request->file('excel') && $request->file('excel')->isValid()) {
            Excel::import(new PegawaiImport, $request->file('excel'));
        }
        
        Session::flash('success','Data berhasil diimpor');
        return redirect()->route('pegawai.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = User::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');
        
        $category = isset($request->category) ? $request->category : null;
        
        $checkCategory = null;
        if($category){
            $checkCategory = EmployeeCategory::where('name',ucwords($category))->first();
            if(!$checkCategory) return redirect()->route('pegawai.index');
        }

        $pegawai = Employee::where('nip','!=','0')->whereNotIn('id',$pejabat->concat($nonpegawai))->aktif()->orderBy('name','asc');
        if($category){
            $pegawai = $pegawai->whereHas('statusPegawai.kategori',function($q)use($category){
                $q->where('name',$category);
            });
        }
        $pegawai = $pegawai->get();

        $spreadsheet = new Spreadsheet;

        $spreadsheet->getProperties()->setCreator('SIT Auliya')
        ->setLastModifiedBy($request->user()->pegawai->name)
        ->setTitle("Data Induk ".($category ? ucwords($category) : 'Civitas')." Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setSubject(($category ? ucwords($category) : 'Civitas')." Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setDescription("Rekapitulasi Data Induk ".($category ? ucwords($category) : 'Civitas')." Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setKeywords("Data, Induk, ".($category ? ucwords($category) : 'Civitas').", Auliya");

        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'No')
        ->setCellValue('B1', 'Nama '.($category && $category == 'mitra' ? ucwords($category) : 'Pegawai'))
        ->setCellValue('C1', 'Nama Panggilan')
        ->setCellValue('D1', 'Nomor Induk '.($category && $category == 'mitra' ? ucwords($category) : 'Pegawai').' Yayasan')
        ->setCellValue('E1', 'Nomor Induk Kependudukan')
        ->setCellValue('F1', 'NPWP')
        ->setCellValue('G1', 'NUPTK')
        ->setCellValue('H1', 'NRG')
        ->setCellValue('I1', 'L/P')
        ->setCellValue('J1', 'Tempat Lahir')
        ->setCellValue('K1', 'Tanggal Lahir')
        ->setCellValue('L1', 'Status Pernikahan')
        ->setCellValue('M1', 'Alamat')
        ->setCellValue('N1', 'RT')
        ->setCellValue('O1', 'RW')
        ->setCellValue('P1', 'Wilayah')
        ->setCellValue('Q1', 'No HP')
        ->setCellValue('R1', 'Email')
        ->setCellValue('S1', 'Pendidikan Terakhir')
        ->setCellValue('T1', 'Program Studi')
        ->setCellValue('U1', 'Universitas')
        ->setCellValue('V1', 'Unit')
        ->setCellValue('W1', 'Penempatan')
        ->setCellValue('X1', 'Status Kepegawaian')
        ->setCellValue('Y1', 'Tahun Masuk Auliya')
        ->setCellValue('Z1', 'Tahun Diangkat Pegawai Tetap');

        $kolom = 2;
        $no = 1;
        $max_kolom = count($pegawai)+1;
        foreach($pegawai as $p) {
            $promotion_date = $p->employee_status_id == 1 && ($p->tetap && $p->tetap->promotion_date) ?Date::parse($p->tetap->promotion_date)->format('Y-m-d') : null;

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$kolom, $no++)
            ->setCellValue('B'.$kolom, $p->name)
            ->setCellValue('C'.$kolom, $p->nickname)
            ->setCellValueExplicit('D'.$kolom, $p->nip ? strval($p->nip) : '', DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$kolom, $p->nik ? strval($p->nik) : '', DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$kolom, $p->npwp ? strval($p->npwp) : '', DataType::TYPE_STRING)
            ->setCellValueExplicit('G'.$kolom, $p->nuptk ? strval($p->nuptk) : '', DataType::TYPE_STRING)
            ->setCellValueExplicit('H'.$kolom, $p->nrg ? strval($p->nrg) : '', DataType::TYPE_STRING)
            ->setCellValue('I'.$kolom, $p->jenisKelamin ? ucwords($p->jenisKelamin->name[0]) : '')
            ->setCellValue('J'.$kolom, $p->birth_place)
            ->setCellValue('K'.$kolom, Date::parse($p->birth_date)->format('Y-m-d'))
            ->setCellValue('L'.$kolom, ucwords($p->statusPernikahan->status))
            ->setCellValue('M'.$kolom, $p->address)
            ->setCellValue('N'.$kolom, $p->rt)
            ->setCellValue('O'.$kolom, $p->rw)
            ->setCellValue('P'.$kolom, $p->alamat->name.', '.$p->alamat->kecamatanName().', '.$p->alamat->kabupatenName().', '.$p->alamat->provinsiName())
            ->setCellValue('Q'.$kolom, "".$p->phone_number)
            ->setCellValue('R'.$kolom, $p->email)
            ->setCellValue('S'.$kolom, $p->pendidikanTerakhir ? $p->pendidikanTerakhir->name : '')
            ->setCellValue('T'.$kolom, $p->latarBidangStudi ? $p->latarBidangStudi->name : '')
            ->setCellValue('U'.$kolom, $p->universitas ? $p->universitas->name : '')
            ->setCellValue('V'.$kolom, $p->units()->count() > 0 ? implode(', ',$p->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('name')->toArray()) : '')
            ->setCellValue('W'.$kolom, $p->units()->count() > 0 ? implode(', ',$p->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '')
            ->setCellValue('X'.$kolom, $p->statusPegawai ? $p->statusPegawai->status : '')
            ->setCellValue('Y'.$kolom, Date::parse($p->join_date)->format('Y-m-d'))
            ->setCellValue('Z'.$kolom, $promotion_date ? $promotion_date : '');

            $kolom++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Data Induk '.($category ? ucwords($category) : 'Civitas Auliya'));

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(5);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(65);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(45);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(45);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(12);

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

        $styleArray = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
        ];

        // Table Head
        $spreadsheet->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2:A'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ],
            'numberFormat' => [
                'formatCode' => NumberFormat::FORMAT_TEXT
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('D2:H'.$max_kolom)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getStyle('I2:I'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);


        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'numberFormat' => [
                'formatCode' => NumberFormat::FORMAT_DATE_YYYYMMDD
            ],
        ];

        // Set the number format mask so that the excel timestamp will be displayed as a human-readable date/time
        $spreadsheet->getActiveSheet()->getStyle('K2:K'.$max_kolom)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('Y2:Z'.$max_kolom)->applyFromArray($styleArray);

        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'numberFormat' => [
                'formatCode' => '000'
            ],
        ];

        // Set a number format mask to display the value as 3 digits with leading zeroes
        $spreadsheet->getActiveSheet()->getStyle('N2:O'.$max_kolom)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getStyle('Q2:Q'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_TEXT);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A2:Z'.$max_kolom)->applyFromArray($styleArray);

        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

        $headers = [
            'Cache-Control' => 'max-age=0',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="data-induk-'.($category ? strtolower($category) : 'civitas-auliya').'-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xlsx"',
        ];

        return response()->stream(function()use($writer){
            $writer->save('php://output');
        }, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use DataTables;
use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Pekerjaan;
use App\Models\Unit;

use App\Http\Resources\Siswa\OrtuResource;

class AkunOrtuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'ortu';
        $this->modul = $modul;
        $this->active = 'Orang Tua';
        $this->route = $this->subsystem.'.psb.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = auth()->user()->role->name;
        
        $candidates = DB::table('tm_candidate_students')
        ->select('parent_id','student_name');
        if(auth()->user()->pegawai->unit->is_school == 1){
            $candidates = $candidates->where('unit_id',auth()->user()->pegawai->unit_id);
        }

        $students = DB::table('tm_students')
        ->select('parent_id','student_name');
        if(auth()->user()->pegawai->unit->is_school == 1){
            $students = $students->whereExists(function($q){
                $q->select(DB::raw(1))
                    ->from('student_history')
                    ->where([
                        'unit_id' => auth()->user()->pegawai->unit_id,
                        'is_lulus' => 0,
                    ])->whereColumn('tm_students.id','student_history.student_id');
            });
        }
        $students = $students->union($candidates);

        $childrens = DB::table($students, 'students')
        ->select('parent_id', DB::raw("GROUP_CONCAT(DISTINCT student_name ORDER BY student_name ASC SEPARATOR ', ') AS childrens"))
        ->groupBy('parent_id');
        ;

        $login = DB::table('login_user')
        ->select('username','user_id','role_id');

        $parents = DB::table('tm_parents')
        ->leftJoin('tm_employees', 'tm_parents.employee_id', '=', 'tm_employees.id');
        if(auth()->user()->pegawai->unit->is_school == 1){
            $parents = $parents->joinSub($childrens, 'students', function ($join) {
                $join->on('tm_parents.id', '=', 'students.parent_id');
            });
        }
        else{
            $parents = $parents->leftJoinSub($childrens, 'students', function ($join) {
                $join->on('tm_parents.id', '=', 'students.parent_id');
            });
        }
        $parents = $parents->leftJoinSub($login, 'login', function ($join) {
            $join->on('tm_parents.id', '=', 'login.user_id')
                 ->where('login.role_id', 36);
        })
        ->select('tm_parents.id','father_name','mother_name','guardian_name','father_phone','mother_phone','username',DB::raw("IF(tm_employees.id IS NULL,'N','Y') AS employee"),'childrens');

        if($request->ajax()){
            return Datatables::of($parents)
                ->addIndexColumn()
                ->setRowId('id')
                ->filterColumn('username', function($query, $keyword) {
                    $query->whereRaw("username like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('childrens', function($query, $keyword) {
                    $query->whereRaw("childrens like ?", ["%{$keyword}%"]);
                })
                ->addColumn('action', function($row){
                    $btnShow = $btnEdit = $btnReset = $btnDelete = null;
                    $btnShow = '<a href="'.route($this->route.'.show', ['id' => $row->id]).'" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-eye"></i></a>';
                    $name = ($row->father_name ? $row->father_name.($row->mother_name ? '/' : null) : null).($row->mother_name ? $row->mother_name.($row->guardian_name ? '/' : null) : null).($row->guardian_name ? $row->guardian_name : null);
                    if(in_array(request()->user()->role->name,['aspv'])){
                        if($row->childrens){
                            $btnEdit = '<a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal(\''.route($this->route.'.children.edit').'\',\''.$row->id.'\')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>';
                        }
                    }
                    if(in_array(request()->user()->role->name,['sek'])){
                        if($row->username){
                            $btnEdit = '<a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal(\''.route($this->route.'.account.edit').'\',\''.$row->id.'\')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>';
                            $btnReset = '<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#reset-confirm" onclick="resetModal(\''.addslashes(htmlspecialchars($name)).'\', \''.route($this->route.'.account.reset', ['id' => $row->id]).'\')"><i class="fas fa-sync-alt fa-flip-horizontal"></i></a>';
                        }
                    }
                    if(in_array(request()->user()->role->name,['am','aspv'])){
                        if(($row->username && $row->childrens) || (!$row->username && !$row->childrens)){
                            $btnDelete = '<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal(\''.$this->active.'\', \''.($row->username ? 'akun ' : null).addslashes(htmlspecialchars($name)).'\', \''.route($this->route.'.destroy', ['id' => $row->id]).'\')"><i class="fas fa-trash"></i></a>';
                        }
                        // Punya akun, tidak punya anak
                        elseif($row->username){
                            $btnDelete = '<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-check-confirm" onclick="deleteCheckModal(\''.$this->active.'\', \'akun '.addslashes(htmlspecialchars($name)).'\', \''.route($this->route.'.destroy', ['id' => $row->id]).'\')"><i class="fas fa-trash"></i></a>';
                        }
                        // Tidak punya akun, punya anak
                        else{
                            $btnDelete = '<button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>';
                        }
                    }
   
                    return $btnShow.$btnEdit.$btnReset.$btnDelete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $count = $parents->count();

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['sek','am','aspv'])) $editable = true;

        return view($this->route.'-index', compact('active','route','editable','count'));
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
        $data = OrangTua::find($id);
        $active = $this->active;
        $route = $this->route;

        if($data)
            return view($this->route.'-show', compact('data','active','route'));
        else
            return "Ups, tidak dapat memuat data";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = OrangTua::find($id);
        $active = $this->active;
        $route = $this->route;

        if($data){
            $jobs = Pekerjaan::active()->get();
            return view($this->route.'-edit', compact('data','active','route','jobs'));
        }
        else return redirect()->route($this->route.'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAccount(Request $request)
    {
        $data = null;
        if($request->id){
            $data = OrangTua::where('id',$request->id)->has('user');
            if(auth()->user()->pegawai->unit->is_school == 1){
                $data = $data->where(function($q){
                    $q->whereHas('siswas.siswas',function($q){
                        $q->where([
                            'unit_id' => auth()->user()->pegawai->unit_id,
                            'is_lulus' => 0
                        ]);
                    })->orWhereHas('calonSiswa',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                });
            }
            $data = $data->first();
        }
        $active = $this->active;
        $route = $this->route;

        if($data)
            return view($this->route.'-edit-account', compact('data','active','route'));
        else
            return "Ups, tidak dapat memuat data";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editChildren(Request $request)
    {
        $data = $request->id ? OrangTua::where('id',$request->id)->where(function($q){
            $q->has('siswas')->orHas('calonSiswa');
        })->first() : null;
        $active = $this->active;
        $route = $this->route;

        if($data){
            $login = DB::table('login_user')
            ->select('username','user_id','role_id');

            $parents = DB::table('tm_parents')
            ->leftJoin('tm_employees', 'tm_parents.employee_id', '=', 'tm_employees.id')
            ->leftJoinSub($login, 'login', function ($join) {
                $join->on('tm_parents.id', '=', 'login.user_id')
                     ->where('login.role_id', 36);
            })
            ->select('tm_parents.id','father_name','mother_name','guardian_name','username',DB::raw("IF(tm_employees.id IS NULL,'N','Y') AS employee"))->orderBy('id','desc')->get();

            return view($this->route.'-edit-children', compact('data','active','route','parents'));
        }
        else
            return "Ups, tidak dapat memuat data";
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
        $messages = [
            'father_nik.numeric' => 'Pastikan NIK ayah hanya mengandung angka',
            'father_phone.regex' => 'Pastikan nomor seluler ayah hanya mengandung angka',
            'mother_nik.numeric' => 'Pastikan NIK ibu hanya mengandung angka',
            'mother_phone.regex' => 'Pastikan nomor seluler ibu hanya mengandung angka',
            'guardian_nik.numeric' => 'Pastikan NIK wali hanya mengandung angka',
            'guardian_phone.regex' => 'Pastikan nomor seluler wali hanya mengandung angka',
        ];

        $this->validate($request, [
            'father_nik' => 'nullable|numeric',
            'father_phone' => 'nullable|regex:/^[0-9]+$/',
            'mother_nik' => 'nullable|numeric',
            'mother_phone' => 'nullable|regex:/^[0-9]+$/',
            'guardian_nik' => 'nullable|numeric',
            'guardian_phone' => 'nullable|regex:/^[0-9]+$/',
        ], $messages);

        $item = $request->id ? OrangTua::find($request->id) : null;

        if($item){
            $item->father_name = $request->father_name;
            $item->father_nik = $request->father_nik;
            $item->father_email = $request->father_email;
            $item->father_phone = $request->father_phone;
            $item->father_job = $request->father_job;
            $item->father_position = $request->father_position; 
            $item->father_job_address = $request->father_job_address; 
            $item->father_phone_office = $request->father_phone_office;
            $item->father_salary = $request->father_salary;

            $item->mother_name = $request->mother_name;
            $item->mother_nik = $request->mother_nik;
            $item->mother_email = $request->mother_email;
            $item->mother_phone = $request->mother_phone;
            $item->mother_job = $request->mother_job;
            $item->mother_position = $request->mother_position; 
            $item->mother_job_address = $request->mother_job_address; 
            $item->mother_phone_office = $request->mother_phone_office;
            $item->mother_salary = $request->mother_salary; 

            $item->parent_address = $request->parent_address;
            $item->parent_phone_number = $request->parent_phone_number;

            $item->guardian_name = $request->guardian_name;
            $item->guardian_nik = $request->guardian_nik;
            $item->guardian_address = $request->guardian_address;
            $item->guardian_email = $request->guardian_email;
            $item->guardian_phone_number = $request->guardian_phone;
            $item->guardian_job = $request->guardian_job;
            $item->guardian_position = $request->guardian_position;
            $item->guardian_job_address = $request->guardian_job_address;
            $item->guardian_phone_office = $request->guardian_phone_office;
            $item->guardian_salary = $request->guardian_salary; 

            $item->save();
            $item->fresh();
            
            Session::flash('success','Data '. $item->name .' berhasil diubah');

            return redirect()->route($this->route.'.show',['id' => $item->id]);
        }

        else Session::flash('danger','Perubahan data orang tua/wali gagal disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAccount(Request $request)
    {
        $messages = [
            'editUsername.required' => 'Mohon masukkan username',
        ];

        $this->validate($request, [
            'editUsername' => 'required'
        ], $messages);

        $item = null;
        if($request->id){
            $item = OrangTua::where('id',$request->id)->has('user');
            if(auth()->user()->pegawai->unit->is_school == 1){
                $item = $item->where(function($q){
                    $q->whereHas('siswas.siswas',function($q){
                        $q->where([
                            'unit_id' => auth()->user()->pegawai->unit_id,
                            'is_lulus' => 0
                        ]);
                    })->orWhereHas('calonSiswa',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                });
            }
            $item = $item->first();
        }

        if($item){
            $user = $item->user;
            $old = $user->username;
            $user->username = $request->editUsername;
            $user->save();

            $user->fresh();
            
            Session::flash('success','Data '.$old.' berhasil diubah'.($old != $user->username ? ' menjadi '.$user->username : ''));
        }

        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateChildren(Request $request)
    {
        $messages = [
            'parent.required' => 'Mohon pilih salah satu orang tua tujuan',
        ];

        $this->validate($request, [
            'parent' => 'required'
        ], $messages);

        $item = $request->id ? OrangTua::where('id',$request->id)->where(function($q){
            $q->has('siswas')->orHas('calonSiswa');
        })->first() : null;

        $parent = $request->parent ? OrangTua::select('id','father_name','mother_name','guardian_name')->where('id',$request->parent)->first() : null;

        if($item && $parent){
            $children = null;
            if($item->siswas()->count() > 0 && $item->calonSiswa()->count() > 0){
                $children = $request->childrenOpt;
            }
            elseif($item->siswas()->count() > 0){
                $children = 'student';
            }
            elseif($item->calonSiswa()->count() > 0){
                $children = 'candidate';
            }
            if($children){
                $child = null;
                if($children == 'student'){
                    $child = $item->siswas()->select('id','student_name','parent_id')->where('id',$request->student)->first();
                }
                elseif($children == 'candidate'){
                    $child = $item->calonSiswa()->select('id','student_name','parent_id')->where('id',$request->candidate)->first();
                }
                if($child){
                    $name = $child->student_name;
                    $child->parent_id = $parent->id;
                    $child->save();

                    $child->fresh();
                    
                    Session::flash('success','Data orang tua/wali '.$name.' berhasil diubah'.($child->orangtua ? ' menjadi '.$child->orangtua->name : ''));
                }
                else Session::flash('danger','Perubahan data gagal disimpan');
            }
            else Session::flash('danger','Perubahan data gagal disimpan');
        }

        else Session::flash('danger','Orang tua/wali tidak ditemukan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $item = OrangTua::find($id);
        $used_count = 0;
        if($item && $used_count < 1){
            $name = $item->name;
            if($item->user){
                if($item->childrensCount < 1 && $request->parent == 'on'){
                    $item->users()->where('role_id',36)->delete();
                    $item->delete();
                }
                else{
                    $item->user->delete();
                }
                Session::flash('success','Data akun '.$name.' berhasil dihapus');
            }
            elseif($item->childrensCount < 1){
                $item->delete();

                Session::flash('success','Data '.$name.' berhasil dihapus');
            }
            else Session::flash('danger','Data tidak dapat dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Reset password the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetAccount($id){
        $ortu = OrangTua::where('id',$id)->has('user');
        if(auth()->user()->pegawai->unit->is_school == 1){
            $ortu = $ortu->where(function($q){
                $q->whereHas('siswas.siswas',function($q){
                    $q->where([
                        'unit_id' => auth()->user()->pegawai->unit_id,
                        'is_lulus' => 0
                    ]);
                })->orWhereHas('calonSiswa',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                });
            });
        }
        $ortu = $ortu->first();

        if($ortu){
            $name = $ortu->name;
            $success = true;
            $phone = $password = null;
            if($ortu->father_phone){
                $password = $ortu->father_phone;
                $phone = 'ayah';
            }
            else{
                if($ortu->mother_phone){
                    $password = $ortu->mother_phone;
                    $phone = 'ibu';
                }
                elseif($ortu->guardian_phone_number){
                    $password = $ortu->guardian_phone_number;
                    $phone = 'wali';
                }
                else{
                    $success = false;
                }
            }
            if($success){
                $user = $ortu->user;
                $user->password = bcrypt($password);
                $user->save();

                Session::flash('success','Sandi '.$name.' berhasil di-reset ke pengaturan awal (nomor telepon '.$phone.')');
            }
            else Session::flash('danger','Sandi tidak dapat di-reset.');
        }
        else Session::flash('danger','Akun tidak ditemukan. Sandi gagal di-reset.');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $candidates = DB::table('tm_candidate_students')
        ->select('parent_id','student_name');

        $students = DB::table('tm_students')
        ->select('parent_id','student_name')
        ->union($candidates);

        $childrens = DB::table($students, 'students')
        ->select('parent_id', DB::raw("GROUP_CONCAT(DISTINCT student_name ORDER BY student_name ASC SEPARATOR ', ') AS childrens"))
        ->groupBy('parent_id');
        ;

        $login = DB::table('login_user')
        ->select('username','user_id','role_id');

        $parents = DB::table('tm_parents')
        ->leftJoin('tm_employees', 'tm_parents.employee_id', '=', 'tm_employees.id')
        ->leftJoinSub($childrens, 'students', function ($join) {
            $join->on('tm_parents.id', '=', 'students.parent_id');
        })
        ->leftJoinSub($login, 'login', function ($join) {
            $join->on('tm_parents.id', '=', 'login.user_id')
                 ->where('login.role_id', 36);
        })
        ->select('tm_parents.*','username',DB::raw("IF(tm_employees.id IS NULL,'N','Y') AS employee"),'childrens')->get();

        $spreadsheet = new Spreadsheet;

        $spreadsheet->getProperties()->setCreator('SIT Auliya')
        ->setLastModifiedBy(auth()->user()->pegawai->name)
        ->setTitle("Data Induk Orang Tua/Wali Siswa Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setSubject("Orang Tua/Wali Siswa Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setDescription("Rekapitulasi Data Induk Orang Tua/Wali Siswa Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setKeywords("Data, Induk, Orang Tua, Wali, Siswa, Auliya");

        $columnNames = [
            'No',
            'Nama Ayah',
            'NIK Ayah',
            'Email Ayah',
            'HP Ayah',
            'Pekerjaan Ayah',
            'Jabatan Ayah',
            'Alamat Kantor Ayah',
            'Telp Kantor Ayah',
            'Gaji Ayah',
            'Nama Ibu',
            'NIK Ibu',
            'Email Ibu',
            'HP Ibu',
            'Pekerjaan Ibu',
            'Jabatan Ibu',
            'Alamat Kantor Ibu',
            'Telp Kantor Ibu',
            'Gaji Ibu',
            'NIPY/NIMY',
            'Alamat Orang Tua',
            'Telp Alternatif',
            'Nama Wali',
            'NIK Wali',
            'Email Wali',
            'HP Wali',
            'Pekerjaan Wali',
            'Jabatan Wali',
            'Alamat Kantor Wali',
            'Telp Kantor Wali',
            'Gaji Wali',
            'Username',
            'Anak'
        ];

        $activeCol = 'A';

        $spreadsheet->setActiveSheetIndex(0);
        foreach($columnNames as $col){
            $spreadsheet->getActiveSheet()->setCellValue($activeCol.'1', $col);
            $activeCol++;
        }

        $row = 2;
        $no = 1;
        $max_kolom = $parents && count($parents) > 0 ? count($parents)+1 : 1;

        foreach($parents as $p) {
            $activeCol = 'A';
            $orangtua = Orangtua::find($p->id);
            $pegawai = Pegawai::find($p->employee_id);

            $spreadsheet->getActiveSheet()
            ->setCellValue($activeCol++.$row, $no++)
            ->setCellValue($activeCol++.$row, $p->father_name)
            ->setCellValueExplicit($activeCol++.$row, $p->father_nik ? strval(strlen($p->father_nik)>120?decrypt($p->father_nik):$p->father_nik) : '', DataType::TYPE_STRING)
            ->setCellValue($activeCol++.$row, $p->father_email)
            ->setCellValue($activeCol++.$row, "".(strlen($p->father_phone)>120?decrypt($p->father_phone):$p->father_phone))
            ->setCellValue($activeCol++.$row, $p->father_job)
            ->setCellValue($activeCol++.$row, $p->father_position)
            ->setCellValue($activeCol++.$row, $p->father_job_address)
            ->setCellValue($activeCol++.$row, $p->father_phone_office)
            ->setCellValue($activeCol++.$row, $p->father_salary)
            ->setCellValue($activeCol++.$row, $p->mother_name)
            ->setCellValueExplicit($activeCol++.$row, $p->mother_nik ? strval(strlen($p->mother_nik)>120?decrypt($p->mother_nik):$p->mother_nik) : '', DataType::TYPE_STRING)
            ->setCellValue($activeCol++.$row, $p->mother_email)
            ->setCellValue($activeCol++.$row, "".(strlen($p->mother_phone)>120?decrypt($p->mother_phone):$p->mother_phone))
            ->setCellValue($activeCol++.$row, $p->mother_job)
            ->setCellValue($activeCol++.$row, $p->mother_position)
            ->setCellValue($activeCol++.$row, $p->mother_job_address)
            ->setCellValue($activeCol++.$row, $p->mother_phone_office)
            ->setCellValue($activeCol++.$row, $p->mother_salary)
            ->setCellValueExplicit($activeCol++.$row, strval($pegawai && $pegawai->nip ? $pegawai->nip : ''), DataType::TYPE_STRING)
            ->setCellValue($activeCol++.$row, strlen($p->parent_address)>120?decrypt($p->parent_address):$p->parent_address)
            ->setCellValue($activeCol++.$row, "".(strlen($p->parent_phone_number)>120?decrypt($p->parent_phone_number):$p->parent_phone_number))
            ->setCellValue($activeCol++.$row, $p->guardian_name)
            ->setCellValueExplicit($activeCol++.$row, $p->guardian_nik ? strval(strlen($p->guardian_nik)>120?decrypt($p->guardian_nik):$p->guardian_nik) : '', DataType::TYPE_STRING)
            ->setCellValue($activeCol++.$row, $p->guardian_email)
            ->setCellValue($activeCol++.$row, "".(strlen($p->guardian_phone_number)>120?decrypt($p->guardian_phone_number):$p->guardian_phone_number))
            ->setCellValue($activeCol++.$row, $p->guardian_job)
            ->setCellValue($activeCol++.$row, $p->guardian_position)
            ->setCellValue($activeCol++.$row, $p->guardian_job_address)
            ->setCellValue($activeCol++.$row, $p->guardian_phone_office)
            ->setCellValue($activeCol++.$row, $p->guardian_salary)
            ->setCellValue($activeCol++.$row, $p->username)
            ->setCellValue($activeCol++.$row, $orangtua->siswas()->count() > 0 ? $orangtua->studentsWithClass : '');

            $row++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Orang Tua Wali Siswa Auliya');
    
        $activeCol = 'A';
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(4);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(100);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(30);

        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(100);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(30);

        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(100);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(25);

        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(100);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(30);

        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension($activeCol++)->setWidth(100);


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
        $spreadsheet->getActiveSheet()->getStyle('A1:AG1')->applyFromArray($styleArray);
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

        $spreadsheet->getActiveSheet()->getStyle('T2:T'.$max_kolom)->applyFromArray($styleArray);

        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

        $headers = [
            'Cache-Control' => 'max-age=0',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="data-induk-orang-tua-wali-siswa-auliya'.'-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xlsx"',
        ];

        return response()->stream(function()use($writer){
            $writer->save('php://output');
        }, 200, $headers);
    }
}

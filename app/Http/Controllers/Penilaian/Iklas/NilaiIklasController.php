<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\NilaiIklas;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class NilaiIklasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'nilai-iklas';
        $this->modul = $modul;
        $this->active = 'Nilai IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $siswa = null)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $kelasList = $riwayatKelas = $competencies = $descs = $count = $nilai = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q){
                $q->where(function($q){
                    $q->where('is_active',1);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q){
                $q->where('is_active',1);
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas();
                if(!in_array($role,$managementRoles)){
                    $kelasList = $kelasList->where('unit_id',auth()->user()->pegawai->unit_id);
                    if($isWali){
                        $kelasList = $kelasList->where([
                            'academic_year_id' => $tahun->id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    }
                }
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                    ])->whereIn('curriculum_id',[1,2]);
                })->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
                    if(!in_array($role,$managementRoles)){
                        $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                        if($isWali){
                            $kelas = $kelas->where([
                                'academic_year_id' => $tahun->id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        }
                    }
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                        ])->whereIn('curriculum_id',[1,2]);
                    })->first();

                    if($kelas){
                        // Inti function
                        $unit = $kelas->unit()->select('id','name')->first();
                        
                        $competencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        $descs = DeskripsiIklas::whereHas('kurikulum',function($q)use($semester){
                            $q->where([
                                'semester_id' => $semester->id
                            ]);
                        })->get();

                        $riwayatKelas = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                        $raporQuery = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->has('nilaiIklas');

                        if($riwayatKelas && count($riwayatKelas) > 0){
                            foreach($riwayatKelas as $r){
                                $rapor[$r->id] = clone $raporQuery;
                                $rapor[$r->id] = $rapor[$r->id]->where('student_id',$r->id)->first();
                                if($rapor[$r->id] && $competencies && count($competencies) > 0){
                                    $count[$r->id] = $rapor[$r->id]->nilaiIklas()->whereHas('kompetensi.categories',function($q)use($unit,$semester){
                                        $q->where([
                                            'semester_id' => $semester->id,
                                            'unit_id' => $unit->id
                                        ]);
                                    })->where('predicate','>',0)->count();
                                    foreach($competencies as $c){
                                        $nilaiKompetensi = $rapor[$r->id]->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                                        $nilai[$r->id][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;
                                    }
                                }
                            }
                        }

                        if($siswa){
                            $siswa = str_replace("-","/",$siswa);
                            $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->where('student_nis', $siswa)->has('identitas')->with('identitas:id,student_name')->first();

                            if($siswa){
                                if($rapor[$siswa->id] && $competencies && count($competencies) > 0){
                                    foreach($competencies as $c){
                                        $nilaiKompetensi = $rapor[$siswa->id]->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                                        $nilai[$r->id][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;
                                    }
                                }
                            }
                            else{
                                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                            }
                        }
                    }
                    else{
                        return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
            }
        }
        else{
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','riwayatKelas','siswa','competencies','descs','count','nilai'));
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->where(function($q){
                $q->where('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->where('is_active',1);
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->whereIn('curriculum_id',[1,2]);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name')->first();
                    
                    $competencies = $unit->kompetensiKategoriIklas()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($competencies && count($competencies) > 0){
                        $rapor = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->where('student_id',$siswa->id)->first();

                        if(!$rapor){
                            $jabatan = Jabatan::where('code','11')->first();
                            $kepsek = $jabatan->pegawaiUnit()->where('unit_id',$kelas->unit_id)->whereHas('pegawai',function($q){
                                $q->aktif();
                            })->first();

                            $rapor = new NilaiRapor();
                            $rapor->student_id = $siswa->id;
                            $rapor->semester_id = $semester->id;
                            $rapor->class_id = $kelas->id;
                            $rapor->report_status_id = 0;
                            $rapor->acc_id = 0;
                            $rapor->unit_id = $kelas->unit_id;
                            $rapor->hr_name = $kelas->walikelas ? $kelas->walikelas->name : '-';
                            $rapor->hm_name = $kepsek ? $kepsek->pegawai->name : '-';
                            $rapor->save();
                            $rapor->fresh();
                        }
                        if($rapor){
                            $savedCount = 0;
                            foreach($competencies as $c){
                                $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();                            
                                if(!$nilaiKompetensi){
                                    $nilaiKompetensi = new NilaiIklas();
                                    $nilaiKompetensi->report_score_id = $rapor->id;
                                    $nilaiKompetensi->competence_id = $c->competence_id;
                                    $nilaiKompetensi->predicate = 0;
                                    $nilaiKompetensi->save();
                                    $nilaiKompetensi->fresh();
                                }
                                if($nilaiKompetensi){
                                    $predicate = isset($request->rating[$c->category->number][$c->number]) ? $request->rating[$c->category->number][$c->number] : 0;
                                    if(isset($predicate) && $predicate >= 0){
                                        $nilaiKompetensi->predicate = $predicate;
                                        $nilaiKompetensi->save();
                                        $savedCount++;
                                    }
                                }
                            }
                            if($savedCount == count($competencies)){
                                Session::flash('success', 'Semua perubahan predikat kompetensi IKLaS berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < count($competencies))){
                                Session::flash('success', 'Beberapa perubahan predikat kompetensi IKLaS berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', $role == 'kepsek' ? 'Belum ada kompetensi yang dapat dinilai' : 'Tidak dapat menyimpan perubahan nilai IKLaS');
                            }
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data kurikulum IKLaS yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]);
                }
                else{
                    Session::flash('danger', 'Tidak ada data siswa yang ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\NilaiIklas;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class NilaiIklasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'nilai-iklas';
        $this->modul = $modul;
        $this->active = 'Nilai IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $siswa = null)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $kelasList = $riwayatKelas = $competencies = $descs = $count = $nilai = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q){
                $q->where(function($q){
                    $q->where('is_active',1);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q){
                $q->where('is_active',1);
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas();
                if(!in_array($role,$managementRoles)){
                    $kelasList = $kelasList->where('unit_id',auth()->user()->pegawai->unit_id);
                    if($isWali){
                        $kelasList = $kelasList->where([
                            'academic_year_id' => $tahun->id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    }
                }
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                    ])->whereIn('curriculum_id',[1,2]);
                })->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
                    if(!in_array($role,$managementRoles)){
                        $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                        if($isWali){
                            $kelas = $kelas->where([
                                'academic_year_id' => $tahun->id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        }
                    }
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                        ])->whereIn('curriculum_id',[1,2]);
                    })->first();

                    if($kelas){
                        // Inti function
                        $unit = $kelas->unit()->select('id','name')->first();
                        
                        $competencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        $descs = DeskripsiIklas::whereHas('kurikulum',function($q)use($semester){
                            $q->where([
                                'semester_id' => $semester->id
                            ]);
                        })->get();

                        $riwayatKelas = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                        $raporQuery = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->has('nilaiIklas');

                        if($riwayatKelas && count($riwayatKelas) > 0){
                            foreach($riwayatKelas as $r){
                                $rapor[$r->id] = clone $raporQuery;
                                $rapor[$r->id] = $rapor[$r->id]->where('student_id',$r->id)->first();
                                if($rapor[$r->id] && $competencies && count($competencies) > 0){
                                    $count[$r->id] = $rapor[$r->id]->nilaiIklas()->whereHas('kompetensi.categories',function($q)use($unit,$semester){
                                        $q->where([
                                            'semester_id' => $semester->id,
                                            'unit_id' => $unit->id
                                        ]);
                                    })->where('predicate','>',0)->count();
                                    foreach($competencies as $c){
                                        $nilaiKompetensi = $rapor[$r->id]->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                                        $nilai[$r->id][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;
                                    }
                                }
                            }
                        }

                        if($siswa){
                            $siswa = str_replace("-","/",$siswa);
                            $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->where('student_nis', $siswa)->has('identitas')->with('identitas:id,student_name')->first();

                            if($siswa){
                                if($rapor[$siswa->id] && $competencies && count($competencies) > 0){
                                    foreach($competencies as $c){
                                        $nilaiKompetensi = $rapor[$siswa->id]->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                                        $nilai[$r->id][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;
                                    }
                                }
                            }
                            else{
                                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                            }
                        }
                    }
                    else{
                        return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
            }
        }
        else{
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','riwayatKelas','siswa','competencies','descs','count','nilai'));
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->where(function($q){
                $q->where('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->where('is_active',1);
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->whereIn('curriculum_id',[1,2]);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name')->first();
                    
                    $competencies = $unit->kompetensiKategoriIklas()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($competencies && count($competencies) > 0){
                        $rapor = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->where('student_id',$siswa->id)->first();

                        if(!$rapor){
                            $jabatan = Jabatan::where('code','11')->first();
                            $kepsek = $jabatan->pegawaiUnit()->where('unit_id',$kelas->unit_id)->whereHas('pegawai',function($q){
                                $q->aktif();
                            })->first();

                            $rapor = new NilaiRapor();
                            $rapor->student_id = $siswa->id;
                            $rapor->semester_id = $semester->id;
                            $rapor->class_id = $kelas->id;
                            $rapor->report_status_id = 0;
                            $rapor->acc_id = 0;
                            $rapor->unit_id = $kelas->unit_id;
                            $rapor->hr_name = $kelas->walikelas ? $kelas->walikelas->name : '-';
                            $rapor->hm_name = $kepsek ? $kepsek->pegawai->name : '-';
                            $rapor->save();
                            $rapor->fresh();
                        }
                        if($rapor){
                            $savedCount = 0;
                            foreach($competencies as $c){
                                $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();                            
                                if(!$nilaiKompetensi){
                                    $nilaiKompetensi = new NilaiIklas();
                                    $nilaiKompetensi->report_score_id = $rapor->id;
                                    $nilaiKompetensi->competence_id = $c->competence_id;
                                    $nilaiKompetensi->predicate = 0;
                                    $nilaiKompetensi->save();
                                    $nilaiKompetensi->fresh();
                                }
                                if($nilaiKompetensi){
                                    $predicate = isset($request->rating[$c->category->number][$c->number]) ? $request->rating[$c->category->number][$c->number] : 0;
                                    if(isset($predicate) && $predicate >= 0){
                                        $nilaiKompetensi->predicate = $predicate;
                                        $nilaiKompetensi->save();
                                        $savedCount++;
                                    }
                                }
                            }
                            if($savedCount == count($competencies)){
                                Session::flash('success', 'Semua perubahan predikat kompetensi IKLaS berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < count($competencies))){
                                Session::flash('success', 'Beberapa perubahan predikat kompetensi IKLaS berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', $role == 'kepsek' ? 'Belum ada kompetensi yang dapat dinilai' : 'Tidak dapat menyimpan perubahan nilai IKLaS');
                            }
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data kurikulum IKLaS yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]);
                }
                else{
                    Session::flash('danger', 'Tidak ada data siswa yang ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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

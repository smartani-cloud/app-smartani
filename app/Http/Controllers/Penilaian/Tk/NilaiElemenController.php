<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\Tk\FormatifKualitatif;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class NilaiElemenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'nilai-elemen';
        $this->modul = $modul;
        $this->active = 'Nilai Elemen';
        $this->route = $this->subsystem.'.penilaian.tk.'.$this->modul;
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

        $kelasList = $riwayatKelas = $elements = $objectives = $descs = $count = $nilai = null;

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

                        $elements = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives',function($q)use($semester){
                            $q->where('semester_id', $semester->id);
                        })->aktif()->orderBy('dev_aspect')->count();
                        
                        $objectives = $kelas->level->objectiveElements()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        $descs = PredikatDeskripsi::select('id','predicate','description')->where([
                            'semester_id' => $semester->id,
                            'level_id' => $kelas->level_id,
                            'rpd_type_id' => 15
                        ])->whereNotNull('description')->count();

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
                        })->has('formatifKualitatif');

                        if($riwayatKelas && count($riwayatKelas) > 0){
                            foreach($riwayatKelas as $r){
                                $rapor[$r->id] = clone $raporQuery;
                                $rapor[$r->id] = $rapor[$r->id]->where('student_id',$r->id)->first();
                                if($rapor[$r->id] && $objectives && count($objectives) > 0){
                                    $count[$r->id] = $rapor[$r->id]->formatifKualitatif()->whereHas('objective.elements',function($q)use($semester,$kelas){
                                        $q->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ]);
                                    })->where('score','>',0)->count();
                                    foreach($objectives as $o){
                                        $nilaiFormatif = $rapor[$r->id]->formatifKualitatif()->where('objective_id',$o->objective_id)->first();
                                        $nilai[$r->id][$o->objective_id] = $nilaiFormatif ? $nilaiFormatif->predicate : null;
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

                            if(!$siswa){
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','riwayatKelas','siswa','elements','objectives','descs','count','nilai'));
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
                    
                    $objectives = $kelas->level->objectiveElements()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($objectives && count($objectives) > 0){
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
                            $predicateList = ['A' => 4,'B' => 3,'C' => 2,'D' => 1];
                            foreach($objectives as $o){
                                $predicate = isset($request->predicate[$o->sort_order]) ? $request->predicate[$o->sort_order] : null;
                                $nilaiFormatif = $rapor->formatifKualitatif()->where('objective_id',$o->objective_id)->first();                            
                                if(!$nilaiFormatif && $predicate && in_array($predicate,['A','B','C','D'])){
                                    $nilaiFormatif = new FormatifKualitatif();
                                    $nilaiFormatif->report_score_id = $rapor->id;
                                    $nilaiFormatif->objective_id = $o->objective_id;
                                    $nilaiFormatif->predicate = null;
                                    $nilaiFormatif->score = 0;
                                    $nilaiFormatif->save();
                                    $nilaiFormatif->fresh();
                                }
                                if($nilaiFormatif){
                                    if($predicate && in_array($predicate,['A','B','C','D'])){
                                        $nilaiFormatif->predicate = $predicate;
                                        $score = $predicateList[$predicate];
                                        if(isset($score) && $score > 0){
                                            $nilaiFormatif->score = $score;
                                        }
                                        $nilaiFormatif->save();
                                        $savedCount++;
                                    }
                                }
                            }
                            if($savedCount == count($objectives)){
                                Session::flash('success', 'Semua perubahan perkembangan berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < count($objectives))){
                                Session::flash('success', 'Beberapa perubahan perkembangan berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', $role == 'kepsek' ? 'Belum ada tujuan pembelajaran yang dapat dinilai' : 'Tidak dapat menyimpan perubahan perkembangan');
                            }
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data tujuan pembelajaran yang ditemukan');
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

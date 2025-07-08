<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Helpers\StringHelper;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\Tk\FormatifKualitatif;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class PerkembanganController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'perkembangan';
        $this->modul = $modul;
        $this->active = 'Perkembangan';
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

        $kelasList = $riwayatKelas = $elements = $objectives = $descs = $count = $nilai = $class = null;

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

                        $count['elements'] = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives',function($q)use($semester){
                            $q->where('semester_id', $semester->id);
                        })->aktif()->orderBy('dev_aspect')->count();
                        
                        $objectives = $kelas->level->objectiveElements()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        $count['desc'] = PredikatDeskripsi::select('id','predicate','description')->where([
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
                                if($rapor[$siswa->id]){
                                    $predicates = [4 => 'A', 3 => 'B', 2 => 'C', 1 => 'D'];
                                    $elements = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives.objective.predicates',function($q)use($rapor,$siswa){
                                        $q->where('report_score_id',$rapor[$siswa->id]->id)->where('score','>',0);
                                    })->get();
                                    if($elements && count($elements) > 0){
                                        foreach($elements as $element){
                                            $scoreQuery = $rapor[$siswa->id]->formatifKualitatif()->where('score','>',0)->whereHas('objective.elements',function($q)use($element){
                                                $q->where('element_id', $element->id);
                                            });
                                            $maxScore[$element->id] = clone $scoreQuery;
                                            $maxScore[$element->id] = $maxScore[$element->id]->max('score');
                                            $minScore[$element->id] = clone $scoreQuery;
                                            $minScore[$element->id] = $minScore[$element->id]->min('score');

                                            if($maxScore[$element->id]){
                                                $descsQuery = PredikatDeskripsi::select('id','predicate','description')->where([
                                                    'semester_id' => $semester->id,
                                                    'level_id' => $kelas->level_id,
                                                    'subject_id' => $element->id,
                                                    'rpd_type_id' => 15
                                                ])->whereNotNull('description');

                                                foreach(['max','min'] as $m){
                                                    $descs[$element->id][$m] = clone $descsQuery;
                                                    $descs[$element->id][$m] = $descs[$element->id][$m]->where('predicate',$m)->first();
                                                    $descs[$element->id][$m] = $descs[$element->id][$m] ? $descs[$element->id][$m]->description : null;
                                                }

                                                $maxScores[$element->id] = Objective::select('id','desc')->whereHas('elements',function($q)use($element){
                                                    $q->where('element_id',$element->id);
                                                })->whereHas('predicates',function($q)use($rapor,$siswa,$maxScore,$element){
                                                    $q->where([
                                                        'report_score_id' => $rapor[$siswa->id]->id,
                                                        'score' => $maxScore[$element->id]
                                                    ]);
                                                })->with('elements:id,sort_order')->get()->sortBy('sort_order');
                                                if($minScore[$element->id] && ($maxScore[$element->id] != $minScore[$element->id])){
                                                    $minScores[$element->id] = Objective::select('id','desc')->whereHas('elements',function($q)use($element){
                                                        $q->where('element_id',$element->id);
                                                    })->whereHas('predicates',function($q)use($rapor,$siswa,$minScore,$element){
                                                        $q->where([
                                                            'report_score_id' => $rapor[$siswa->id]->id,
                                                            'score' => $minScore[$element->id]
                                                        ]);
                                                    })->with('elements:id,sort_order')->get()->sortBy('sort_order');
                                                    $nilai[$siswa->id][$element->id]['min'] = $predicates[$minScore[$element->id]];

                                                    if(isset($descs[$element->id]['min'])){
                                                        $nilai[$siswa->id][$element->id]['rendah'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs[$element->id]['min']);
                                                        $nilai[$siswa->id][$element->id]['rendah'] = str_replace("@elemen",$element->dev_aspect,$nilai[$siswa->id][$element->id]['rendah']);
                                                        $nilai[$siswa->id][$element->id]['rendah'] = str_replace("@capaian",'<b>'.strtolower(StringHelper::natural_language_join($minScores[$element->id]->pluck('desc')->toArray(),'dan')).'</b>',$nilai[$siswa->id][$element->id]['rendah']);
                                                    }
                                                }
                                                $nilai[$siswa->id][$element->id]['maks'] = $predicates[$maxScore[$element->id]];
                                                if(isset($descs[$element->id]['max'])){
                                                    $nilai[$siswa->id][$element->id]['tinggi'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs[$element->id]['max']);
                                                    $nilai[$siswa->id][$element->id]['tinggi'] = str_replace("@elemen",$element->dev_aspect,$nilai[$siswa->id][$element->id]['tinggi']);
                                                    $nilai[$siswa->id][$element->id]['tinggi'] = str_replace("@capaian",'<b>'.strtolower(StringHelper::natural_language_join($maxScores[$element->id]->pluck('desc')->toArray(),'dan')).'</b>',$nilai[$siswa->id][$element->id]['tinggi']);
                                                }
                                            }
                                        }
                                        foreach($objectives as $o){
                                            if(isset($nilai[$siswa->id][$o->objective_id])){
                                                if(isset($nilai[$siswa->id][$o->element_id]['maks']) && $nilai[$siswa->id][$o->objective_id] == $nilai[$siswa->id][$o->element_id]['maks']){
                                                    $class[$o->objective_id] = 'font-weight-bold text-success';
                                                }
                                                if(isset($nilai[$siswa->id][$o->element_id]['min']) && $nilai[$siswa->id][$o->objective_id] == $nilai[$siswa->id][$o->element_id]['min']){
                                                    $class[$o->objective_id] = 'font-weight-bold text-warning';
                                                }
                                            }
                                        }
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','riwayatKelas','siswa','elements','objectives','descs','count','nilai','class'));
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

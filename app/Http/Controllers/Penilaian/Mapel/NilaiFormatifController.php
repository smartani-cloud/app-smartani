<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Kurdeka\NilaiAkhir;
use App\Models\Penilaian\Kurdeka\NilaiFormatif;
use App\Models\Penilaian\Kurdeka\PersentaseNilaiAkhir;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class NilaiFormatifController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'formatif';
        $this->modul = $modul;
        $this->active = 'Nilai Formatif';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $mataPelajaran = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $mataPelajaranList = null;
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $riwayatKelas = $tpfCount = $nilai = null;

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
                $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id) : Kelas::whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($kelas){
                    $kelas = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]) : Kelas::whereHas('jadwal',function($q)use($semester){
                        $q->where([
                            'teacher_id' => auth()->user()->pegawai->id,
                            'semester_id' => $semester->id,
                        ]);
                    })->where('id', $kelas);
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($kelas){
                        $unit = $kelas->unit()->select('id','name')->first();

                        $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                        if($kelas->major_id){
                            $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                            $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                        }
                        else $kelompok = $kelompok_umum;

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                            $q->when($role == 'guru',function($q){
                                return $q->where('employee_id',auth()->user()->pegawai->id);
                            })->whereHas('skbm',function($q)use($tahun){
                                $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                            });
                        })->whereHas('jadwalPelajaran',function($q)use($role,$semester,$kelas){
                            $q->when($role == 'guru',function($q){
                                return $q->where('teacher_id', auth()->user()->pegawai->id);
                            })->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        });

                        if($unit->name == 'SD'){
                            $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                                $q->where('level_id',$kelas->level_id);
                            });
                        }

                        if($semester->is_active == 0){
                            $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                $q->where('semester_id',$semester->id);
                            });
                        }

                        $mapel = clone $mapelFiltered;
                        $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                        if($mapel->count() > 0){
                            $mataPelajaranList = $mapel->get();
                        }

                        $mapelMulok = clone $mapelFiltered;
                        $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                        if($mapelMulok->count() > 0){
                            $mataPelajaranList = $mataPelajaranList ? $mataPelajaranList->concat($mapelMulok->get()) : $mapelMulok->get();
                        }

                        if($mataPelajaran){
                            $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                            if($mataPelajaran){
                                // Inti Function
                                $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                                $tpfCount = $mataPelajaran->kd()->select('kd')->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id,
                                    'kd_type_id' => 3
                                ])->whereNotNull('kd')->first();

                                $raporQuery = $kelas->rapor()->where([
                                    'semester_id' => $semester->id
                                ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->whereHas('nilaiAkhirKurdeka',function($q)use($mataPelajaran){
                                    $q->where([
                                        'subject_id' => $mataPelajaran->id
                                    ]);
                                });

                                if($riwayatKelas && count($riwayatKelas) > 0){
                                    foreach($riwayatKelas as $r){
                                        $rapor = clone $raporQuery;
                                        $rapor = $rapor->where('student_id',$r->id)->first();
                                        if($rapor){
                                            $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                                'subject_id' => $mataPelajaran->id
                                            ])->first();
                                            $nilai[$r->id]['akhir'] = $nilaiAkhir;
                                            if($nilaiAkhir && $tpfCount){
                                                for($i=1;$i<=$tpfCount->kd;$i++){
                                                    $nilaiPf = $nilaiAkhir->nilaiFormatif()->where('index',$i)->first();
                                                    $nilai[$r->id][$i] = $nilaiPf ? $nilaiPf->scoreWithSeparator : 0;
                                                }
                                            }
                                        }
                                    }
                                }
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
            // $semester = Semester::aktif()->first();
            // $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get() : $semester->jadwalPelajarans()->where('teacher_id', auth()->user()->pegawai->id)->with('kelas:id,level_id,class_name_id')->get()->pluck('kelas')->unique();
            // if($role == 'guru'){
            //     $kelas = $semester->jadwalPelajarans()->where('teacher_id', auth()->user()->pegawai->id)->with('kelas:id,level_id,class_name_id')->get()->pluck('kelas')->unique()->first();
            //     if($kelas){
            //         return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            //     }
            //     else return redirect()->route($this->route.'.index');
            // }

            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','mataPelajaranList','mataPelajaran','riwayatKelas','tpfCount','nilai'));
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
    public function update(Request $request, $tahun, $semester, $kelas, $mataPelajaran)
    {
        $role = $request->user()->role->name;

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
            $kelas = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]) : Kelas::whereHas('jadwal',function($q)use($semester){
                $q->where([
                    'teacher_id' => auth()->user()->pegawai->id,
                    'semester_id' => $semester->id,
                ]);
            })->where('id', $kelas);
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($kelas){
                $unit = $kelas->unit()->select('id','name')->first();

                $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if($kelas->major_id){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                    $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                }
                else $kelompok = $kelompok_umum;

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                })->whereHas('jadwalPelajaran',function($q)use($role,$semester,$kelas){
                    $q->when($role == 'guru',function($q){
                        return $q->where('teacher_id', auth()->user()->pegawai->id);
                    })->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                        $q->where('level_id',$kelas->level_id);
                    });
                }

                if($semester->is_active == 0){
                    $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                        $q->where('semester_id',$semester->id);
                    });
                }

                $mataPelajaranList = null;

                $mapel = clone $mapelFiltered;
                $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                if($mapel->count() > 0){
                    $mataPelajaranList = $mapel->get();
                }

                $mapelMulok = clone $mapelFiltered;
                $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                if($mapelMulok->count() > 0){
                    $mataPelajaranList = $mataPelajaranList ? $mataPelajaranList->concat($mapelMulok->get()) : $mapelMulok->get();
                }

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                    $tpfCount = $mataPelajaran->kd()->select('kd')->where([
                        'semester_id' => $semester->id,
                        'level_id' => $kelas->level_id,
                        'kd_type_id' => 3
                    ])->whereNotNull('kd')->first();

                    $percentages = $mataPelajaran->finalScorePercentages()->where([
                        'semester_id' => $semester->id,
                        'level_id' => $kelas->level_id
                    ])->first();

                    if(!$percentages){
                        $percentages = new PersentaseNilaiAkhir();
                        $percentages->semester_id = $semester->id;
                        $percentages->level_id = $kelas->level_id;
                        $percentages->subject_id = $mataPelajaran->id;
                        $percentages->naf_percentage = 25;
                        $percentages->nas_percentage = 25;
                        $percentages->ntss_percentage = 25;
                        $percentages->nass_percentage = 25;
                        $percentages->save();
                        $percentages->fresh();
                    }

                    $raporQuery = $kelas->rapor()->where([
                        'semester_id' => $semester->id
                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    });

                    if($riwayatKelas && count($riwayatKelas) > 0){
                        $savedCount = 0;
                        foreach($riwayatKelas as $r){
                            // Inti Function
                            $rapor = clone $raporQuery;
                            $rapor = $rapor->where('student_id',$r->id)->first();
                            if(!$rapor){
                                $jabatan = Jabatan::where('code','11')->first();
                                $kepsek = $jabatan->pegawaiUnit()->where('unit_id',$kelas->unit_id)->whereHas('pegawai',function($q){
                                    $q->aktif();
                                })->first();

                                $rapor = new NilaiRapor();
                                $rapor->student_id = $r->id;
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
                                $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                    'subject_id' => $mataPelajaran->id
                                ])->first();
                                if(!$nilaiAkhir){
                                    $nilaiAkhir = new NilaiAkhir();
                                    $nilaiAkhir->report_score_id = $rapor->id;
                                    $nilaiAkhir->subject_id = $mataPelajaran->id;
                                    $nilaiAkhir->save();
                                    $nilaiAkhir->fresh();
                                }

                                if($nilaiAkhir){
                                    $inputName = 's-'.$r->id.'-project';
                                    $score = $request->{$inputName};
                                    if(isset($score) && $score >= 0){
                                        $nilaiAkhir->project = $request->{$inputName};
                                    }
                                    if($tpfCount){
                                        for($i=1;$i<=$tpfCount->kd;$i++){
                                            $nilaiPf = $nilaiAkhir->nilaiFormatif()->where('index',$i)->first();
                                            if(!$nilaiPf){
                                                $nilaiPf = new NilaiFormatif();
                                                $nilaiPf->rkd_score_id = $nilaiAkhir->id;
                                                $nilaiPf->index = $i;
                                                $nilaiPf->save();
                                                $nilaiPf->fresh();
                                            }
                                            $inputName = 's-'.$r->id.'-pf-'.$i;
                                            $score = $request->{$inputName};
                                            if(isset($score) && $score >= 0){
                                                $nilaiPf->score = $score;
                                                $nilaiPf->save();
                                            }
                                        }
                                    }
                                    $totalDatas = $tpfCount ? ($tpfCount->kd+1) : 1;
                                    $nilaiAkhir->naf = ($nilaiAkhir->project+$nilaiAkhir->nilaiFormatif()->sum('score'))/$totalDatas;
                                    if($percentages){
                                        $nar = 0;
                                        $finalScores = ['naf','nas','ntss','nass'];
                                        foreach($finalScores as $f){
                                            $attr = $f.'_percentage';
                                            $nar += (($percentages->{$attr}/100)*$nilaiAkhir->{$f});
                                        }
                                        $nilaiAkhir->nar = $nar;
                                    }
                                    $nilaiAkhir->save();
                                    $savedCount++;
                                }
                            }
                        }
                        if($savedCount == count($riwayatKelas)){
                            Session::flash('success', 'Semua perubahan nilai formatif berhasil disimpan');
                        }
                        elseif($savedCount > 0 && ($savedCount < count($riwayatKelas))){
                            Session::flash('success', 'Beberapa perubahan nilai formatif berhasil disimpan');
                        }
                        else{
                            Session::flash('danger', $role == 'kepsek' ? 'Belum ada nilai yang dimasukkan oleh guru mata pelajaran ini' : 'Tidak dapat menyimpan perubahan nilai formatif');
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data siswa yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

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

<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\Kurdeka\NilaiFormatif;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class JumlahTpfController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'jumlah-tpf';
        $this->modul = $modul;
        $this->active = 'Jumlah TP Formatif';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $tingkat = null)
    {
        $role = auth()->user()->role->name;

        $tingkatList = $mataPelajaranList = null;
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $data = null;

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
                $tingkatList = auth()->user()->pegawai->unit->levels()->whereHas('classes',function($q)use($semester){
                    $q->whereHas('jadwal',function($q)use($semester){
                        $q->where([
                            'teacher_id' => auth()->user()->pegawai->id,
                            'semester_id' => $semester->id,
                        ]);
                    });
                })->whereHas('curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($tingkat){
                    $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                        $q->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        });
                    })->whereHas('curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($tingkat){
                        $unit = auth()->user()->pegawai->unit;

                        $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                            $q->when($role == 'guru',function($q){
                                return $q->where('employee_id',auth()->user()->pegawai->id);
                            })->whereHas('skbm',function($q)use($tahun){
                                $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                            });
                        });

                        if($unit->name == 'SD'){
                            $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                                $q->where('level_id',$tingkat->id);
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

                        // Inti Function
                        $data = null;

                        if($mataPelajaranList && count($mataPelajaranList) > 0){
                            foreach($mataPelajaranList as $m){
                                $kd[$m->id] = $m->kd()->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $tingkat->id,
                                    'kd_type_id' => 3
                                ])->first();

                                $data[$m->id] = $kd[$m->id] ? $kd[$m->id]->kd : null;
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
            // $tingkatList = auth()->user()->pegawai->unit->levels()->get();

            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','tingkatList','tingkat','mataPelajaranList','data'));
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
    public function update(Request $request, $tahun, $semester, $tingkat)
    {
        $role = auth()->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->where(function($q){
                $q->where('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                // Inti function
                if($mataPelajaranList && count($mataPelajaranList) > 0){
                    $kdCount = 0;
                    foreach($mataPelajaranList as $mataPelajaran){
                        $inputName = 'value-'.$mataPelajaran->id;

                        $kd = $mataPelajaran->kd()->where([
                            'semester_id' => $semester->id,
                            'level_id' => $tingkat->id,
                            'kd_type_id' => 3
                        ])->first();

                        if(!$kd){
                            KDSetting::create([
                                'semester_id' => $semester->id,
                                'level_id' => $tingkat->id,
                                'subject_id' => $mataPelajaran->id,
                                'employee_id' => auth()->user()->pegawai->id,
                                'kd_type_id' => 3,
                                'kd' => $request->{$inputName},
                                'kd_type_id' => 3,
                            ]);

                            $kdCount++;
                        }
                        else{
                            $old = $kd->kd;
                            $kd->kd = $request->{$inputName};
                            $kd->save();
                            $kd->fresh();

                            if(($old > $kd->kd) || ($old < $kd->kd)){
                                $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where([
                                        'unit_id' => $request->user()->pegawai->unit_id,
                                        'level_id' => $tingkat->id
                                    ])->get() : Kelas::where('level_id',$tingkat->id)->whereHas('jadwal',function($q)use($semester){
                                    $q->where([
                                        //'teacher_id' => auth()->user()->pegawai->id,
                                        'semester_id' => $semester->id,
                                    ]);
                                })->get();

                                foreach($kelasList as $kelas){
                                    $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                        $q->where([
                                            'semester_id' => $semester->id,
                                            'class_id' => $kelas->id
                                        ]);
                                    })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                                    $percentages = $mataPelajaran->finalScorePercentages()->where([
                                        'semester_id' => $semester->id,
                                        'level_id' => $kelas->level_id
                                    ])->first();

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
                                        if($old > $kd->kd){
                                            NilaiFormatif::whereHas('nilaiAkhir',function($q)use($semester,$kelas,$mataPelajaran){
                                                $q->whereHas('rapor',function($q)use($semester,$kelas){
                                                    $q->where([
                                                        'semester_id' => $semester->id,
                                                        'class_id' => $kelas->id
                                                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                                                        $q->where([
                                                            'semester_id' => $semester->id,
                                                            'class_id' => $kelas->id
                                                        ]);
                                                    });
                                                })->where('subject_id',$mataPelajaran->id);
                                            })->where('index','>',$kd->kd)->delete();
                                        }

                                        foreach($riwayatKelas as $r){
                                            $rapor = clone $raporQuery;
                                            $rapor = $rapor->where('student_id',$r->id)->first();
                                            if($rapor){
                                                $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                                    'subject_id' => $mataPelajaran->id
                                                ])->first();
                                                if($nilaiAkhir){
                                                    $nilaiAkhir->naf = ($nilaiAkhir->project+$nilaiAkhir->nilaiFormatif()->sum('score'))/($kd->kd+1);
                                                    if($percentages){
                                                        $nar = 0;
                                                        $finalScores = ['naf','nas','ntss','nass'];
                                                        foreach($finalScores as $f){
                                                            $attr = $f.'_percentage';
                                                            $nar += (($percentages->{$attr}/100)*$nilaiAkhir->{$f});
                                                        }
                                                        $nilaiAkhir->nar = $nar;
                                                    }
                                                    else $nilaiAkhir->nar = null;
                                                    $nilaiAkhir->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $kdCount++;
                        }
                    }

                    if($kdCount == count($mataPelajaranList)){
                        Session::flash('success', 'Semua perubahan jumlah TP formatif berhasil disimpan');
                    }
                    elseif($kdCount > 0 && ($kdCount < count($mataPelajaranList))){
                        Session::flash('success', 'Beberapa perubahan jumlah TP formatif berhasil disimpan');
                    }
                    else{
                        Session::flash('danger', 'Ups, sepertinya ada yang tidak beres');
                    }
                }
                else{
                    Session::flash('danger', 'Tidak ada mata pelajaran yang ditemukan');
                }

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);

            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

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

<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\Kurdeka\Deskripsi;
use App\Models\Penilaian\Kurdeka\Hafalan;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class HaditsDoaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'hadits-doa';
        $this->modul = $modul;
        $this->active = 'Hafalan Hadits & Doa';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $mataPelajaran = null, $siswa = null)
    {
        $role = $request->user()->role->name;

        $kategoriList = ['hadits','doa'];
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $kelasList = $mataPelajaranList = $riwayatKelas = $descs = $capaian = null;

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
                    ])->whereIn('curriculum_id',[1,2]);
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
                        ])->whereIn('curriculum_id',[1,2]);
                    })->first();

                    if($kelas){
                        $unit = $kelas->unit()->select('id','name')->first();

                        $subjectName = $unit->id == 1 ? '%Qur\'an%' : '%Agama Islam%';

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', $subjectName)->whereHas('skbmDetail',function($q)use($role,$tahun){
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

                        $mapel = clone $mapelFiltered;
                        $mapel = $mapel;

                        if($mapel->count() > 0){
                            $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                        }

                        if($mataPelajaran){
                            $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                            if($mataPelajaran){
                                // Inti Function
                                $riwayatKelas = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                                foreach($kategoriList as $kategori){
                                    $descs[$kategori] = $mataPelajaran->predicate()->whereHas('RpdType',function($q)use($kategori){
                                        $q->where('rpd_type','Hafalan '.ucwords($kategori));
                                    })->where([
                                        'semester_id' => $semester->id,
                                        'level_id' => $kelas->level_id
                                    ])->when($role == 'guru',function($q){
                                        return $q->where('employee_id', auth()->user()->pegawai->id);
                                    })->get();
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
                                    foreach($riwayatKelas as $r){
                                        $rapor[$r->id] = clone $raporQuery;
                                        $rapor[$r->id] = $rapor[$r->id]->where('student_id',$r->id)->first();
                                        if($rapor[$r->id]){
                                            foreach($kategoriList as $kategori){
                                                $capaian[$r->id][$kategori]['hafalan'] = $rapor[$r->id]->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                                                    $q->where('mem_type',ucwords($kategori));
                                                })->count();
                                                $capaianDeskripsi[$r->id][$kategori] = $rapor[$r->id]->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                                    $q->where('rpd_type','Hafalan '.ucwords($kategori));
                                                })->first();
                                                $capaian[$r->id][$kategori]['desc'] = $capaianDeskripsi[$r->id][$kategori] && $capaianDeskripsi[$r->id][$kategori]->deskripsi ? $capaianDeskripsi[$r->id][$kategori]->deskripsi->description : null;
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
                                        if($rapor[$siswa->id]){
                                            foreach($kategoriList as $kategori){
                                                $capaian[$kategori]['hafalan'] = $rapor[$siswa->id]->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                                                    $q->where('mem_type',ucwords($kategori));
                                                })->get();
                                                $capaian[$kategori]['desc'] = $capaianDeskripsi[$siswa->id][$kategori] ? $capaianDeskripsi[$siswa->id][$kategori]->rpd_id : null;
                                            }
                                        }
                                    }
                                    else{
                                        return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
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
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','kategoriList','semesterList','semester','kelasList','kelas','mataPelajaranList','mataPelajaran','riwayatKelas','siswa','descs','capaian'));
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
    public function update(Request $request, $tahun, $semester, $kelas, $mataPelajaran, $siswa)
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
                ])->whereIn('curriculum_id',[1,2]);
            })->first();

            if($kelas){
                $unit = $kelas->unit()->select('id','name')->first();

                $subjectName = $unit->id == 1 ? '%Qur\'an%' : '%Agama Islam%';

                $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', $subjectName)->whereHas('skbmDetail',function($q)use($role,$tahun){
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

                $mataPelajaranList = null;

                $mapel = clone $mapelFiltered;
                $mapel = $mapel;

                if($mapel->count() > 0){
                    $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                }

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    $siswa = str_replace("-","/",$siswa);
                    $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->where('student_nis', $siswa)->has('identitas')->with('identitas:id,student_name')->first();

                    if($siswa){
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
                            // Inti Function
                            $kategoriList = ['hadits','doa'];

                            $savedCount = 0;
                            $totalCount = 0;
                            foreach($kategoriList as $kategori){
                                if($request->{$kategori}){
                                    $totalCount += count($request->{$kategori});
                                    foreach($request->{$kategori} as $key => $$kategori){
                                        $jenisHafalan = HafalanType::select('id')->where('mem_type',ucwords($kategori))->first();
                                        if($jenisHafalan && $$kategori){
                                            $hafalan = $rapor->hafalanKurdeka()->where([
                                                'order' => $key,
                                            ])->whereHas('jenis',function($q)use($kategori){
                                                $q->where('mem_type',ucwords($kategori));
                                            })->first();
                                            if(!$hafalan){
                                                $hafalan = new Hafalan();
                                                $hafalan->report_score_id = $rapor->id;
                                                $hafalan->mem_type_id = $jenisHafalan->id;
                                                $hafalan->order = $key;
                                                $hafalan->save();
                                                $hafalan->fresh();
                                            }
                                            if($hafalan){
                                                $hafalan->desc = $$kategori;
                                                $hafalan->save();
                                                $savedCount++;
                                            }
                                        }
                                    }
                                    if($rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){$q->where('mem_type',ucwords($kategori));})->count() > count($request->{$kategori})){
                                        $rapor->hafalanKurdeka()->where('order','>=',count($request->{$kategori}))->delete();
                                    }
                                }
                                $descName = $kategori.'Desc';
                                if(isset($request->{$descName})){
                                    $desc = $mataPelajaran->predicate()->whereHas('RpdType',function($q)use($kategori){
                                        $q->where('rpd_type','Hafalan '.ucwords($kategori));
                                    })->where([
                                        'id' => $request->{$descName},
                                        'semester_id' => $semester->id,
                                        'level_id' => $kelas->level_id
                                    ])->when($role == 'guru',function($q){
                                        return $q->where('employee_id', auth()->user()->pegawai->id);
                                    })->first();

                                    if($desc){
                                        $reportDesc = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                                        })->first();
                                        if(!$reportDesc){
                                            $reportDesc = new Deskripsi();
                                            $reportDesc->report_score_id = $rapor->id;
                                            $reportDesc->rpd_type_id = $desc->rpd_type_id;
                                            $reportDesc->rpd_id = $desc->id;
                                            $reportDesc->save();
                                            $reportDesc->fresh();
                                        }
                                        if($reportDesc){
                                            $reportDesc->rpd_id = $desc->id;
                                            $reportDesc->save();
                                        }
                                    }
                                }
                            }
                            if($savedCount > 0 && ($savedCount == $totalCount)){
                                Session::flash('success', 'Semua perubahan capaian hafalan hadits & doa berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < $totalCount)){
                                Session::flash('success', 'Beberapa perubahan capaian hafaln hadits & doa berhasil disimpan');
                            }                    
                            else{
                                Session::flash('danger', 'Tidak dapat menyimpan perubahan capaian hafalan hadits & doa');
                            }
                            return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id, 'siswa' => $siswa->studentNisLink]);

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

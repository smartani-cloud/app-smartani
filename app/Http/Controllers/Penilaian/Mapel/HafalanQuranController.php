<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Alquran\Juz;
use App\Models\Alquran\StatusHafalan;
use App\Models\Alquran\Surat;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\Kurdeka\Deskripsi;
use App\Models\Penilaian\Kurdeka\Quran;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class HafalanQuranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'quran';
        $this->modul = $modul;
        $this->active = 'Hafalan Qur\'an';
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
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $kelasList = $mataPelajaranList = $riwayatKelas = $juz = $surat = $status = $descs = $capaian = null;

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

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
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

                                $descs = $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                                    $q->where('rpd_type','Hafalan');
                                })->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id
                                ])->when($role == 'guru',function($q){
                                    return $q->where('employee_id', auth()->user()->pegawai->id);
                                })->get();

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
                                            $capaian[$r->id]['quran'] = $rapor[$r->id]->quranKurdeka()->count();
                                            $capaianDeskripsi[$r->id] = $rapor[$r->id]->deskripsiKurdeka()->whereHas('jenis',function($q){
                                                $q->where('rpd_type','Hafalan');
                                            })->first();
                                            $capaian[$r->id]['desc'] = $capaianDeskripsi[$r->id] && $capaianDeskripsi[$r->id]->deskripsi ? $capaianDeskripsi[$r->id]->deskripsi->description : null;
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
                                        $juz = Juz::orderBy('id','desc')->get();
                                        $surat = Surat::all();
                                        $status = StatusHafalan::orderBy('id','desc')->get();

                                        if($rapor[$siswa->id]){
                                            $capaian['quran'] = $rapor[$siswa->id]->quranKurdeka()->get();
                                            $capaian['desc'] = $capaianDeskripsi[$siswa->id] ? $capaianDeskripsi[$siswa->id]->rpd_id : null;
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','mataPelajaranList','mataPelajaran','riwayatKelas','siswa','descs','juz','surat','status','capaian'));
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

                $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
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
                            $savedCount = 0;
                            if($request->jenis){
                                foreach($request->jenis as $key => $jenis){
                                    if(in_array($jenis,['surat','juz'])){
                                        $jenisHafalan = HafalanType::select('id')->where('mem_type',ucwords($jenis))->first();
                                        if($jenisHafalan){
                                            $quran = $rapor->quranKurdeka()->where('order',$key)->first();
                                            if(!$quran){
                                                $quran = new Quran();
                                                $quran->report_score_id = $rapor->id;
                                                $quran->mem_type_id = $jenisHafalan->id;
                                                $quran->order = $key;
                                                $quran->save();
                                                $quran->fresh();
                                            }
                                            if($quran){
                                                $juz = isset($request->juz[$key]) && $jenis == 'juz' ? Juz::where('id',$request->juz[$key])->first() : null;
                                                $status = isset($request->status[$key]) && $jenis == 'juz' ? StatusHafalan::where('id',$request->status[$key])->first() : null;
                                                $surat = isset($request->surat[$key]) && $jenis == 'surat' ? Surat::where('id',$request->surat[$key])->first() : null;
                                                $ayat = isset($request->ayat[$key]) && $jenis == 'surat' ? $request->ayat[$key] : null;
                                                
                                                $quran->mem_type_id = $jenisHafalan->id;
                                                $quran->juz_id = $juz ? $juz->id : null;
                                                $quran->surah_id = $surat ? $surat->id : null;
                                                $quran->verse = $ayat ? $ayat : null;
                                                $quran->status_id = $status ? $status->id : null;
                                                $quran->save();
                                                $savedCount++;
                                            }
                                        }
                                    }
                                }
                                if($rapor->quranKurdeka()->count() > count($request->jenis)){
                                    $rapor->quranKurdeka()->where('order','>=',count($request->jenis))->delete();
                                }
                            }
                            if(isset($request->deskripsi)){
                                $desc = $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                                    $q->where('rpd_type','Hafalan');
                                })->where([
                                    'id' => $request->deskripsi,
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id
                                ])->when($role == 'guru',function($q){
                                    return $q->where('employee_id', auth()->user()->pegawai->id);
                                })->first();

                                if($desc){
                                    $reportDesc = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                                        $q->where('rpd_type','Hafalan');
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
                            if($savedCount > 0 && ($savedCount == count($request->jenis))){
                                Session::flash('success', 'Semua perubahan capaian hafalan qur\'an berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < count($request->jenis))){
                                Session::flash('success', 'Beberapa perubahan capaian hafalan qur\'an berhasil disimpan');
                            }                    
                            else{
                                Session::flash('danger', 'Tidak dapat menyimpan perubahan capaian hafalan qur\'an');
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

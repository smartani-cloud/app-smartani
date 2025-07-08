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
use App\Models\Penilaian\Kurdeka\Buku;
use App\Models\Penilaian\Kurdeka\Khatam;
use App\Models\Penilaian\Kurdeka\KhatamBuku;
use App\Models\Penilaian\Kurdeka\KhatamQuran;
use App\Models\Penilaian\Kurdeka\KhatamType;
use App\Models\Penilaian\Kurdeka\UnitBuku;
use App\Models\Penilaian\Kurdeka\Deskripsi;
use App\Models\Penilaian\Kurdeka\Quran;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class KhatamanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'khataman';
        $this->modul = $modul;
        $this->active = 'Khataman';
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

        $kategoriList = ['kelancaran','kebagusan'];
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $kelasList = $mataPelajaranList = $riwayatKelas = $types = $books = $juz = $surat = $status = $descs = $capaian = null;

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

                                $types = KhatamType::whereHas('levels',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'level_id' => $kelas->level_id
                                    ]);
                                })->get();

                                $books = Buku::whereHas('units',function($q)use($semester,$unit){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'unit_id' => $unit->id
                                    ]);
                                })->orderBy('title')->get();

                                foreach($kategoriList as $kategori){
                                    $descs[$kategori] = $mataPelajaran->predicate()->whereHas('RpdType',function($q)use($kategori){
                                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
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
                                            $capaian[$r->id]['type'] = $rapor[$r->id]->khatamKurdeka;
                                            if($capaian[$r->id]['type'] && $capaian[$r->id]['type']->type){
                                                if($capaian[$r->id]['type']->type_id == 1){
                                                    $capaian[$r->id]['quran'] = $rapor[$r->id]->khatamQuran()->count();
                                                }
                                                elseif($capaian[$r->id]['type']->type_id == 2){
                                                    $capaian[$r->id]['buku'] = $rapor[$r->id]->khatamBuku ? $rapor[$r->id]->khatamBuku->book : null;
                                                }
                                            }
                                            foreach($kategoriList as $kategori){
                                                $capaianDeskripsi[$r->id][$kategori] = $rapor[$r->id]->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                                    $q->where('rpd_type',ucwords($kategori).' Tilawah');
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
                                        $juz = Juz::orderBy('id','desc')->get();
                                        $surat = Surat::all();
                                        $status = StatusHafalan::orderBy('id','desc')->get();

                                        if($rapor[$siswa->id]){
                                            $capaian['quran'] = $rapor[$siswa->id]->khatamQuran()->get();
                                            $capaian['type'] = $rapor[$siswa->id]->khatamKurdeka;
                                            if($capaian['type'] && $capaian['type']->type){
                                                if($capaian['type']->type_id == 1){
                                                    $capaian['quran'] = $rapor[$siswa->id]->khatamQuran()->get();
                                                }
                                                elseif($capaian['type']->type_id == 2){
                                                    $capaian['buku'] = $rapor[$siswa->id]->khatamBuku ? $rapor[$siswa->id]->khatamBuku->book_id : null;
                                                }
                                            }
                                            foreach($kategoriList as $kategori){
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

        return view($this->route.'-index', compact('active','route','tahun','kategoriList','semesterList','semester','kelasList','kelas','mataPelajaranList','mataPelajaran','riwayatKelas','siswa','descs','types','books','juz','surat','status','capaian'));
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
                            $kategoriList = ['kelancaran','kebagusan'];

                            if(isset($request->type)){
                                $type = KhatamType::select('id','name')->whereHas('levels',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'level_id' => $kelas->level_id
                                    ]);
                                })->where('id',$request->type)->first();
                                if($type){
                                    if($type->name == "Al-Qur'an"){
                                        if($request->jenis && count($request->jenis) == 2){
                                            foreach($request->jenis as $key => $jenis){
                                                if(in_array($jenis,['surat','juz'])){
                                                    $jenisHafalan = HafalanType::select('id')->where('mem_type',ucwords($jenis))->first();
                                                    if($jenisHafalan){
                                                        $isStart = $key == 0 ? 1 : 0;
                                                        $khatamQuran = $rapor->khatamQuran()->where('is_start',$isStart)->first();
                                                        if(!$khatamQuran){
                                                            $khatamQuran = new KhatamQuran();
                                                            $khatamQuran->report_score_id = $rapor->id;
                                                            $khatamQuran->is_start = $isStart;
                                                            $khatamQuran->mem_type_id = $jenisHafalan->id;
                                                            $khatamQuran->save();
                                                            $khatamQuran->fresh();
                                                        }
                                                        if($khatamQuran){
                                                            $juz = isset($request->juz[$key]) && $jenis == 'juz' ? Juz::where('id',$request->juz[$key])->first() : null;
                                                            $status = isset($request->status[$key]) && $jenis == 'juz' ? StatusHafalan::where('id',$request->status[$key])->first() : null;
                                                            $surat = isset($request->surat[$key]) && $jenis == 'surat' ? Surat::where('id',$request->surat[$key])->first() : null;
                                                            $ayat = isset($request->ayat[$key]) && $jenis == 'surat' ? $request->ayat[$key] : null;

                                                            $khatamQuran->mem_type_id = $jenisHafalan->id;
                                                            $khatamQuran->juz_id = $juz ? $juz->id : null;
                                                            $khatamQuran->surah_id = $surat ? $surat->id : null;
                                                            $khatamQuran->verse = $ayat ? $ayat : null;
                                                            $khatamQuran->status_id = $status ? $status->id : null;
                                                            $khatamQuran->save();
                                                        }
                                                    }
                                                }
                                            }
                                            if($rapor->khatamBuku) $rapor->khatamBuku->delete();
                                        }
                                    }
                                    elseif($type->name == "Buku"){
                                        $book = Buku::whereHas('units',function($q)use($semester,$unit){
                                            $q->where([
                                                'semester_id' => $semester->id,
                                                'unit_id' => $unit->id
                                            ]);
                                        })->where('id',$request->buku)->first();
                                        if($book){
                                            $khatamBuku = $rapor->khatamBuku;
                                            if(!$khatamBuku){
                                                $khatamBuku = new KhatamBuku();
                                                $khatamBuku->report_score_id = $rapor->id;
                                                $khatamBuku->book_id = $book->id;
                                                $khatamBuku->save();
                                                $khatamBuku->fresh();
                                            }
                                            if($khatamBuku){
                                                $khatamBuku->book_id = $book->id;
                                                $khatamBuku->save();
                                            }
                                            if($rapor->khatamQuran()->count() > 0) $rapor->khatamQuran()->delete();
                                        }
                                    }
                                    $reportKhatam = $rapor->khatamKurdeka;
                                    if(!$reportKhatam){
                                        $reportKhatam = new Khatam();
                                        $reportKhatam->report_score_id = $rapor->id;
                                        $reportKhatam->type_id = $type->id;
                                        $reportKhatam->save();
                                        $reportKhatam->fresh();
                                    }
                                    if($reportKhatam){
                                        $reportKhatam->type_id = $type->id;
                                        $reportKhatam->last = $request->last;
                                        $reportKhatam->total = $request->total;
                                        $reportKhatam->percentage = $request->total > 0 ? (($request->last/$request->total)*100) : 0;
                                        $reportKhatam->save();
                                    }
                                }
                            }
                            foreach($kategoriList as $kategori){
                                $descName = $kategori.'Desc';
                                if(isset($request->{$descName})){
                                    $desc = $mataPelajaran->predicate()->whereHas('RpdType',function($q)use($kategori){
                                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                                    })->where([
                                        'id' => $request->{$descName},
                                        'semester_id' => $semester->id,
                                        'level_id' => $kelas->level_id
                                    ])->when($role == 'guru',function($q){
                                        return $q->where('employee_id', auth()->user()->pegawai->id);
                                    })->first();

                                    if($desc){
                                        $reportDesc = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                            $q->where('rpd_type',ucwords($kategori).' Tilawah');
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
                            Session::flash('success', 'Semua perubahan capaian khataman berhasil disimpan');

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

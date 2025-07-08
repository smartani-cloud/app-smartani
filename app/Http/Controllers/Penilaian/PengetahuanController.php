<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiPengetahuan;
use App\Models\Penilaian\NilaiPengetahuanDetail;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class PengetahuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $mataPelajaran = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $mataPelajaranList = null;
        
        $semesterList = Semester::all();

        $riwayatKelas = $jumlahKd = $rpd = $persentase = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->with(['siswa.identitas' => function ($q){$q->orderBy('student_name', 'asc');}])->get();
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        $unit = $kelas->unit()->select('id','name')->first();

                        $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                        if($kelas->major_id){
                            $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                            $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                        }
                        else $kelompok = $kelompok_umum;

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'));

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
                            $mataPelajaranList = $mataPelajaranList->concat($mapelMulok->get());
                        }

                        if($mataPelajaran){
                            $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                            $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                            if($mataPelajaran){
                                // Inti Function
                                $riwayatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name');

                                $jadwal = $kelas->jadwal()->where([
                                    'subject_id' => $mataPelajaran->id,
                                    'level_id' => $kelas->level_id,
                                    'semester_id' => $semester->id
                                ])->first();

                                $jumlahKd = $jadwal ? $mataPelajaran->kd()->select('kd')->where([
                                    'employee_id' => $jadwal->teacher_id,
                                    'level_id' => $kelas->level_id,
                                    'semester_id' => $semester->id
                                ])->pengetahuan()->whereNotNull('kd')->first() : null;

                                $rpd = $jadwal ? $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                                    $q->where('rpd_type','Nilai Pengetahuan');
                                })->where([
                                    'employee_id' => $jadwal->teacher_id,
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id
                                ])->count() : 0;

                                if($riwayatKelas && count($riwayatKelas) > 0 && $jumlahKd && $rpd == 4){
                                    $r = $riwayatKelas->first();
                                    $s = $r ? $r->siswa()->select('id')->first() : null;
                                    $rapor = $s ? $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first() : null;
                                    $persentase = $rapor ? $rapor->pengetahuan()->where('subject_id',$mataPelajaran->id)->first() : null;
                                }
                            }
                        }
                    }
                    else{
                        return redirect()->route('mapel.pengetahuan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('mapel.pengetahuan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('mapel.pengetahuan.index');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->with(['siswa.identitas' => function ($q){$q->orderBy('student_name', 'asc');}])->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('mapel.pengetahuan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('mapel.pengetahuan.index');
            }
        }

        return view('penilaian.'.$role.'.pengetahuan', compact('semesterList', 'semester', 'kelasList', 'kelas', 'mataPelajaranList', 'mataPelajaran', 'riwayatKelas', 'jumlahKd', 'rpd', 'persentase'));
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
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('mapel.pengetahuan.index');

        $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
            if($role == 'guru'){
                $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
            }
            $kelas = $kelas->first();

            if($kelas){
                $unit = $kelas->unit()->select('id','name')->first();

                $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if($kelas->major_id){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                    $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                }
                else $kelompok = $kelompok_umum;

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'));

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
                    $mataPelajaranList = $mataPelajaranList->concat($mapelMulok->get());
                }

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    $riwayatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();

                    $jadwal = $kelas->jadwal()->where([
                        'subject_id' => $mataPelajaran->id,
                        'level_id' => $kelas->level_id,
                        'semester_id' => $semester->id
                    ])->first();

                    $jumlahKd = $jadwal ? $mataPelajaran->kd()->select('kd')->where([
                        'employee_id' => $jadwal->teacher_id,
                        'level_id' => $kelas->level_id,
                        'semester_id' => $semester->id
                    ])->pengetahuan()->whereNotNull('kd')->first() : null;

                    $rpd = $jadwal ? $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                        $q->where('rpd_type','Nilai Pengetahuan');
                    })->where([
                        'employee_id' => $jadwal->teacher_id,
                        'semester_id' => $semester->id,
                        'level_id' => $kelas->level_id
                    ])->count() : 0;

                    if($riwayatKelas && count($riwayatKelas) > 0){
                        if($jumlahKd && $rpd == 4){
                            // Inti Function
                            $pwedit = md5($request->pwedit);

                            $pass = $request->user()->pegawai->verification_password;
                            if($pwedit == $pass){
                                $raporCount = 0;
                                foreach($riwayatKelas as $r){
                                    $s = $r->siswa()->select('id')->first();
                                    $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                    if($rapor){
                                        $examScores = ['pts','pas','project'];
                                        $raporCount++;
                                        $pengetahuan = $rapor->pengetahuan()->where('subject_id',$mataPelajaran->id)->first();
                                        if(!$pengetahuan){
                                            $nilai = new NilaiPengetahuan();
                                            $nilai->score_id = $rapor->id;
                                            $nilai->subject_id = $mataPelajaran->id;
                                            foreach($examScores as $e){
                                                $inputName = 's-'.$s->id.'-'.$e;
                                                $nilai->{$e} = $request->{$inputName};
                                            }
                                            $nilai->precentage_kd = $request->persenkd;
                                            $nilai->precentage_pts = $request->persenpts;
                                            $nilai->precentage_pas = $request->persenpas;
                                            $nilai->precentage_project = $request->persenproject;
                                            
                                            $pengetahuan = $rapor->pengetahuan()->save($nilai);
                                            $pengetahuan->fresh();
                                        }
                                        else{
                                            foreach($examScores as $e){
                                                $inputName = 's-'.$s->id.'-'.$e;
                                                $pengetahuan->{$e} = $request->{$inputName};
                                            }
                                            $pengetahuan->precentage_kd = $request->persenkd;
                                            $pengetahuan->precentage_pts = $request->persenpts;
                                            $pengetahuan->precentage_pas = $request->persenpas;
                                            $pengetahuan->precentage_project = $request->persenproject;
                                            
                                            $pengetahuan->save();
                                            $pengetahuan->fresh();
                                        }
                                        $detail = $pengetahuan->nilaipengetahuandetail();
                                        if($detail->count() > 0){
                                            $detail->delete();
                                        }
                                        for($i = 1; $i <= $jumlahKd->kd; $i++){
                                            $inputName = 's-'.$s->id.'-kd-'.$i;
                                            $score = $request->{$inputName};
                                            if(isset($score) && $score > 0){
                                                $nilai = new NilaiPengetahuanDetail();
                                                $nilai->score = $score;

                                                $pengetahuan->nilaipengetahuandetail()->save($nilai);
                                            }
                                        }
                                        $mean = $pengetahuan->nilaipengetahuandetail()->avg('score');

                                        $nilaiakhir = (($request->persenkd / 100) * $mean) + (($request->persenpts / 100) * $pengetahuan->pts) + (($request->persenpas / 100) * $pengetahuan->pas) + (($request->persenproject / 100) * $pengetahuan->project);

                                        $rangenilai = $mataPelajaran->rangePredikat()->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->first();

                                        if($rangenilai){
                                            if($nilaiakhir >= $rangenilai->range_a){
                                                $predikat = "A";
                                            }
                                            elseif($nilaiakhir >= $rangenilai->range_b){
                                                $predikat = "B";
                                            }
                                            elseif($nilaiakhir >= $rangenilai->range_c){
                                                $predikat = "C";
                                            }
                                            elseif($nilaiakhir >= $rangenilai->range_d){
                                                $predikat = "D";
                                            }
                                        }
                                        else{
                                            if($nilaiakhir >= 85){
                                                $predikat = "A";
                                            }
                                            elseif($nilaiakhir >= 75){
                                                $predikat = "B";
                                            }
                                            elseif($nilaiakhir >= 65){
                                                $predikat = "C";
                                            }
                                            elseif($nilaiakhir >= 0){
                                                $predikat = "D";
                                            }
                                        }

                                        $rpd = $jadwal ? $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                                            $q->where('rpd_type','Nilai Pengetahuan');
                                        })->where([
                                            'employee_id' => $jadwal->teacher_id,
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->where('predicate', 'like', $predikat)->first() : null;

                                        if($rpd){
                                            $pengetahuan->update([
                                                'mean' => $mean,
                                                'score_knowledge' => $nilaiakhir,
                                                'rpd_id' => $rpd->id
                                            ]);
                                        }
                                        else{
                                            $pengetahuan->nilaipengetahuandetail()->delete();
                                        }
                                    }
                                }
                                if($raporCount == count($riwayatKelas)){
                                    Session::flash('success', 'Semua perubahan nilai pengetahuan berhasil disimpan');
                                }
                                elseif($raporCount > 0 && ($raporCount < count($riwayatKelas))){
                                    Session::flash('success', 'Beberapa perubahan nilai pengetahuan berhasil disimpan');
                                }
                                else{
                                    Session::flash('danger', 'Belum ada nilai yang dimasukkan oleh guru mata pelajaran ini');
                                }
                            }
                            else{
                                Session::flash('danger', 'Password Verifikasi Tidak Sesuai! Harap mengisi password dengan benar dan pastikan Anda telah mengkonfigurasi password verifikasi!');
                            }
                        }
                        elseif(!$jumlahKd && $rpd < 4){
                            Session::flash('danger', 'Jumlah NH dan Predikat Pengetahuan Belum Diatur!');
                        }
                        elseif(!$jumlahKd && $rpd == 4){
                            Session::flash('danger', 'Jumlah NH Belum Diatur!');
                        }
                        elseif($jumlahKd && $rpd < 4){
                            Session::flash('danger', 'Predikat Pengetahuan Belum Diatur!');
                        }
                        else{
                            Session::flash('danger', 'Ups, terjadi error!');
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data siswa yang ditemukan');
                    }
                    return redirect()->route('mapel.pengetahuan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route('mapel.pengetahuan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route('mapel.pengetahuan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route('mapel.pengetahuan.index');
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

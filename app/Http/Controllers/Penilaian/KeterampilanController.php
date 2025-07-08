<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiKeterampilan;
use App\Models\Penilaian\NilaiKeterampilanDetail;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class KeterampilanController extends Controller
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

        $riwayatKelas = $jumlahKd = $rpd = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
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
                                ])->keterampilan()->whereNotNull('kd')->first() : null;

                                $rpd = $jadwal ? $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                                    $q->where('rpd_type','Nilai Keterampilan');
                                })->where([
                                    'employee_id' => $jadwal->teacher_id,
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id
                                ])->count() : 0;
                            }
                        }
                    }
                    else{
                        return redirect()->route('mapel.keterampilan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('mapel.keterampilan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('mapel.keterampilan.index');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('mapel.keterampilan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('mapel.keterampilan.index');
            }
        }

        return view('penilaian.'.$role.'.keterampilan', compact('semesterList', 'semester', 'kelasList', 'kelas', 'mataPelajaranList', 'mataPelajaran', 'riwayatKelas', 'jumlahKd', 'rpd'));
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
        if(!$tahun) return redirect()->route('mapel.keterampilan.index');

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
                    ])->keterampilan()->whereNotNull('kd')->first() : null;

                    $rpd = $jadwal ? $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                        $q->where('rpd_type','Nilai Keterampilan');
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
                                        $raporCount++;
                                        $keterampilan = $rapor->keterampilan()->where('subject_id',$mataPelajaran->id)->first();
                                        if(!$keterampilan){
                                            $nilai = new NilaiKeterampilan();
                                            $nilai->score_id = $rapor->id;
                                            $nilai->subject_id = $mataPelajaran->id;
                                            
                                            $keterampilan = $rapor->keterampilan()->save($nilai);
                                            $keterampilan->fresh();
                                        }
                                        $detail = $keterampilan->nilaiketerampilandetail();
                                        if($detail->count() > 0){
                                            $detail->delete();
                                        }
                                        for($i = 1; $i <= $jumlahKd->kd; $i++){
                                            $inputName = 's-'.$s->id.'-kd-'.$i;
                                            $score = $request->{$inputName};
                                            if(isset($score) && $score > 0){
                                                $nilai = new NilaiKeterampilanDetail();
                                                $nilai->score = $score;

                                                $keterampilan->nilaiketerampilandetail()->save($nilai);
                                            }
                                        }
                                        $mean = $keterampilan->nilaiketerampilandetail()->avg('score');

                                        $rangenilai = $mataPelajaran->rangePredikat()->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->first();

                                        if($rangenilai){
                                            if($mean >= $rangenilai->range_a){
                                                $predikat = "A";
                                            }
                                            elseif($mean >= $rangenilai->range_b){
                                                $predikat = "B";
                                            }
                                            elseif($mean >= $rangenilai->range_c){
                                                $predikat = "C";
                                            }
                                            elseif($mean >= $rangenilai->range_d){
                                                $predikat = "D";
                                            }
                                        }
                                        else{
                                            if($mean >= 85){
                                                $predikat = "A";
                                            }
                                            elseif($mean >= 75){
                                                $predikat = "B";
                                            }
                                            elseif($mean >= 65){
                                                $predikat = "C";
                                            }
                                            elseif($mean >= 0){
                                                $predikat = "D";
                                            }
                                        }

                                        $rpd = $jadwal ? $mataPelajaran->predicate()->whereHas('RpdType',function($q){
                                            $q->where('rpd_type','Nilai Keterampilan');
                                        })->where([
                                            'employee_id' => $jadwal->teacher_id,
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->where('predicate', 'like', $predikat)->first() : null;

                                        if($rpd){
                                            $keterampilan->update([
                                                'mean' => $mean,
                                                'rpd_id' => $rpd->id
                                            ]);   
                                        }
                                        else{
                                            $keterampilan->nilaiketerampilandetail()->delete();
                                        }
                                    }
                                }
                                if($raporCount == count($riwayatKelas)){
                                    Session::flash('success', 'Semua perubahan nilai keterampilan berhasil disimpan');
                                }
                                elseif($raporCount > 0 && ($raporCount < count($riwayatKelas))){
                                    Session::flash('success', 'Beberapa perubahan nilai keterampilan berhasil disimpan');
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
                            Session::flash('danger', 'Jumlah NH dan Predikat Keterampilan Belum Diatur!');
                        }
                        elseif(!$jumlahKd && $rpd == 4){
                            Session::flash('danger', 'Jumlah NH Belum Diatur!');
                        }
                        elseif($jumlahKd && $rpd < 4){
                            Session::flash('danger', 'Predikat Keterampilan Belum Diatur!');
                        }
                        else{
                            Session::flash('danger', 'Ups, terjadi error!');
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data siswa yang ditemukan');
                    }
                    return redirect()->route('mapel.keterampilan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route('mapel.keterampilan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route('mapel.keterampilan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route('mapel.keterampilan.index');
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

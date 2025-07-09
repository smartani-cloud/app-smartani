<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiSikap;
use App\Models\Penilaian\RasType;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class SikapController extends Controller
{
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $riwayatKelas = $rpdSpiritual = $rpdSosial = null;
        
        $semesterList = Semester::all();

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
                        // Inti Function
                        $riwayatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();

                        $rpdSpiritual = $kelas->walikelas->predikat()->select('id','predicate')->whereHas('RpdType',function($q){
                            $q->where('rpd_type','Spiritual');
                        })->orderBy('predicate', 'ASC')->get();
                        $rpdSosial = $kelas->walikelas->predikat()->select('id','predicate')->whereHas('RpdType',function($q){
                            $q->where('rpd_type','Sosial');
                        })->orderBy('predicate', 'ASC')->get();
                    }
                    else{
                        return redirect()->route('penilaian.sikap.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('penilaian.sikap.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('penilaian.sikap.index');
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
                    return redirect()->route('penilaian.sikap.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('penilaian.sikap.index');
            }
        }

        return view('penilaian.'.$role.'.sikap', compact('semesterList', 'semester', 'kelasList', 'kelas', 'riwayatKelas', 'rpdSpiritual', 'rpdSosial'));
    }

    public function update(Request $request, $tahun, $semester, $kelas)
    {
        $role = $request->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('penilaian.sikap.index');

        $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
            if($role == 'guru'){
                $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
            }
            $kelas = $kelas->first();

            if($kelas){
                // Inti Function
                $riwayatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();

                $tipePredikat = RasType::select('id','ras_type')->get();

                $rpdSpiritual = $kelas->walikelas->predikat()->select('id','predicate','description')->whereHas('RpdType',function($q){
                    $q->where('rpd_type','Spiritual');
                })->orderBy('predicate', 'ASC')->get();
                $rpdSosial = $kelas->walikelas->predikat()->select('id','predicate','description')->whereHas('RpdType',function($q){
                    $q->where('rpd_type','Sosial');
                })->orderBy('predicate', 'ASC')->get();

                if($riwayatKelas && count($riwayatKelas) > 0){
                    if(($rpdSpiritual && count($rpdSpiritual) > 0) && ($rpdSosial && count($rpdSosial) > 0)){
                        $pwedit = md5($request->pwedit);
                        $pass = $request->user()->pegawai->verification_password;
                        if($pwedit == $pass){
                            $raporCount = 0;
                            foreach($riwayatKelas as $r){
                                $s = $r->siswa()->select('id')->first();
                                $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                if($rapor){
                                    $raporCount++;
                                    $sikapCount = $rapor ? $rapor->sikap()->count() : 0;
                                    foreach($tipePredikat as $t){
                                        $nilai = $sikapCount > 0 ? $rapor->sikap()->where('ras_type_id', $t->id)->first() : null;
                                        $inputName = 's-'.$s->id.'-r-'.$t->id;
                                        $rpd = isset($request->{$inputName}) && strlen($request->{$inputName}) > 0 ? $request->{$inputName} : null;
                                        if($rpd){
                                            $rpdCount = $kelas->walikelas->predikat()->whereHas('RpdType',function($q)use($t){
                                                $q->where('rpd_type',$t->type);
                                            })->where('id',$rpd)->count();
                                            $rpd = $rpdCount > 0 ? $kelas->walikelas->predikat()->where('id',$rpd)->first() : null;
                                        }
                                        if(!$nilai){
                                            $nilai = new NilaiSikap();
                                            $nilai->ras_type_id = $t->id;
                                            $nilai->rpd_id = $rpd ? $rpd['id'] : null;
                                            $nilai->predicate = $rpd ? $rpd['predicate'] : null;
                                            $nilai->description = $rpd ? $rpd['description'] : null;
                                            $rapor->sikap()->save($nilai);
                                        }
                                        else{
                                            if($rpd && ($nilai->rpd_id != $rpd->id)){
                                                $nilai->rpd_id = $rpd['id'];
                                                $nilai->predicate = $rpd['predicate'];
                                                $nilai->description = $rpd['description'];
                                                $nilai->save();
                                            }
                                            elseif(!$rpd){
                                                $nilai->rpd_id = null;
                                                $nilai->predicate = null;
                                                $nilai->description =  null;
                                                $nilai->save();
                                            }
                                        }
                                    }
                                }
                            }
                            if($raporCount == count($riwayatKelas)){
                                Session::flash('success', 'Semua perubahan nilai sikap berhasil disimpan');
                            }
                            elseif($raporCount > 0 && ($raporCount < count($riwayatKelas))){
                                Session::flash('success', 'Beberapa perubahan nilai sikap berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', 'Belum ada nilai yang dimasukkan oleh wali kelas');
                            }
                        }
                        else{
                            Session::flash('danger', 'Password Verifikasi Tidak Sesuai! Harap mengisi password dengan benar dan pastikan Anda telah mengkonfigurasi password verifikasi!');
                        }
                    }
                    elseif((!$rpdSpiritual || count($rpdSpiritual) < 1) && (!$rpdSosial || count($rpdSosial) < 1)){
                        Session::flash('danger', 'Predikat nilai sikap spiritual dan predikat nilai sikap sosial belum diatur!');
                    }
                    elseif(!$rpdSpiritual || count($rpdSpiritual) < 1){
                        Session::flash('danger', 'Predikat nilai sikap spiritual belum diatur!');
                    }
                    elseif(!$rpdSosial || count($rpdSosial) < 1){
                        Session::flash('danger', 'Predikat nilai sikap sosial belum diatur!');
                    }
                    else{
                        Session::flash('danger', 'Ups, terjadi error!');
                    }
                }
                else{
                    Session::flash('danger', 'Tidak ada data siswa yang ditemukan');
                }
                return redirect()->route('penilaian.sikap.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route('penilaian.sikap.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route('penilaian.sikap.index');
        }
    }
}

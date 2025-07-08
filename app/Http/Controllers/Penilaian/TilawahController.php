<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Level;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Siswa\Siswa;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Skbm\Skbm;

// Terpakai
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiTilawah;
use App\Models\Penilaian\Tilawah;
use App\Models\Penilaian\TilawahType;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class TilawahController extends Controller
{
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $skbm = Skbm::aktif()->where('unit_id', auth()->user()->pegawai->unit_id)->first();
        $mapelskbm = $skbm->detail->where('employee_id', $employee_id)->pluck('subject_id');
        $semester_id = session('semester_aktif');
        if (empty($mapelskbm)) {
            $mapel = NULL;
        } else {
            $mapel = MataPelajaran::whereIn('id', $mapelskbm)->get();
        }
        $semester = Semester::where('id', $semester_id)->first();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.tilawah', compact('mapel', 'semester', 'level'));
    }

    public function indexKepsek(Request $request, $tahun = null, $semester = null, $kelas = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $tipeList = $riwayatKelas = null;
        
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
                        $riwayatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name');

                        $tipeList = TilawahType::select('id','tilawah_type')->get();
                    }
                    else{
                        return redirect()->route('penilaian.tilawah.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('penilaian.tilawah.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('penilaian.tilawah.index');
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
                    return redirect()->route('penilaian.tilawah.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('penilaian.tilawah.index');
            }
        }

        return view('penilaian.'.$role.'.tilawah', compact('semesterList', 'semester', 'kelasList', 'kelas', 'tipeList', 'riwayatKelas'));
    }

    public function getkelas(Request $request)
    {
        $jadwal = JadwalPelajaran::where([['level_id', $request->level_id], ['teacher_id', auth()->user()->pegawai->id], ['subject_id', $request->mapel_id], ['semester_id', session('semester_aktif')]])->get();
        if ($jadwal->isEmpty()) {
            $kelas = NULL;
        } else {
            foreach ($jadwal as $jadwals) {
                $idkelas[] = $jadwals->class_id;
            }
        }
        if (isset($idkelas)) {
            $kelas = Kelas::whereIn('id', $idkelas)->get();
        } else {
            $kelas = Kelas::where('id', 0)->get();
        }
        $view = view('penilaian.getlevel', compact('kelas', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswa(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $class_id = $request->class_id;
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')->get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
            $tilawah = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $semester_id]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $semester_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $semester_id]])->first();
                    $counttilawah = Tilawah::where('score_id', $nilairapor->id)->count();
                    if ($counttilawah > 0) {
                        $tilawah[$key] = Tilawah::where('score_id', $nilairapor->id)->with('nilaitilawah')->first();
                    } else {
                        $tilawah[$key] = FALSE;
                    }
                } else {
                    $tilawah[$key] = FALSE;
                }
            }
        }
        $view = view('penilaian.getsiswatilawah', compact('siswa', 'tilawah', 'countrapor', 'validasi'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function store(Request $request)
    {
        $semester_id = session('semester_aktif');
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $iserror = FALSE;

        foreach ($siswa_id as $key => $siswas_id) {
            $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();

                $siswas = Siswa::where('id', $siswas_id)->first();
                $kelas = Kelas::where('id', $siswas->class_id)->first();
                $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                $namawali = $wali->name;
                $nilairapor->hr_name = $namawali;
                $counttilawah = Tilawah::where('score_id', $nilairapor->id)->count();
                if ($nilairapor->update() && $counttilawah == 0) {
                    $tilawah = Tilawah::create([
                        'score_id' => $nilairapor->id
                    ]);
                    if ($tilawah->save()) {
                        $tilawah_id = $tilawah->id;
                        foreach ($request->fieldep as $kunci => $field) {
                            $scoretilawah = NilaiTilawah::create([
                                'tilawah_id' => $tilawah_id,
                                'tilawah_type_id' => $kunci + 1,
                                'score' => 0,
                                'predicate' => $request->{$field}[$key]
                            ]);
                            if ($scoretilawah->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    $tilawah = Tilawah::where('score_id', $nilairapor->id)->first();
                    $tilawah_id = $tilawah->id;
                    $hapusscoretilawah = NilaiTilawah::where('tilawah_id', $tilawah_id)->delete();
                    if ($hapusscoretilawah) {
                        foreach ($request->fieldep as $kunci => $field) {
                            $scoretilawah = NilaiTilawah::create([
                                'tilawah_id' => $tilawah_id,
                                'tilawah_type_id' => $kunci + 1,
                                'score' => 0,
                                'predicate' => $request->{$field}[$key]
                            ]);
                            if ($scoretilawah->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            } else {
                $siswas = Siswa::where('id', $siswas_id)->first();
                $kelas = Kelas::where('id', $siswas->class_id)->first();
                $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                $namawali = $wali->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $nilairapor = NilaiRapor::create([
                    'student_id' => $siswas_id,
                    'semester_id' => $semester_id,
                    'class_id' => $siswas->class_id,
                    'report_status_id' => 0,
                    'report_status_pts_id' => 0,
                    'acc_id' => 0,
                    'unit_id' => $siswas->unit_id,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek
                ]);
                if ($nilairapor->save()) {
                    $tilawah = Tilawah::create([
                        'score_id' => $nilairapor->id
                    ]);
                    if ($tilawah->save()) {
                        $tilawah_id = $tilawah->id;
                        $mean = 0;

                        foreach ($request->fieldep as $kunci => $field) {
                            $scoretilawah = NilaiTilawah::create([
                                'tilawah_id' => $tilawah_id,
                                'tilawah_type_id' => $kunci + 1,
                                'score' => 0,
                                'predicate' => $request->{$field}[$key]
                            ]);
                            if ($scoretilawah->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }


        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaianmapel/nilaitilawah')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaitilawah')->with(['error' => 'Data gagal disimpan']);
        }
    }

    public function update(Request $request, $tahun, $semester, $kelas)
    {
        $role = $request->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('penilaian.tilawah.index');

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

                $tipeList = TilawahType::select('id','tilawah_type')->get();

                if($riwayatKelas && count($riwayatKelas) > 0){
                    if(count($tipeList) > 0){
                        $pwedit = md5($request->pwedit);
                        $pass = $request->user()->pegawai->verification_password;
                        if($pwedit == $pass){
                            $raporCount = 0;
                            foreach($riwayatKelas as $r){
                                $s = $r->siswa()->select('id')->first();
                                $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                if($rapor){
                                    $raporCount++;
                                    $tilawah = $rapor ? $rapor->tilawah : null;
                                    if(!$tilawah){
                                        $tilawah = $rapor->tilawah()->save(new Tilawah());
                                        $tilawah->fresh();
                                    }
                                    foreach($tipeList as $t){
                                        $inputName = 's-'.$s->id.'-t-'.$t->id;
                                        $predicate = isset($request->{$inputName}) && strlen($request->{$inputName}) > 0 ? $request->{$inputName} : null;

                                        $nilai = $tilawah->detail()->where('tilawah_type_id',$t->id)->first();
                                        if(!$nilai){
                                            $nilai = new NilaiTilawah();
                                            $nilai->tilawah_type_id = $t->id;
                                            $nilai->score = 0;
                                            $nilai->predicate = $predicate ? $predicate : '';
                                            $tilawah->detail()->save($nilai);
                                        }
                                        elseif($nilai->predicate != $predicate){
                                            $nilai->predicate = $predicate ? $predicate : '';
                                            $nilai->save();
                                        }
                                    }
                                }
                            }
                            if($raporCount == count($riwayatKelas)){
                                Session::flash('success', 'Semua perubahan nilai tilawah berhasil disimpan');
                            }
                            elseif($raporCount > 0 && ($raporCount < count($riwayatKelas))){
                                Session::flash('success', 'Beberapa perubahan nilai tilawah berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', 'Belum ada nilai yang dimasukkan oleh guru Quran');
                            }
                        }
                        else{
                            Session::flash('danger', 'Password Verifikasi Tidak Sesuai! Harap mengisi password dengan benar dan pastikan Anda telah mengkonfigurasi password verifikasi!');
                        }
                    }
                    else{
                        Session::flash('danger', 'Kompetensi tilawah belum diatur!');
                    }
                }
                else{
                    Session::flash('danger', 'Tidak ada data siswa yang ditemukan');
                }
                return redirect()->route('penilaian.tilawah.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route('penilaian.tilawah.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route('penilaian.tilawah.index');
        }
    }
}

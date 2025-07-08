<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\Ekstra;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Kbm\TahunAjaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Rekrutmen\Pegawai;
use Illuminate\Support\Facades\Auth;


class EkstraController extends Controller
{
    public function index()
    {

        // teacher = employee_id
        $employee_id = Auth::user()->user_id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // check class
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;

        $semester = Semester::where('is_active', 1)->first();
        $smt_aktif = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with(['identitas' => function ($q){$q->select('id','student_name');}])-> get()->sortBy('identitas.student_name')->values();
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 2], ['employee_id', $employee_id]])->orderBy('description', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        return view('penilaian.ekstra', compact('rpd', 'siswa', 'kelas', 'semester'));
    }

    public function getEkstra(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $siswa = Siswa::where('id', $siswa_id)->first();

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 2], ['employee_id', $employee_id]])->orderBy('description', 'ASC')->get();
        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $countrapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $countekstra = Ekstra::where([['score_id', $nilairapor->id]])->count();
            if ($countekstra > 0) {
                $ekstra = Ekstra::where([['score_id', $nilairapor->id]])->get();
            } else {
                $ekstra = FALSE;
            }
        } else {
            $ekstra = FALSE;
        }
        $view = view('penilaian.getekstra')->with('ekstra', $ekstra)->with('siswa_id', $siswa_id)->with('rpd', $rpd)->with('countrapor', $countrapor)->with('validasi', $validasi)->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpan(Request $request)
    {

        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $namawali = auth()->user()->pegawai->name;
            $nilairapor->hr_name = $namawali;
            $countekstra = Ekstra::where('score_id', $nilairapor->id)->count();
            if ($nilairapor->update() && $countekstra > 0) {
                $hapusekstra = Ekstra::where('score_id', $nilairapor->id)->delete();

                if ($hapusekstra) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                }

                if ($request->ekstra) {
                    $deskripsi = $request->deskripsi;
                    foreach ($request->ekstra as $key => $ekstra) {
                        $rpd = PredikatDeskripsi::where('id', $deskripsi[$key])->first();
                        $simpanekstra = Ekstra::create([
                            'score_id' => $nilairapor->id,
                            'extra_name' => $ekstra,
                            'description' => $rpd->description,
                            'rpd_id' => $deskripsi[$key]
                        ]);
                        if ($simpanekstra->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                }
            } else {
                $deskripsi = $request->deskripsi;
                foreach ($request->ekstra as $key => $ekstra) {
                    $rpd = PredikatDeskripsi::where('id', $deskripsi[$key])->first();
                    $simpanekstra = Ekstra::create([
                        'score_id' => $nilairapor->id,
                        'extra_name' => $ekstra,
                        'description' => $rpd->description,
                        'rpd_id' => $deskripsi[$key]
                    ]);
                    if ($simpanekstra->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            }
        } else {
            $siswas = Siswa::where('id', $siswa_id)->first();
            $namawali = auth()->user()->pegawai->name;
            $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->latest()->first();
            $namakepsek = $kepsek->name;
            $nilairapor = NilaiRapor::create([
                'student_id' => $siswa_id,
                'semester_id' => $smt_aktif,
                'class_id' => $siswas->class_id,
                'report_status_id' => 0,
                'acc_id' => 0,
                'unit_id' => $siswas->unit_id,
                'hr_name' => $namawali,
                'hm_name' => $namakepsek
            ]);
            if ($nilairapor->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
            $deskripsi = $request->deskripsi;
            foreach ($request->ekstra as $key => $ekstra) {
                $rpd = PredikatDeskripsi::where('id', $deskripsi[$key])->first();
                $simpanekstra = Ekstra::create([
                    'score_id' => $nilairapor->id,
                    'extra_name' => $ekstra,
                    'description' => $rpd->description,
                    'rpd_id' => $deskripsi[$key]
                ]);
                if ($simpanekstra->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }
        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaian/ekstra')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaian/ekstra')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}

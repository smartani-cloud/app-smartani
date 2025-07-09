<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Prestasi;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\TahunAjaran;
use App\Models\Rekrutmen\Pegawai;

class PrestasiController extends Controller
{
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::where('id', $smt_aktif)->first();

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }

        return view('penilaian.prestasi', compact('siswa', 'kelas', 'semester'));
    }

    public function getPrestasi(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $siswa = Siswa::where('id', $siswa_id)->first();

        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $countrapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $countprestasi = Prestasi::where([['score_id', $nilairapor->id]])->count();
            if ($countprestasi > 0) {
                $prestasi = Prestasi::where([['score_id', $nilairapor->id]])->get();
            } else {
                $prestasi = FALSE;
            }
        } else {
            $prestasi = FALSE;
        }
        $view = view('penilaian.getprestasi')->with('prestasi', $prestasi)->with('siswa', $siswa)->with('countrapor', $countrapor)->with('validasi', $validasi)->render();
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
            $countprestasi = Prestasi::where('score_id', $nilairapor->id)->count();
            if ($nilairapor->update() && $countprestasi > 0) {
                $hapusprestasi = Prestasi::where('score_id', $nilairapor->id)->delete();

                if ($hapusprestasi) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                }
                if ($request->prestasi) {
                    $deskripsi = $request->deskripsi;
                    foreach ($request->prestasi as $key => $prestasi) {
                        $simpanprestasi = Prestasi::create([
                            'score_id' => $nilairapor->id,
                            'achievement_name' => $prestasi,
                            'description' => $deskripsi[$key]
                        ]);
                        if ($simpanprestasi->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                }
            } else {
                $deskripsi = $request->deskripsi;
                foreach ($request->prestasi as $key => $prestasi) {
                    $simpanprestasi = Prestasi::create([
                        'score_id' => $nilairapor->id,
                        'achievement_name' => $prestasi,
                        'description' => $deskripsi[$key]
                    ]);
                    if ($simpanprestasi->save()) {
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
            $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
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
            foreach ($request->prestasi as $key => $prestasi) {
                $simpanprestasi = Prestasi::create([
                    'score_id' => $nilairapor->id,
                    'achievement_name' => $prestasi,
                    'description' => $deskripsi[$key]
                ]);
                if ($simpanprestasi->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }
        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaian/prestasi')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaian/prestasi')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}

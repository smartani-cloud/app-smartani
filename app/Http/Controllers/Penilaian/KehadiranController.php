<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\NilaiRapor;
use Illuminate\Http\Request;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Penilaian\Kehadiran;
use App\Models\Kbm\TahunAjaran;
use App\Models\Rekrutmen\Pegawai;

class KehadiranController extends Controller
{
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::where('is_active', 1)->first();
        $kehadiran = FALSE;
        $countrapor = $validasi = 0;

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
            foreach ($siswa as $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
                    $countkehadiran = Kehadiran::where('score_id', $nilairapor->id)->count();
                    if ($countkehadiran > 0) {
                        $kehadiran[] = Kehadiran::where('score_id', $nilairapor->id)->first();
                    } else {
                        $kehadiran[] = FALSE;
                    }
                } else {
                    $kehadiran[] = FALSE;
                }
            }
        }
        return view('penilaian.kehadiran', compact('siswa', 'kehadiran', 'kelas', 'semester', 'countrapor', 'validasi'));
    }

    public function create(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $hariefektif = $request->hariefektif;
        $sakit = $request->sakit;
        $izin = $request->izin;
        $alpha = $request->alpha;
        $iserror = FALSE;

        foreach ($request->siswa as $key => $siswa) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
            if ($nilairapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswa], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();

                $namawali = auth()->user()->pegawai->name;
                $nilairapor->hr_name = $namawali;
                $kehadiran = Kehadiran::where('score_id', $nilairapor->id)->count();

                if ($nilairapor->update() && $kehadiran > 0) {
                    $kehadiran = Kehadiran::where('score_id', $nilairapor->id)->first();
                    $kehadiran->absent = $alpha[$key];
                    $kehadiran->sick = $sakit[$key];
                    $kehadiran->leave = $izin[$key];
                    $kehadiran->effective_day = $hariefektif[$key];

                    if ($kehadiran->update()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    $kehadiran = Kehadiran::create([
                        'score_id' => $nilairapor->id,
                        'absent' => $alpha[$key],
                        'sick' => $sakit[$key],
                        'leave' => $izin[$key],
                        'effective_day' => $hariefektif[$key],
                    ]);

                    if ($kehadiran->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            } else {
                $siswas = Siswa::where('id', $siswa)->first();
                $namawali = auth()->user()->pegawai->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $nilairapor = NilaiRapor::create([
                    'student_id' => $siswa,
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
                    break;
                }
                $kehadiran = Kehadiran::create([
                    'score_id' => $nilairapor->id,
                    'absent' => $alpha[$key],
                    'sick' => $sakit[$key],
                    'leave' => $izin[$key],
                    'effective_day' => $hariefektif[$key],
                ]);

                if ($kehadiran->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }


        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaian/kehadiran')->with(['sukses' => 'Data berhasil disimpan']);
        } elseif ($iserror == TRUE) {
            return redirect('/kependidikan/penilaian/kehadiran')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}

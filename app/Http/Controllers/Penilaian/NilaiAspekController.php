<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\DeskripsiAspek;
use App\Models\Penilaian\NilaiPTSTK;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PtsTK;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiAspekController extends Controller
{
    public function index()
    {
        // teacher = employee_id
        $employee_id = Auth::user()->pegawai->id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // check class
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;

        $semester = Semester::where('is_active', 1)->first();

        $siswa = Siswa::where('class_id', $class_id)->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        return view('penilaian.nilaiaspek', compact('siswa', 'kelas', 'semester'));
    }

    public function getnilai(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $aspek = AspekPerkembangan::where([['is_deleted', 0]])->get();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;

        foreach ($aspek as $key => $aspeks) {
            $desc = DeskripsiAspek::where([['level_id', $kelas->level_id], ['development_aspect_id', $aspeks->id], ['is_deleted', 0]])->orderBy('predicate', 'ASC')->get();
            if ($desc->isEmpty()) {
                $rpd[$key] = NULL;
            } else {
                $rpd[$key] = $desc;
            }
        }
        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $countnilaiaspek = PtsTK::where([['score_id', $nilairapor->id]])->count();
            if ($countnilaiaspek > 0) {
                $nilaiaspek = PtsTK::where([['score_id', $nilairapor->id]])->with('nilai')->first();
            } else {
                $nilaiaspek = FALSE;
            }
        } else {
            $nilaiaspek = FALSE;
        }
        $view = view('penilaian.getnilaiaspek')->with('aspek', $aspek)->with('nilaiaspek', $nilaiaspek)->with('siswa_id', $siswa_id)->with('rpd', $rpd)->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function store(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $aspek_id = $request->aspek_id;
        $predikat = $request->predikat;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $namawali = auth()->user()->pegawai->name;
            $nilairapor->hr_name = $namawali;
            $countnilaiaspek = PtsTK::where([['score_id', $nilairapor->id]])->with('nilai')->count();
            if ($nilairapor->update() && $countnilaiaspek == 0) {
                $nilaiaspek = PtsTK::create([
                    'score_id' => $nilairapor->id,
                ]);
                if ($nilaiaspek->save()) {
                    $iserror = FALSE;
                    foreach ($aspek_id as $key => $aspek_ids) {
                        $datapredikat = DeskripsiAspek::where('id', $predikat[$key])->first();
                        $detailaspek = NilaiPTSTK::create([
                            'report_mid_kg_id' => $nilaiaspek->id,
                            'development_aspect_id' => $aspek_ids,
                            'aspect_description_id' => $predikat[$key],
                            'predicate' => $datapredikat->predicate,
                            'description' => $datapredikat->description
                        ]);
                        if ($detailaspek->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                } else {
                    $iserror = TRUE;
                }
            } else {
                $nilaiaspek = PtsTK::where('score_id', $nilairapor->id)->with('nilai')->first();
                $detailaspek = $nilaiaspek->nilai;

                foreach ($detailaspek as $detailaspeks) {
                    $detailnilai = NilaiPTSTK::where('id', $detailaspeks->id)->first();
                    $kunci = array_search($detailaspeks->development_aspect_id, $aspek_id);
                    $datapredikat = DeskripsiAspek::where('id', $predikat[$kunci])->first();
                    $detailnilai->aspect_description_id = $predikat[$kunci];
                    $detailnilai->predicate = $datapredikat->predicate;
                    $detailnilai->description = $datapredikat->description;
                    if ($detailnilai->update()) {
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
                'report_status_pts_id' => 0,
                'acc_id' => 0,
                'unit_id' => $siswas->unit_id,
                'hr_name' => $namawali,
                'hm_name' => $namakepsek
            ]);
            if ($nilairapor->save()) {
                $iserror = FALSE;
                $nilaiaspek = PtsTK::create([
                    'score_id' => $nilairapor->id,
                ]);
                if ($nilaiaspek->save()) {
                    $iserror = FALSE;
                    foreach ($aspek_id as $key => $aspek_ids) {
                        $datapredikat = DeskripsiAspek::where('id', $predikat[$key])->first();
                        $detailaspek = NilaiPTSTK::create([
                            'report_mid_kg_id' => $nilaiaspek->id,
                            'development_aspect_id' => $aspek_ids,
                            'aspect_description_id' => $predikat[$key],
                            'predicate' => $datapredikat->predicate,
                            'description' => $datapredikat->description
                        ]);
                        if ($detailaspek->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                } else {
                    $iserror = TRUE;
                }
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiantk/nilaiaspek')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaiantk/nilaiaspek')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}

<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use Illuminate\Http\Request;

use App\Models\Penilaian\Hafalan;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\NilaiHafalan;
use App\Models\Rekrutmen\Pegawai;

class HaditsController extends Controller
{
    public function index()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $agama = MataPelajaran::where([['subject_name', 'like', "%Agama Islam%"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $agama->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
        if ($jadwal->isEmpty()) {
            $level = NULL;
        } else {
            foreach ($jadwal as $jadwals) {
                $idlevel[] = $jadwals->level_id;
            }
        }
        if (isset($idlevel)) {
            $level = Level::whereIn('id', $idlevel)->get();
        } else {
            $level = Level::where('id', 0)->get();
        }
        $semester = Semester::where('id', $semester_id)->first();

        return view('penilaian.nilaihadits', compact('semester', 'agama', 'level'));
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

        $siswa = Siswa::where([['class_id', $class_id]])->with(['identitas' => function ($q){$q->select('id','student_name');}])-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        $view = view('penilaian.getsiswahafalan', compact('siswa'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getHadits(Request $request)
    {
        $class_id = $request->class_id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;

        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $countrapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $counthadits = Hafalan::where([['score_id', $nilairapor->id]])->count();
            if ($counthadits > 0) {
                $hafalan = Hafalan::where('score_id', $nilairapor->id)
                    ->with('nilai', function ($query) {
                        $query->where('mem_type_id', 1);
                    })->first();

                $doa = Hafalan::where('score_id', $nilairapor->id)
                    ->with('nilai', function ($query) {
                        $query->where('mem_type_id', 2);
                    })->first();
            } else {
                $hafalan = FALSE;
                $doa = FALSE;
            }
        } else {
            $hafalan = FALSE;
            $doa = FALSE;
        }
        $view = view('penilaian.gethadits')->with('hadits', $hafalan)->with('siswa_id', $siswa_id)->with('doa', $doa)->with('countrapor', $countrapor)->with('validasi', $validasi)->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function store(Request $request)
    {
        $class_id = $request->class_id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $siswas = Siswa::where('id', $siswa_id)->first();
            $kelas = Kelas::where('id', $siswas->class_id)->first();
            $wali = Pegawai::where('id', $kelas->teacher_id)->first();
            $namawali = $wali->name;
            $nilairapor->hr_name = $namawali;
            $counthadits = Hafalan::where('score_id', $nilairapor->id)->count();
            if ($nilairapor->update() && $counthadits > 0) {
                $hadits = Hafalan::where('score_id', $nilairapor->id)->first();
                $countnilai = NilaiHafalan::where('report_memorize_id', $hadits->id)->count();
                if ($countnilai > 0) {
                    if (isset($request->hadits)) {
                        $cekhadits = NilaiHafalan::where([['report_memorize_id', $hadits->id], ['mem_type_id', 1]])->count();
                        if ($cekhadits == 0) {
                            $predikathadits = $request->predikathadits;
                            foreach ($request->hadits as $key => $haditss) {
                                $simpanhadits = NilaiHafalan::create([
                                    'report_memorize_id' => $hadits->id,
                                    'mem_type_id' => 1,
                                    'hadits_doa' => $haditss,
                                    'predicate' => $predikathadits[$key]
                                ]);
                                if ($simpanhadits->save()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                            }
                        } else {
                            $hapushadits = NilaiHafalan::where([['report_memorize_id', $hadits->id], ['mem_type_id', 1]])->delete();
                            if ($hapushadits) {
                                $predikathadits = $request->predikathadits;
                                foreach ($request->hadits as $key => $haditss) {
                                    $simpanhadits = NilaiHafalan::create([
                                        'report_memorize_id' => $hadits->id,
                                        'mem_type_id' => 1,
                                        'hadits_doa' => $haditss,
                                        'predicate' => $predikathadits[$key]
                                    ]);
                                    if ($simpanhadits->save()) {
                                        $iserror = FALSE;
                                    } else {
                                        $iserror = TRUE;
                                        break;
                                    }
                                }
                            } else {
                                $iserror = TRUE;
                            }
                        }
                    } else {
                        $hapushadits = NilaiHafalan::where([['report_memorize_id', $hadits->id], ['mem_type_id', 1]])->delete();
                        if ($hapushadits) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                        }
                    }
                    if (isset($request->doa)) {
                        $cekdoa = NilaiHafalan::where([['report_memorize_id', $hadits->id], ['mem_type_id', 2]])->count();
                        if ($cekdoa == 0) {
                            $predikatdoa = $request->predikatdoa;
                            foreach ($request->doa as $key => $doas) {
                                $simpandoa = NilaiHafalan::create([
                                    'report_memorize_id' => $hadits->id,
                                    'mem_type_id' => 2,
                                    'hadits_doa' => $doas,
                                    'predicate' => $predikatdoa[$key]
                                ]);
                                if ($simpandoa->save()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                            }
                        } else {
                            $hapusdoa = NilaiHafalan::where([['report_memorize_id', $hadits->id], ['mem_type_id', 2]])->delete();
                            if ($hapusdoa) {
                                $predikatdoa = $request->predikatdoa;
                                foreach ($request->doa as $key => $doas) {
                                    $simpandoa = NilaiHafalan::create([
                                        'report_memorize_id' => $hadits->id,
                                        'mem_type_id' => 2,
                                        'hadits_doa' => $doas,
                                        'predicate' => $predikatdoa[$key]
                                    ]);
                                    if ($simpandoa->save()) {
                                        $iserror = FALSE;
                                    } else {
                                        $iserror = TRUE;
                                        break;
                                    }
                                }
                            } else {
                                $iserror = TRUE;
                            }
                        }
                    } else {
                        $hapusdoa = NilaiHafalan::where([['report_memorize_id', $hadits->id], ['mem_type_id', 2]])->delete();
                        if ($hapusdoa) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                        }
                    }
                    /*$hapushadits = NilaiHafalan::where('report_memorize_id', $hadits->id)->delete();
                    if ($hapushadits) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                    }
                    if ($request->hadits || $request->doa) {
                        $predikathadits = $request->predikathadits;
                        $predikatdoa = $request->predikatdoa;
                        foreach ($request->hadits as $key => $haditss) {
                            $simpanhadits = NilaiHafalan::create([
                                'report_memorize_id' => $hadits->id,
                                'mem_type_id' => 1,
                                'hadits_doa' => $haditss,
                                'predicate' => $predikathadits[$key]
                            ]);
                            if ($simpanhadits->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }

                        foreach ($request->doa as $key => $doas) {
                            $simpandoa = NilaiHafalan::create([
                                'report_memorize_id' => $hadits->id,
                                'mem_type_id' => 2,
                                'hadits_doa' => $doas,
                                'predicate' => $predikatdoa[$key]
                            ]);
                            if ($simpandoa->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    }*/
                } else {
                    if (isset($request->hadits)) {
                        $predikathadits = $request->predikathadits;
                        foreach ($request->hadits as $key => $haditss) {
                            $simpanhadits = NilaiHafalan::create([
                                'report_memorize_id' => $hadits->id,
                                'mem_type_id' => 1,
                                'hadits_doa' => $haditss,
                                'predicate' => $predikathadits[$key]
                            ]);
                            if ($simpanhadits->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    }
                    if (isset($request->doa)) {
                        $predikatdoa = $request->predikatdoa;
                        foreach ($request->doa as $key => $doas) {
                            $simpandoa = NilaiHafalan::create([
                                'report_memorize_id' => $hadits->id,
                                'mem_type_id' => 2,
                                'hadits_doa' => $doas,
                                'predicate' => $predikatdoa[$key]
                            ]);
                            if ($simpandoa->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    }
                }
            } else {
                $hadits = Hafalan::create([
                    'score_id' => $nilairapor->id
                ]);
                if ($hadits->save()) {
                    if (isset($request->hadits)) {
                        $predikathadits = $request->predikathadits;
                        foreach ($request->hadits as $key => $haditss) {
                            $simpanhadits = NilaiHafalan::create([
                                'report_memorize_id' => $hadits->id,
                                'mem_type_id' => 1,
                                'hadits_doa' => $haditss,
                                'predicate' => $predikathadits[$key]
                            ]);
                            if ($simpanhadits->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    }
                    if (isset($request->doa)) {
                        $predikatdoa = $request->predikatdoa;
                        foreach ($request->doa as $key => $doas) {
                            $simpandoa = NilaiHafalan::create([
                                'report_memorize_id' => $hadits->id,
                                'mem_type_id' => 2,
                                'hadits_doa' => $doas,
                                'predicate' => $predikatdoa[$key]
                            ]);
                            if ($simpandoa->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        }
                    }
                } else {
                    $iserror = TRUE;
                }
            }
        } else {
            $siswas = Siswa::where('id', $siswa_id)->first();
            $kelas = Kelas::where('id', $siswas->class_id)->first();
            $wali = Pegawai::where('id', $kelas->teacher_id)->first();
            $namawali = $wali->name;
            $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
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
            } else {
                $iserror = TRUE;
            }
            $hadits = Hafalan::create([
                'score_id' => $nilairapor->id
            ]);
            if ($hadits->save()) {
                if (isset($request->hadits)) {
                    $predikathadits = $request->predikathadits;
                    foreach ($request->hadits as $key => $haditss) {
                        $simpanhadits = NilaiHafalan::create([
                            'report_memorize_id' => $hadits->id,
                            'mem_type_id' => 1,
                            'hadits_doa' => $haditss,
                            'predicate' => $predikathadits[$key]
                        ]);
                        if ($simpanhadits->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                }
                if (isset($request->doa)) {
                    $predikatdoa = $request->predikatdoa;
                    foreach ($request->doa as $key => $doas) {
                        $simpandoa = NilaiHafalan::create([
                            'report_memorize_id' => $hadits->id,
                            'mem_type_id' => 2,
                            'hadits_doa' => $doas,
                            'predicate' => $predikatdoa[$key]
                        ]);
                        if ($simpandoa->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                }
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaianmapel/nilaihadits')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaihadits')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}

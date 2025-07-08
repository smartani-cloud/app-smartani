<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\NilaiPengetahuan;
use App\Models\Penilaian\NilaiPraktekUsp;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\NilaiSKHB;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\SKHB;
use App\Models\Penilaian\SKHBFinal;
use App\Models\Penilaian\TilawahType;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Unit;
use Illuminate\Http\Request;

class SkhbController extends Controller
{
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::orderBy('semester_id', 'ASC')->get();
        $semesteraktif = Semester::where('id', $smt_aktif)->first();
        $siswa = $kelas->riwayat()->select('student_id')->where('semester_id',$semesteraktif->id)->has('siswa')->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name')->pluck('siswa');
        foreach ($siswa as $key => $siswas) {
            $skhb[$key] = SKHB::where([['student_id', $siswas->id], ['academic_year_id', $semesteraktif->academic_year_id]])->first();
        }

        return view('penilaian.refijazah', compact('siswa', 'semester', 'semesteraktif', 'kelas', 'skhb'));
    }

    public function kepsek()
    {
        $semester = Semester::where('is_active', 1)->first();
        $unit = auth()->user()->pegawai->unit->id;
        if ($unit == 2) {
            $level = Level::where('level', 6)->first();
        } elseif ($unit == 3) {
            $level = Level::where('level', 9)->first();
        } elseif ($unit == 4) {
            $level = Level::where('level', 12)->first();
        }
        $kelas = Kelas::where('level_id', $level->id)->get();

        return view('penilaian.refijazah_kepsek', compact('level', 'semester', 'kelas'));
    }

    public function getsiswa(Request $request)
    {
        $semester = Semester::where('id', $request->semester_id)->first();
        $kelas = Kelas::where('id', $request->class_id)->first();
        $siswakelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->has('siswa')->with(['siswa'=>function($q){return $q->select('id');}])->get()->pluck('siswa')->pluck('id');
        $siswa = SKHB::where('unit_id', auth()->user()->pegawai->unit_id)->whereIn('student_id', $siswakelas)->get();
        $view = view('penilaian.getsiswarefijazah', compact('siswa', 'semester'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function validasi(Request $request)
    {
        $class_id = $request->class_id;
        $unit = auth()->user()->pegawai->unit_id;
        $siswa = Siswa::where('class_id', $class_id)->pluck('id');
        $skhb = SKHB::where('unit_id', $unit)->whereIn('student_id', $siswa)->get();
        foreach ($skhb as $skhb) {
            $skhb->report_status_id = 1;
            $skhb->acc_id = auth()->user()->pegawai->id;
            if ($skhb->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
                break;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/ijazahkepsek/refijazah')->with(['sukses' => 'Referensi Ijazah berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/ijazahkepsek/refijazah')->with(['error' => 'Referensi Ijazah gagal divalidasi']);
        }
    }

    public function lihatnilai(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $skhb = $siswa->skhb()->where([['student_id', $id], ['unit_id', $unit->id]])->first();
                $nilaiskhb = SKHB::where([['student_id', $id], ['unit_id', $unit->id]])->first();

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $request->major_id)->get();
                $kelompok = $kelompok_umum->concat($kelompok_peminatan);

                $mata_pelajaran = MataPelajaran::whereIn('group_subject_id', $kelompok->pluck('id'))->count();

                $total_rows = $kelompok->count() + $mata_pelajaran;
                return view('penilaian.nilai_refijazah', compact('siswa', 'unit', 'semester', 'skhb', 'nilaiskhb', 'kelompok', 'total_rows'));
            }
        }

        if (isset($request->wali)) {
            return redirect('/kependidikan/refijazah');
        } else {
            return redirect('/kependidikan/ijazahkepsek/refijazah');
        }
    }

    public function validasisiswa($id)
    {
        $unit = auth()->user()->pegawai->unit_id;
        $siswa = Siswa::where('id', $id)->pluck('id');
        $skhb = SKHB::where('unit_id', $unit)->where('student_id', $siswa)->first();
        if ($skhb) {
            $skhb->report_status_id = 1;
            $skhb->acc_id = auth()->user()->pegawai->id;
            if ($skhb->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $iserror = TRUE;
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/ijazahkepsek/refijazah')->with(['sukses' => 'Referensi Ijazah berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/ijazahkepsek/refijazah')->with(['error' => 'Referensi Ijazah gagal divalidasi']);
        }
    }

    public function regenerate($id)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::orderBy('semester_id', 'ASC')->get();
        $semesteraktif = Semester::where('id', $smt_aktif)->first();
        $siswa = Siswa::where('id', $id)->first();
        $skhb = SKHB::where([['student_id', $id], ['academic_year_id', $semesteraktif->academic_year_id]])->first();

        return view('penilaian.regenerate', compact('siswa', 'semester', 'semesteraktif', 'kelas', 'skhb'));
    }

    public function regenerate_simpan(Request $request)
    {
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $semesteraktif = Semester::where('id', session('semester_aktif'))->first();
        $semester_id = $request->semester;
        $nilaiakhir = $request->nilai_akhir;
        $nilaipraktek = $request->nilai_praktek;
        $nilaiusp = $request->nilai_usp;
        $siswa = Siswa::where('id', $siswa_id)->first();
        $iserror = FALSE;

        if ($siswa) {
            $countskhb = SKHB::where('student_id', $siswa->id)->count();
            if ($countskhb == 0) {
                $wali = Pegawai::where('id', $siswa->kelas->teacher_id)->first();
                $namawali = $wali->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $skhb = SKHB::create([
                    'student_id' => $siswa->id,
                    'academic_year_id' => $semesteraktif->academic_year_id,
                    'unit_id' => $siswa->kelas->unit_id,
                    'report_status_id' => 0,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek,
                    'percentage_report' => $nilaiakhir,
                    'percentage_practice' => $nilaipraktek,
                    'percentage_usp' => $nilaiusp,
                ]);
                if ($skhb->save()) {
                    $idrapor = NilaiRapor::where('student_id', $siswa->id)->whereIn('semester_id', $semester_id)->pluck('id');
                    if (count($idrapor) != count($semester_id)) {
                        $nilaikurang[] = $siswa->identitas->student_name;
                    }
                    $subject_id = NilaiPengetahuan::whereIn('score_id', $idrapor)->pluck('subject_id');
                    foreach ($subject_id as $subject_ids) {
                        $avg = 0;
                        $nilaipengetahuan = NilaiPengetahuan::where('subject_id', $subject_ids)->whereIn('score_id', $idrapor)->avg('score_knowledge');
                        $skhbrapor = NilaiSKHB::create([
                            'skhb_id' => $skhb->id,
                            'subject_id' => $subject_ids,
                            'score' => round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP),
                            'skhb_score_type_id' => 1
                        ]);
                        $avg = $avg + (round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP) * ($nilaiakhir / 100));
                        $cekpraktek = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->count();
                        $cekusp = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->count();
                        if ($cekpraktek > 0) {
                            $npraktek =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->first();
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($npraktek->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + (round($npraktek->score, 0, PHP_ROUND_HALF_UP) * ($nilaipraktek / 100));
                        } else {
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($cekusp > 0) {
                            $nusp =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->first();
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($nusp->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + (round($nusp->score, 0, PHP_ROUND_HALF_UP) * ($nilaiusp / 100));
                        } else {
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($skhbrapor->save() && $skhbpraktek->save() && $skhbusp->save()) {
                            $nilaiskhb = NilaiSKHB::where([['skhb_id', $skhb->id], ['subject_id', $subject_ids]])->get();
                            foreach ($nilaiskhb as $nilaiskhbs) {
                                $nilaiskhbs->avg = round($avg, 0, PHP_ROUND_HALF_UP);;
                                if ($nilaiskhbs->update()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                            }
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
                $skhb = SKHB::where('student_id', $siswa->id)->first();
                $skhb->percentage_report = $nilaiakhir;
                $skhb->percentage_practice = $nilaipraktek;
                $skhb->percentage_usp = $nilaiusp;
                $nilaiskhb = NilaiSKHB::where('skhb_id', $skhb->id)->delete();
                if ($skhb->update() && $nilaiskhb) {
                    $idrapor = NilaiRapor::where('student_id', $siswa->id)->whereIn('semester_id', $semester_id)->pluck('id');
                    if (count($idrapor) != count($semester_id)) {
                        $nilaikurang[] = $siswa->identitas->student_name;
                    }
                    $subject_id = NilaiPengetahuan::whereIn('score_id', $idrapor)->pluck('subject_id');
                    foreach ($subject_id as $subject_ids) {
                        $avg = 0;
                        $nilaipengetahuan = NilaiPengetahuan::where('subject_id', $subject_ids)->whereIn('score_id', $idrapor)->avg('score_knowledge');
                        $skhbrapor = NilaiSKHB::create([
                            'skhb_id' => $skhb->id,
                            'subject_id' => $subject_ids,
                            'score' => round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP),
                            'skhb_score_type_id' => 1
                        ]);
                        $avg = $avg + (round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP) * ($nilaiakhir / 100));
                        $cekpraktek = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->count();
                        $cekusp = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->count();
                        if ($cekpraktek > 0) {
                            $npraktek =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->first();
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($npraktek->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + (round($npraktek->score, 0, PHP_ROUND_HALF_UP) * ($nilaipraktek / 100));
                        } else {
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($cekusp > 0) {
                            $nusp =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswa->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->first();
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($nusp->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + (round($nusp->score, 0, PHP_ROUND_HALF_UP) * ($nilaiusp / 100));
                        } else {
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($skhbrapor->save() && $skhbpraktek->save() && $skhbusp->save()) {
                            $nilaiskhb = NilaiSKHB::where([['skhb_id', $skhb->id], ['subject_id', $subject_ids]])->get();
                            foreach ($nilaiskhb as $nilaiskhbs) {
                                $nilaiskhbs->avg = round($avg, 0, PHP_ROUND_HALF_UP);
                                if ($nilaiskhbs->update()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                            }
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
        }

        if ($iserror) {
            return redirect('/kependidikan/refijazah')->with(['error' => 'Data gagal disimpan']);
        } else {
            if (isset($nilaikurang)) {
                $kurang = 'Terdapat siswa yang belum memiliki nilai semester lengkap sesuai dengan range yang terpilih, yaitu:';
                $kurang .= '<ol style="margin-bottom : 0;">';
                foreach ($nilaikurang as $nk) {
                    $kurang .= '<li>' . $nk . '</li>';
                }
                $kurang .= '</ol>';
                return redirect('/kependidikan/refijazah')->with(['kurang' => $kurang]);
            } else {
                return redirect('/kependidikan/refijazah')->with(['sukses' => 'Data berhasil disimpan']);
            }
        }
    }

    public function generate(Request $request)
    {
        $class_id = $request->class_id;
        $semesteraktif = Semester::where('id', session('semester_aktif'))->first();
        $semester_id = $request->semester;
        $nilaiakhir = $request->nilai_akhir;
        $nilaipraktek = $request->nilai_praktek;
        $nilaiusp = $request->nilai_usp;
        $siswa = Siswa::where('class_id', $class_id)->get();

        foreach ($siswa as $siswas) {
            $countskhb = SKHB::where('student_id', $siswas->id)->count();
            if ($countskhb == 0) {
                $wali = Pegawai::where('id', $siswas->kelas->teacher_id)->first();
                $namawali = $wali->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $skhb = SKHB::create([
                    'student_id' => $siswas->id,
                    'academic_year_id' => $semesteraktif->academic_year_id,
                    'unit_id' => $siswas->kelas->unit_id,
                    'report_status_id' => 0,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek,
                    'percentage_report' => $nilaiakhir,
                    'percentage_practice' => $nilaipraktek,
                    'percentage_usp' => $nilaiusp,
                ]);
                if ($skhb->save()) {
                    $idrapor = NilaiRapor::where('student_id', $siswas->id)->whereIn('semester_id', $semester_id)->pluck('id');
                    if (count($idrapor) != count($semester_id)) {
                        $nilaikurang[] = $siswas->identitas->student_name;
                    }
                    $subject_id = NilaiPengetahuan::whereIn('score_id', $idrapor)->pluck('subject_id');
                    foreach ($subject_id as $subject_ids) {
                        $avg = 0;
                        $nilaipengetahuan = NilaiPengetahuan::where('subject_id', $subject_ids)->whereIn('score_id', $idrapor)->avg('score_knowledge');
                        $skhbrapor = NilaiSKHB::create([
                            'skhb_id' => $skhb->id,
                            'subject_id' => $subject_ids,
                            'score' => round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP),
                            'skhb_score_type_id' => 1
                        ]);
                        $avg = $avg + (round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP) * ($nilaiakhir / 100));
                        $cekpraktek = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->count();
                        $cekusp = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->count();
                        if ($cekpraktek > 0) {
                            $npraktek =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->first();
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($npraktek->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + (round($npraktek->score, 0, PHP_ROUND_HALF_UP) * ($nilaipraktek / 100));
                        } else {
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($cekusp > 0) {
                            $nusp =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->first();
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($nusp->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + (round($nusp->score, 0, PHP_ROUND_HALF_UP) * ($nilaiusp / 100));
                        } else {
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($skhbrapor->save() && $skhbpraktek->save() && $skhbusp->save()) {
                            $nilaiskhb = NilaiSKHB::where([['skhb_id', $skhb->id], ['subject_id', $subject_ids]])->get();
                            foreach ($nilaiskhb as $nilaiskhbs) {
                                $nilaiskhbs->avg = round($avg, 0, PHP_ROUND_HALF_UP);;
                                if ($nilaiskhbs->update()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                            }
                            $iserror = FALSE;
                        } else {
                            $breakout = TRUE;
                            $iserror = TRUE;
                            break;
                        }
                    }
                } else {
                    $iserror = TRUE;
                    break;
                }
            } else {
                $skhb = SKHB::where('student_id', $siswas->id)->first();
                $skhb->percentage_report = $nilaiakhir;
                $skhb->percentage_practice = $nilaipraktek;
                $skhb->percentage_usp = $nilaiusp;
                $nilaiskhb = NilaiSKHB::where('skhb_id', $skhb->id)->delete();
                if ($skhb->update() && $nilaiskhb) {
                    $idrapor = NilaiRapor::where('student_id', $siswas->id)->whereIn('semester_id', $semester_id)->pluck('id');
                    if (count($idrapor) != count($semester_id)) {
                        $nilaikurang[] = $siswas->identitas->student_name;
                    }
                    $subject_id = NilaiPengetahuan::whereIn('score_id', $idrapor)->pluck('subject_id');
                    foreach ($subject_id as $subject_ids) {
                        $avg = 0;
                        $nilaipengetahuan = NilaiPengetahuan::where('subject_id', $subject_ids)->whereIn('score_id', $idrapor)->avg('score_knowledge');
                        $skhbrapor = NilaiSKHB::create([
                            'skhb_id' => $skhb->id,
                            'subject_id' => $subject_ids,
                            'score' => round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP),
                            'skhb_score_type_id' => 1
                        ]);
                        $avg = $avg + (round($nilaipengetahuan, 0, PHP_ROUND_HALF_UP) * ($nilaiakhir / 100));
                        $cekpraktek = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->count();
                        $cekusp = NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->count();
                        if ($cekpraktek > 0) {
                            $npraktek =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 1]])->first();
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($npraktek->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + (round($npraktek->score, 0, PHP_ROUND_HALF_UP) * ($nilaipraktek / 100));
                        } else {
                            $skhbpraktek = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 2
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($cekusp > 0) {
                            $nusp =  NilaiPraktekUsp::where([['subject_id', $subject_ids], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')], ['class_id', $class_id], ['type', 2]])->first();
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => round($nusp->score, 0, PHP_ROUND_HALF_UP),
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + (round($nusp->score, 0, PHP_ROUND_HALF_UP) * ($nilaiusp / 100));
                        } else {
                            $skhbusp = NilaiSKHB::create([
                                'skhb_id' => $skhb->id,
                                'subject_id' => $subject_ids,
                                'score' => 0,
                                'skhb_score_type_id' => 3
                            ]);
                            $avg = $avg + 0;
                        }
                        if ($skhbrapor->save() && $skhbpraktek->save() && $skhbusp->save()) {
                            $nilaiskhb = NilaiSKHB::where([['skhb_id', $skhb->id], ['subject_id', $subject_ids]])->get();
                            foreach ($nilaiskhb as $nilaiskhbs) {
                                $nilaiskhbs->avg = round($avg, 0, PHP_ROUND_HALF_UP);
                                if ($nilaiskhbs->update()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                            }
                            $iserror = FALSE;
                        } else {
                            $breakout = TRUE;
                            $iserror = TRUE;
                            break;
                        }
                    }
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
            if (isset($breakout)) {
                break;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/refijazah')->with(['error' => 'Data gagal disimpan']);
        } else {
            if (isset($nilaikurang)) {
                $kurang = 'Terdapat siswa yang belum memiliki nilai semester lengkap sesuai dengan range yang terpilih, yaitu:';
                $kurang .= '<ol style="margin-bottom : 0;">';
                foreach ($nilaikurang as $nk) {
                    $kurang .= '<li>' . $nk . '</li>';
                }
                $kurang .= '</ol>';
                return redirect('/kependidikan/refijazah')->with(['kurang' => $kurang]);
            } else {
                return redirect('/kependidikan/refijazah')->with(['sukses' => 'Data berhasil disimpan']);
            }
        }
    }
}

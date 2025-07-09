<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\RaporPas;
use App\Models\Penilaian\IndikatorIklas;
use App\Models\Penilaian\IndikatorIklasDetail;
use App\Models\Penilaian\PasTK;
use App\Models\Penilaian\RefIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Skbm\Skbm;

class PenilaianController extends Controller
{
    public function nilaisikap()
    {
        return view('penilaian.nilaisikap');
    }

    public function predikatsikap()
    {
        return view('penilaian.predikatsikap');
    }

    public function kehadiran()
    {
        return view('penilaian.kehadiran');
    }

    public function cetakpts()
    {
        $siswa = Siswa::find(10);
        return view('penilaian.cetakpts', compact('siswa'));
    }

    public function coverpts()
    {
        return view('cetakcoverpts');
    }

    public function descpts()
    {
        return view('penilaian.descpts');
    }

    public function deskripsipts()
    {
        return view('penilaian.deskripsipts');
    }

    public function cetakpas()
    {
        $siswa = Siswa::find(10);
        return view('penilaian.cetakpas', compact('siswa'));
    }

    public function iklas()
    {
        return view('penilaian.iklas');
    }

    public function ekstra()
    {
        return view('penilaian.ekstra');
    }

    public function deskripsiekstra()
    {
        return view('penilaian.deskripsiekstra');
    }

    public function prestasi()
    {
        return view('penilaian.prestasi');
    }

    public function catatanwali()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $semester_id = session('semester_aktif');
        $semester = Semester::where('id', $semester_id)->first();
        $pas = FALSE;
        $countrapor = $validasi = 0;

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    if ($kelas->unit_id == 1) {
                        $countpas = PasTK::where('score_id', $nilairapor->id)->count();
                        if ($countpas > 0) {
                            $pas[$key] = PasTK::where('score_id', $nilairapor->id)->first();
                        } else {
                            $pas = FALSE;
                        }
                    } else {
                        $countpas = RaporPas::where('score_id', $nilairapor->id)->count();
                        if ($countpas > 0) {
                            $pas[$key] = RaporPas::where('score_id', $nilairapor->id)->first();
                        } else {
                            $pas = FALSE;
                        }
                    }
                }
            }
        }
        return view('penilaian.catatanwali', compact('siswa', 'pas', 'kelas', 'semester', 'countrapor', 'validasi'));
    }

    public function simpancatatan(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $semester_id = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $notes = $request->notes;
        if (isset($request->naik)) {
            $naik = 1;
        }
        $iserror = FALSE;

        foreach ($siswa_id as $key => $siswaid) {
            $countrapor = NilaiRapor::where([['student_id', $siswaid], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswaid], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                $namawali = auth()->user()->pegawai->name;
                $nilairapor->hr_name = $namawali;
                if ($nilairapor->update() && $kelas->unit_id == 1) {
                    $countpas = PasTK::where('score_id', $nilairapor->id)->count();
                } else {
                    $countpas = RaporPas::where('score_id', $nilairapor->id)->count();
                }
                if ($countpas > 0) {
                    if ($kelas->unit_id == 1) {
                        $pas = PasTK::where('score_id', $nilairapor->id)->first();
                    } else {
                        $pas = RaporPas::where('score_id', $nilairapor->id)->first();
                    }
                    if (isset($naik)) {
                        $kenaikan = $request->kenaikan;
                        $pas->conclusion = $kenaikan[$key];
                    } else {
                        $pas->notes = $notes[$key];
                    }
                    if ($pas->update()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    if (isset($naik)) {
                        $kenaikan = $request->kenaikan;
                        if ($kelas->unit_id == 1) {
                            $pas = PasTK::create([
                                'score_id' => $nilairapor->id,
                                'conclusion' => $kenaikan[$key]
                            ]);
                        } else {
                            $pas = RaporPas::create([
                                'score_id' => $nilairapor->id,
                                'conclusion' => $kenaikan[$key]
                            ]);
                        }
                    } else {
                        if ($kelas->unit_id == 1) {
                            $pas = PasTK::create([
                                'score_id' => $nilairapor->id,
                                'notes' => $notes[$key]
                            ]);
                        } else {
                            $pas = RaporPas::create([
                                'score_id' => $nilairapor->id,
                                'notes' => $notes[$key]
                            ]);
                        }
                    }

                    if ($pas->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            } else {
                $siswas = Siswa::where('id', $siswaid)->first();
                $namawali = auth()->user()->pegawai->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $nilairapor = NilaiRapor::create([
                    'student_id' => $siswaid,
                    'semester_id' => $semester_id,
                    'class_id' => $siswas->class_id,
                    'report_status_id' => 0,
                    'acc_id' => 0,
                    'unit_id' => $siswas->unit_id,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek
                ]);
                if ($nilairapor->save()) {
                    if (isset($naik)) {
                        $kenaikan = $request->kenaikan;
                        if ($kelas->unit_id == 1) {
                            $pas = PasTK::create([
                                'score_id' => $nilairapor->id,
                                'conclusion' => $kenaikan[$key]
                            ]);
                        } else {
                            $pas = RaporPas::create([
                                'score_id' => $nilairapor->id,
                                'conclusion' => $kenaikan[$key]
                            ]);
                        }
                    } else {
                        if ($kelas->unit_id == 1) {
                            $pas = PasTK::create([
                                'score_id' => $nilairapor->id,
                                'notes' => $notes[$key]
                            ]);
                        } else {
                            $pas = RaporPas::create([
                                'score_id' => $nilairapor->id,
                                'notes' => $notes[$key]
                            ]);
                        }
                    }

                    if ($pas->save()) {
                        $iserror = FALSE;
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

        if ($iserror) {
            if (isset($naik)) {
                return redirect('/kependidikan/penilaian/kenaikankelas')->with(['error' => 'Data gagal disimpan']);
            } else {
                return redirect('/kependidikan/penilaian/catatanwali')->with(['error' => 'Data gagal disimpan']);
            }
        } else {
            if (isset($naik)) {
                return redirect('/kependidikan/penilaian/kenaikankelas')->with(['sukses' => 'Data berhasil disimpan']);
            } else {
                return redirect('/kependidikan/penilaian/catatanwali')->with(['sukses' => 'Data berhasil disimpan']);
            }
        }
    }

    public function kenaikankelas()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $semester_id = session('semester_aktif');
        $semester = Semester::where('id', $semester_id)->first();
        $pas = FALSE;
        $countrapor = $validasi = 0;

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    if ($kelas->unit_id == 1) {
                        $countpas = PasTK::where('score_id', $nilairapor->id)->count();
                    } else {
                        $countpas = RaporPas::where('score_id', $nilairapor->id)->count();
                    }
                    if ($countpas > 0) {
                        if ($kelas->unit_id == 1) {
                            $pas[$key] = PasTK::where('score_id', $nilairapor->id)->first();
                        } else {
                            $pas[$key] = RaporPas::where('score_id', $nilairapor->id)->first();
                        }
                    } else {
                        $pas = FALSE;
                    }
                }
            }
        }

        return view('penilaian.kenaikankelas', compact('siswa', 'pas', 'semester', 'kelas', 'countrapor', 'validasi'));
    }

    public function kdsetting()
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $skbm = Skbm::aktif()->where('unit_id', auth()->user()->pegawai->unit_id)->first();
        $mapelskbm = $skbm->detail->where('employee_id', $employee_id)->pluck('subject_id');
        if (empty($mapelskbm)) {
            $mapel = NULL;
        } else {
            $mapel = MataPelajaran::whereIn('id', $mapelskbm)->where('subject_name', 'not like', "Qur'an")->get();
        }
        $semester = Semester::where('id', $semester_id)->first();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.kdsetting', compact('semester', 'mapel', 'level'));
    }

    public function kdlevel(Request $request)
    {
        $semester_id = session('semester_aktif');
        $jadwal = JadwalPelajaran::where([['subject_id', $request->mapel_id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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
        $view = view('penilaian.getlevelkd', compact('level', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getkd(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $countkd = KDSetting::where([['subject_id', $request->mapel_id], ['level_id', $request->level_id], ['employee_id', $employee_id], ['semester_id', $semester_id]])->count();
        if ($countkd == 0) {
            $kd1 = FALSE;
            $kd2 = FALSE;
        } else {
            $kd = KDSetting::where([['subject_id', $request->mapel_id], ['level_id', $request->level_id], ['employee_id', $employee_id], ['semester_id', $semester_id]])->get();
            foreach ($kd as $key => $kds) {
                if ($kds->kd_type_id == 1) {
                    $kd1 = $kds->kd;
                } else {
                    $kd2 = $kds->kd;
                }
            }
        }
        $view = view('penilaian.getkd', compact('kd1', 'kd2'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function kdsimpan(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $mapel_id = $request->mapel;
        $level_id = $request->kelas;
        $semester_id = session('semester_aktif');
        $iserror = FALSE;

        $countkd = KDSetting::where([['subject_id', $mapel_id], ['level_id', $level_id], ['employee_id', $employee_id], ['semester_id', $semester_id]])->count();
        if ($countkd > 0) {
            $kd = KDSetting::where([['subject_id', $mapel_id], ['level_id', $level_id], ['employee_id', $employee_id], ['semester_id', $semester_id]])->get();
            foreach ($kd as $kds) {
                if ($kds->kd_type_id == 1) {
                    $kds->kd = $request->kd1;
                } elseif ($kds->kd_type_id == 2) {
                    $kds->kd = $request->kd2;
                }
                if ($kds->update()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        } else {
            $kd1 = KDSetting::create([
                'semester_id' => $semester_id,
                'level_id' => $level_id,
                'subject_id' => $mapel_id,
                'kd' => $request->kd1,
                'kd_type_id' => 1,
                'employee_id' => $employee_id
            ]);

            $kd2 = KDSetting::create([
                'semester_id' => $semester_id,
                'level_id' => $level_id,
                'subject_id' => $mapel_id,
                'kd' => $request->kd2,
                'kd_type_id' => 2,
                'employee_id' => $employee_id
            ]);

            if ($kd1->save() && $kd2->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/kdsetting')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/kdsetting')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function indikatoriklas()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $semester_id = session('semester_aktif');
        $semester = Semester::where('id', $semester_id)->first();
        $level_id = $kelas->level_id;
        $unit_id = $kelas->unit_id;

        $indikator_id = IndikatorIklas::where([['unit_id', $unit_id], ['level_id', $level_id]])->with('detail.ref')->first();
        if ($indikator_id) {
            $indikator = IndikatorIklasDetail::where('iklas_indicator_id', $indikator_id->id)->with('ref')->get();
        } else {
            $indikator = FALSE;
        }
        $refiklas = RefIklas::all();

        return view('penilaian.indikatoriklas', compact('indikator', 'refiklas'));
    }

    public function tambahindikator(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $semester_id = session('semester_aktif');
        $semester = Semester::where('id', $semester_id)->first();
        $level_id = $kelas->level_id;
        $unit_id = $kelas->unit_id;

        $countindikator = IndikatorIklas::where([['unit_id', $unit_id], ['level_id', $level_id]])->with('detail.ref')->count();
        if ($countindikator == 0) {
            $buatindikator = IndikatorIklas::create([
                'level_id' => $level_id,
                'unit_id' => $unit_id
            ]);
            if ($buatindikator->save()) {
                $detail = IndikatorIklasDetail::create([
                    'iklas_indicator_id' => $buatindikator->id,
                    'iklas_ref_id' => $request->refiklas,
                    'indicator' => $request->indikator
                ]);
                if ($detail->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                }
            } else {
                $iserror = TRUE;
            }
        } else {
            $indikator = IndikatorIklas::where([['unit_id', $unit_id], ['level_id', $level_id]])->with('detail.ref')->first();
            $detail = IndikatorIklasDetail::create([
                'iklas_indicator_id' => $indikator->id,
                'iklas_ref_id' => $request->refiklas,
                'indicator' => $request->indikator
            ]);
            if ($detail->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaian/indikatoriklas')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaian/indikatoriklas')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function ubahindikator(Request $request)
    {
        $detail = IndikatorIklasDetail::where('id', $request->id)->first();
        $detail->indicator = $request->indikator;
        if ($detail->update()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaian/indikatoriklas')->with(['error' => 'Data gagal diubah']);
        } else {
            return redirect('/kependidikan/penilaian/indikatoriklas')->with(['sukses' => 'Data berhasil diubah']);
        }
    }

    public function hapusindikator(Request $request)
    {
        $query = IndikatorIklasDetail::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaian/indikatoriklas')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaian/indikatoriklas')->with(['error' => 'Data gagal dihapus']);
        }
    }
}

<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\NilaiKeterampilan;
use App\Models\Penilaian\NilaiKeterampilanDetail;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\RangePredikat;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Skbm\Skbm;

class NilaiKeterampilanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $skbm = Skbm::aktif()->where('unit_id', auth()->user()->pegawai->unit_id)->first();
        $mapelskbm = $skbm->detail->where('employee_id', $employee_id)->pluck('subject_id');
        $semester_id = session('semester_aktif');
        if (empty($mapelskbm)) {
            $mapel = NULL;
        } else {
            $mapel = MataPelajaran::whereIn('id', $mapelskbm)->where('subject_name', 'not like', "Qur'an")->get();
        }
        $semester = Semester::where('id', $semester_id)->first();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.nilaiketerampilan', compact('mapel', 'semester', 'level'));
    }

    public function predikat()
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

        return view('penilaian.predikatketerampilan', compact('semester', 'level', 'mapel'));
    }

    public function getdesc(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $subject_id = $request->mapel_id;
        $level_id = $request->level_id;
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 5], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $level_id]])->orderBy('predicate', 'ASC')->get();
        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        $view = view('penilaian.getdescketerampilan', compact('rpd'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getlevel(Request $request)
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
        $view = view('penilaian.getlevelpengetahuan', compact('level', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
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
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $countrapor = NilaiRapor::where([['student_id', $siswa[0]->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswa[0]->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                $countketerampilan = NilaiKeterampilan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($countketerampilan > 0) {
                    $persentase = NilaiKeterampilan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                } else {
                    $persentase = FALSE;
                }
            } else {
                $persentase = FALSE;
            }
            $validasi = NilaiRapor::where([['report_status_id', 0], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $countketerampilan = NilaiKeterampilan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($countketerampilan > 0) {
                        $nilaiketerampilan[$key] = NilaiKeterampilan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->with('nilaiketerampilandetail')->first();
                    } else {
                        $nilaiketerampilan[$key] = FALSE;
                    }
                } else {
                    $nilaiketerampilan[$key] = FALSE;
                }
            }
        }


        $jumlahkd = KDSetting::where([['kd_type_id', 2], ['employee_id', $employee_id], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
        if ($jumlahkd == NULL) {
            $jumlahkd = FALSE;
        }

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 5], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $level_id]])->count();

        $view = view('penilaian.getketerampilan', compact('rpd', 'jumlahkd', 'siswa', 'persentase', 'nilaiketerampilan', 'countrapor', 'validasi'))->render();
        return response()->json(array('success' => true, 'html' => $view));
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
    public function simpanPredikat(Request $request)
    {
        $request->validate([
            'predikat' => 'required',
            'deskripsi' => 'required',
        ]);

        //Jenis predikat deskripsi untuk nilai keterampilan
        $rpd_type_id = 5;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;
        $subject_id = $request->idmapel;
        $level_id = $request->idlevel;
        $semester_id = session('semester_aktif');

        $query = PredikatDeskripsi::create([
            'subject_id' => $subject_id,
            'level_id' => $level_id,
            'semester_id' => $semester_id,
            'predicate' => $request->predikat,
            'description' => preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi)),
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaianmapel/predikatketerampilan')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatketerampilan')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function ubahPredikat(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->predicate = $request->predikat;
        $query->description = preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi));

        if ($query->update()) {
            return redirect('/kependidikan/penilaianmapel/predikatketerampilan')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatketerampilan')->with(['error' => 'Data gagal diubah']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusPredikat(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaianmapel/predikatketerampilan')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatketerampilan')->with(['error' => 'Data gagal dihapus']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $iserror = FALSE;

        foreach ($siswa_id as $key => $siswas_id) {
            $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                $siswass = Siswa::where('id', $siswas_id)->first();
                $kelas = Kelas::where('id', $siswass->class_id)->first();
                $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                $namawali = $wali->name;
                $nilairapor->hr_name = $namawali;
                $countketerampilan = NilaiKeterampilan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($nilairapor->update() && $countketerampilan == 0) {
                    $mean = 0;

                    foreach ($request->fieldkd as $field) {
                        $mean += $request->{$field}[$key];
                    }

                    $means = $mean / $request->jumlahkd;

                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($means >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($means >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($means >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($means >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($means >= 85) {
                            $predikat = "A";
                        } elseif ($means >= 75) {
                            $predikat = "B";
                        } elseif ($means >= 65) {
                            $predikat = "C";
                        } elseif ($means >= 0) {
                            $predikat = "D";
                        }
                    }

                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 5], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();

                    if($rpd){
                        $nilaiketerampilan = NilaiKeterampilan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'mean' => $means,
                            'rpd_id' => $rpd->id
                        ]);
                        $iserror = FALSE;
                    }
                    else {
                        $iserror = TRUE;
                        break;
                    }

                    if ($nilaiketerampilan->save()) {
                        $nk_id = $nilaiketerampilan->id;
                        
                        foreach ($request->fieldkd as $field) {
                            $detailketerampilan = NilaiKeterampilanDetail::create([
                                'score_skill_id' => $nk_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailketerampilan->save()) {
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
                    $nilaiketerampilan = NilaiKeterampilan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                    $nk_id = $nilaiketerampilan->id;
                    if($nilaiketerampilan->nilaiketerampilandetail()->count() > 0) {
                        $nilaiketerampilan->nilaiketerampilandetail()->delete();
                    }
                    $mean = 0;
                    foreach ($request->fieldkd as $field) {
                        $detailketerampilan = NilaiKeterampilanDetail::create([
                            'score_skill_id' => $nk_id,
                            'score' => $request->{$field}[$key]
                        ]);
                        if ($detailketerampilan->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                        $mean = $mean + $request->{$field}[$key];
                    }
                    $means = $mean / $request->jumlahkd;
                    $updateketerampilan = NilaiKeterampilan::where('id', $nk_id)->first();
                    $updateketerampilan->mean = $means;

                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($means >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($means >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($means >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($means >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($means >= 85) {
                            $predikat = "A";
                        } elseif ($means >= 75) {
                            $predikat = "B";
                        } elseif ($means >= 65) {
                            $predikat = "C";
                        } elseif ($means >= 0) {
                            $predikat = "D";
                        }
                    }
                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 5], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                    $updateketerampilan->rpd_id = $rpd->id;
                    if ($updateketerampilan->update()) {
                        $iserror = FALSE;
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
                    'acc_id' => 0,
                    'unit_id' => $siswas->unit_id,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek
                ]);
                if ($nilairapor->save()) {
                    $mean = 0;

                    foreach ($request->fieldkd as $field) {
                        $mean += $request->{$field}[$key];
                    }

                    $means = $mean / $request->jumlahkd;

                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($means >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($means >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($means >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($means >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($means >= 85) {
                            $predikat = "A";
                        } elseif ($means >= 75) {
                            $predikat = "B";
                        } elseif ($means >= 65) {
                            $predikat = "C";
                        } elseif ($means >= 0) {
                            $predikat = "D";
                        }
                    }

                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 5], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();

                    if($rpd){
                        $nilaiketerampilan = NilaiKeterampilan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'mean' => $means,
                            'rpd_id' => $rpd->id
                        ]);
                        $iserror = FALSE;
                    }
                    else {
                        $iserror = TRUE;
                        break;
                    }

                    if ($nilaiketerampilan->save()) {
                        $nk_id = $nilaiketerampilan->id;
                        
                        foreach ($request->fieldkd as $field) {
                            $detailketerampilan = NilaiKeterampilanDetail::create([
                                'score_skill_id' => $nk_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailketerampilan->save()) {
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
            return redirect('/kependidikan/penilaianmapel/nilaiketerampilan')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaiketerampilan')->with(['error' => 'Data gagal disimpan']);
        }
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

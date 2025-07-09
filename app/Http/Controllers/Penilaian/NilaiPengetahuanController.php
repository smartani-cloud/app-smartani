<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\KkmPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\NilaiPengetahuan;
use App\Models\Penilaian\NilaiPengetahuanDetail;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\NilaiSKHB;
use App\Models\Penilaian\RangePredikat;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Skbm\Skbm;

class NilaiPengetahuanController extends Controller
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

        return view('penilaian.nilaipengetahuan', compact('mapel', 'semester', 'level'));
    }

    public function indexkepsek()
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit = auth()->user()->pegawai->unit->id;
        $mapel = MataPelajaran::where('unit_id', $unit)->where('subject_name', 'not like', "Qur'an")->get();
        if (empty($mapel)) {
            $mapel = NULL;
        }
        $semester = Semester::orderBy('semester_id', 'ASC')->get();
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.npkepsek', compact('mapel', 'semester', 'level'));
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

        return view('penilaian.predikatpengetahuan', compact('semester', 'level', 'mapel'));
    }

    public function getdesc(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $subject_id = $request->mapel_id;
        $level_id = $request->level_id;
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $level_id]])->orderBy('predicate', 'ASC')->get();
        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        $view = view('penilaian.getdescpengetahuan', compact('rpd'))->render();
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

        //Jenis predikat deskripsi untuk nilai pengetahuan
        $rpd_type_id = 4;

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
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function ubahPredikat(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->predicate = $request->predikat;
        $query->description = preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi));

        if ($query->update()) {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['error' => 'Data gagal diubah']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusPredikat(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['error' => 'Data gagal dihapus']);
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
        $pts = $request->pts;
        $pas = $request->pas;
        $project = $request->project;
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
                $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($nilairapor->update() && $countpengetahuan == 0) {
                    $mean = 0;
                    foreach ($request->fieldkd as $field) {
                        $mean += $request->{$field}[$key];
                    }
                    $means = $mean / $request->jumlahkd;

                    $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($nilaiakhir >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($nilaiakhir >= 85) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= 75) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= 65) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= 0) {
                            $predikat = "D";
                        }
                    }

                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                    if($rpd){
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'mean' => $means,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                            'rpd_id' => $rpd->id,
                            'score_knowledge' => $nilaiakhir
                        ]);
                        $iserror = FALSE;
                    }
                    else {
                        $iserror = TRUE;
                        break;
                    }
                    
                    if ($nilaipengetahuan->save()) {
                        $np_id = $nilaipengetahuan->id;
                        foreach ($request->fieldkd as $field) {
                            $detailpengetahuan = NilaiPengetahuanDetail::create([
                                'score_knowledge_id' => $np_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailpengetahuan->save()) {
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
                    $nilaipengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                    $nilaipengetahuan->pts = $pts[$key];
                    $nilaipengetahuan->pas = $pas[$key];
                    $nilaipengetahuan->project = $project[$key];
                    $nilaipengetahuan->precentage_kd = $request->persenkd;
                    $nilaipengetahuan->precentage_pts = $request->persenpts;
                    $nilaipengetahuan->precentage_pas = $request->persenpas;
                    $nilaipengetahuan->precentage_project = $request->persenproject;
                    if ($nilaipengetahuan->update()) {
                        $np_id = $nilaipengetahuan->id;
                        if($nilaipengetahuan->nilaipengetahuandetail()->count() > 0) {
                            $nilaipengetahuan->nilaipengetahuandetail()->delete();
                        }
                        $mean = 0;
                        foreach ($request->fieldkd as $field) {
                            $detailpengetahuan = NilaiPengetahuanDetail::create([
                                'score_knowledge_id' => $np_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailpengetahuan->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                            $mean = $mean + $request->{$field}[$key];
                        }
                        $means = $mean / $request->jumlahkd;
                        $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                        $updatepengetahuan->mean = $means;
                        $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                        $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                        if ($rangenilai) {
                            if ($nilaiakhir >= $rangenilai->range_a) {
                                $predikat = "A";
                            } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                $predikat = "B";
                            } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                $predikat = "C";
                            } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                $predikat = "D";
                            }
                        } else {
                            if ($nilaiakhir >= 85) {
                                $predikat = "A";
                            } elseif ($nilaiakhir >= 75) {
                                $predikat = "B";
                            } elseif ($nilaiakhir >= 65) {
                                $predikat = "C";
                            } elseif ($nilaiakhir >= 0) {
                                $predikat = "D";
                            }
                        }
                        $kelass = Kelas::where('id', $class_id)->first();
                        $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                        if($rpd){
                            $updatepengetahuan->rpd_id = $rpd->id;
                            $updatepengetahuan->score_knowledge = $nilaiakhir;
                        }
                        if($rpd && $updatepengetahuan->update()) {
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

                    $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($nilaiakhir >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($nilaiakhir >= 85) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= 75) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= 65) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= 0) {
                            $predikat = "D";
                        }
                    }

                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                    if($rpd){
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'mean' => $means,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                            'rpd_id' => $rpd->id,
                            'score_knowledge' => $nilaiakhir
                        ]);
                        $iserror = FALSE;
                    }
                    else {
                        $iserror = TRUE;
                        break;
                    }

                    if ($nilaipengetahuan->save()) {
                        $np_id = $nilaipengetahuan->id;
                        foreach ($request->fieldkd as $field) {
                            $detailpengetahuan = NilaiPengetahuanDetail::create([
                                'score_knowledge_id' => $np_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailpengetahuan->save()) {
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
            return redirect('/kependidikan/penilaianmapel/nilaipengetahuan')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaipengetahuan')->with(['error' => 'Data gagal disimpan']);
        }
    }

    public function storekepsek(Request $request)
    {
        $pwedit = md5($request->pwedit);
        $semester_id = $request->semester_id;
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $pts = $request->pts;
        $pas = $request->pas;
        $project = $request->project;
        $iserror = FALSE;

        $pass = auth()->user()->pegawai->verification_password;
        if ($pwedit == $pass) {
            foreach ($siswa_id as $key => $siswas_id) {
                $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $siswass = Siswa::where('id', $siswas_id)->first();
                    $semester = Semester::where('id', $semester_id)->first();
                    $kelasHistory = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'class_id' => $class_id, 'student_id' => $siswas_id])->first();
                    $kelas = $kelasHistory->kelas;
                    $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                    $namawali = $wali->name;
                    $nilairapor->hr_name = $namawali;
                    $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($nilairapor->update() && $countpengetahuan == 0) {
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                        ]);
                        if ($nilaipengetahuan->save()) {
                            $np_id = $nilaipengetahuan->id;
                            $mean = 0;
                            foreach ($request->fieldkd as $field) {
                                $detailpengetahuan = NilaiPengetahuanDetail::create([
                                    'score_knowledge_id' => $np_id,
                                    'score' => $request->{$field}[$key]
                                ]);
                                if ($detailpengetahuan->save()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                                $mean = $mean + $request->{$field}[$key];
                            }
                            $means = $mean / $request->jumlahkd;
                            $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                            $updatepengetahuan->mean = $means;
                            $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                            if ($rangenilai) {
                                if ($nilaiakhir >= $rangenilai->range_a) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                    $predikat = "D";
                                }
                            } else {
                                if ($nilaiakhir >= 85) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= 75) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= 65) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= 0) {
                                    $predikat = "D";
                                }
                            }
                            $kelass = Kelas::where('id', $class_id)->first();
                            $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                            if($rpd){
                                $updatepengetahuan->rpd_id = $rpd->id;
                                $updatepengetahuan->score_knowledge = $nilaiakhir;
                            }
                            if ($rpd && $updatepengetahuan->update()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    } else {
                        $nilaipengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                        $nilaipengetahuan->pts = $pts[$key];
                        $nilaipengetahuan->pas = $pas[$key];
                        $nilaipengetahuan->project = $project[$key];
                        $nilaipengetahuan->precentage_kd = $request->persenkd;
                        $nilaipengetahuan->precentage_pts = $request->persenpts;
                        $nilaipengetahuan->precentage_pas = $request->persenpas;
                        $nilaipengetahuan->precentage_project = $request->persenproject;
                        if ($nilaipengetahuan->update()) {
                            $np_id = $nilaipengetahuan->id;
                            $hapusdetailpengetahuan = NilaiPengetahuanDetail::where('score_knowledge_id', $np_id)->delete();
                            if ($hapusdetailpengetahuan) {
                                $mean = 0;
                                foreach ($request->fieldkd as $field) {
                                    $detailpengetahuan = NilaiPengetahuanDetail::create([
                                        'score_knowledge_id' => $np_id,
                                        'score' => $request->{$field}[$key]
                                    ]);
                                    if ($detailpengetahuan->save()) {
                                        $iserror = FALSE;
                                    } else {
                                        $iserror = TRUE;
                                        break;
                                    }
                                    $mean = $mean + $request->{$field}[$key];
                                }
                                $means = $mean / $request->jumlahkd;
                                $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                                $updatepengetahuan->mean = $means;
                                $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                                $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                                if ($rangenilai) {
                                    if ($nilaiakhir >= $rangenilai->range_a) {
                                        $predikat = "A";
                                    } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                        $predikat = "B";
                                    } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                        $predikat = "C";
                                    } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                        $predikat = "D";
                                    }
                                } else {
                                    if ($nilaiakhir >= 85) {
                                        $predikat = "A";
                                    } elseif ($nilaiakhir >= 75) {
                                        $predikat = "B";
                                    } elseif ($nilaiakhir >= 65) {
                                        $predikat = "C";
                                    } elseif ($nilaiakhir >= 0) {
                                        $predikat = "D";
                                    }
                                }
                                $kelass = Kelas::where('id', $class_id)->first();
                                $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                                if($rpd){
                                    $updatepengetahuan->rpd_id = $rpd->id;
                                    $updatepengetahuan->score_knowledge = $nilaiakhir;
                                }
                                if ($rpd && $updatepengetahuan->update()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
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
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                        ]);
                        if ($nilaipengetahuan->save()) {
                            $np_id = $nilaipengetahuan->id;
                            $mean = 0;
                            foreach ($request->fieldkd as $field) {
                                $detailpengetahuan = NilaiPengetahuanDetail::create([
                                    'score_knowledge_id' => $np_id,
                                    'score' => $request->{$field}[$key]
                                ]);
                                if ($detailpengetahuan->save()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                                $mean = $mean + $request->{$field}[$key];
                            }
                            $means = $mean / $request->jumlahkd;
                            $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                            $updatepengetahuan->mean = $means;
                            $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                            if ($rangenilai) {
                                if ($nilaiakhir >= $rangenilai->range_a) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                    $predikat = "D";
                                }
                            } else {
                                if ($nilaiakhir >= 85) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= 75) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= 65) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= 0) {
                                    $predikat = "D";
                                }
                            }
                            $kelass = Kelas::where('id', $class_id)->first();
                            $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                            if($rpd){
                                $updatepengetahuan->rpd_id = $rpd->id;
                                $updatepengetahuan->score_knowledge = $nilaiakhir;
                            }
                            if ($rpd && $updatepengetahuan->update()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
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
                return redirect('/kependidikan/penilaiankepsek/nilaipengetahuan')->with(['sukses' => 'Data berhasil disimpan']);
            } else {
                return redirect('/kependidikan/penilaiankepsek/nilaipengetahuan')->with(['error' => 'Data gagal disimpan']);
            }
        } else {
            return redirect('/kependidikan/penilaiankepsek/nilaipengetahuan')->with(['error' => 'Password Verifikasi Tidak Sesuai! Harap mengisi password dengan benar dan pastikan Anda telah mengkonfigurasi password verifikasi!']);
        }
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

    public function getkelaskepsek(Request $request)
    {
        $kelas = Kelas::where('unit_id', auth()->user()->pegawai->unit_id)->where('level_id', $request->level_id)->get();
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
                $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($countpengetahuan > 0) {
                    $persentase = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                } else {
                    $persentase = FALSE;
                }
            } else {
                $persentase = FALSE;
            }
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($countpengetahuan > 0) {
                        $nilaipengetahuan[$key] = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->with('nilaipengetahuandetail')->first();
                    } else {
                        $nilaipengetahuan[$key] = FALSE;
                    }
                } else {
                    $nilaipengetahuan[$key] = FALSE;
                }
            }
            $validasi = NilaiRapor::where([['semester_id', $semester_id], ['class_id', $class_id], ['report_status_id', 0]])->count();
        }


        $jumlahkd = KDSetting::where([['kd_type_id', 1], ['employee_id', $employee_id], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
        if ($jumlahkd == NULL) {
            $jumlahkd = FALSE;
        }

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $level_id]])->count();
        $kkm = KkmPelajaran::where(['subject_id' => $subject_id, 'semester_id' => $semester_id])->first();

        $view = view('penilaian.getpengetahuan', compact('rpd', 'jumlahkd', 'siswa', 'persentase', 'nilaipengetahuan', 'validasi', 'countrapor','kkm'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswakepsek(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = $request->semester_id;

        $raporsiswa = NilaiRapor::where([['class_id', $class_id], ['semester_id', $semester_id]])->pluck('student_id');
        $siswa = Siswa::whereIn('id', $raporsiswa)->get();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $countrapor = NilaiRapor::where([['student_id', $siswa[0]->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswa[0]->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($countpengetahuan > 0) {
                    $persentase = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                } else {
                    $persentase = FALSE;
                }
            } else {
                $persentase = FALSE;
            }
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($countpengetahuan > 0) {
                        $nilaipengetahuan[$key] = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->with('nilaipengetahuandetail')->first();
                    } else {
                        $nilaipengetahuan[$key] = FALSE;
                    }
                } else {
                    $nilaipengetahuan[$key] = FALSE;
                }
            }
            $validasi = NilaiRapor::where([['semester_id', $semester_id], ['class_id', $class_id], ['report_status_id', 0]])->count();
        }

        $kkm = KkmPelajaran::where(['subject_id' => $subject_id, 'semester_id' => $semester_id])->first();
        $jumlahkd = KDSetting::where([['kd_type_id', 1], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
        if ($jumlahkd == NULL) {
            $jumlahkd = FALSE;
        }
        $view = view('penilaian.getpengetahuankepsek', compact('jumlahkd', 'siswa', 'persentase', 'nilaipengetahuan', 'validasi', 'countrapor', 'semester_id', 'kkm'))->render();
        return response()->json(array('success' => true, 'html' => $view));
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
=======
<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\KkmPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\NilaiPengetahuan;
use App\Models\Penilaian\NilaiPengetahuanDetail;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\NilaiSKHB;
use App\Models\Penilaian\RangePredikat;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Skbm\Skbm;

class NilaiPengetahuanController extends Controller
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

        return view('penilaian.nilaipengetahuan', compact('mapel', 'semester', 'level'));
    }

    public function indexkepsek()
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit = auth()->user()->pegawai->unit->id;
        $mapel = MataPelajaran::where('unit_id', $unit)->where('subject_name', 'not like', "Qur'an")->get();
        if (empty($mapel)) {
            $mapel = NULL;
        }
        $semester = Semester::orderBy('semester_id', 'ASC')->get();
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.npkepsek', compact('mapel', 'semester', 'level'));
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

        return view('penilaian.predikatpengetahuan', compact('semester', 'level', 'mapel'));
    }

    public function getdesc(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $subject_id = $request->mapel_id;
        $level_id = $request->level_id;
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $level_id]])->orderBy('predicate', 'ASC')->get();
        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        $view = view('penilaian.getdescpengetahuan', compact('rpd'))->render();
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

        //Jenis predikat deskripsi untuk nilai pengetahuan
        $rpd_type_id = 4;

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
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function ubahPredikat(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->predicate = $request->predikat;
        $query->description = preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi));

        if ($query->update()) {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['error' => 'Data gagal diubah']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusPredikat(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaianmapel/predikatpengetahuan')->with(['error' => 'Data gagal dihapus']);
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
        $pts = $request->pts;
        $pas = $request->pas;
        $project = $request->project;
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
                $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($nilairapor->update() && $countpengetahuan == 0) {
                    $mean = 0;
                    foreach ($request->fieldkd as $field) {
                        $mean += $request->{$field}[$key];
                    }
                    $means = $mean / $request->jumlahkd;

                    $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($nilaiakhir >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($nilaiakhir >= 85) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= 75) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= 65) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= 0) {
                            $predikat = "D";
                        }
                    }

                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                    if($rpd){
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'mean' => $means,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                            'rpd_id' => $rpd->id,
                            'score_knowledge' => $nilaiakhir
                        ]);
                        $iserror = FALSE;
                    }
                    else {
                        $iserror = TRUE;
                        break;
                    }
                    
                    if ($nilaipengetahuan->save()) {
                        $np_id = $nilaipengetahuan->id;
                        foreach ($request->fieldkd as $field) {
                            $detailpengetahuan = NilaiPengetahuanDetail::create([
                                'score_knowledge_id' => $np_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailpengetahuan->save()) {
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
                    $nilaipengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                    $nilaipengetahuan->pts = $pts[$key];
                    $nilaipengetahuan->pas = $pas[$key];
                    $nilaipengetahuan->project = $project[$key];
                    $nilaipengetahuan->precentage_kd = $request->persenkd;
                    $nilaipengetahuan->precentage_pts = $request->persenpts;
                    $nilaipengetahuan->precentage_pas = $request->persenpas;
                    $nilaipengetahuan->precentage_project = $request->persenproject;
                    if ($nilaipengetahuan->update()) {
                        $np_id = $nilaipengetahuan->id;
                        if($nilaipengetahuan->nilaipengetahuandetail()->count() > 0) {
                            $nilaipengetahuan->nilaipengetahuandetail()->delete();
                        }
                        $mean = 0;
                        foreach ($request->fieldkd as $field) {
                            $detailpengetahuan = NilaiPengetahuanDetail::create([
                                'score_knowledge_id' => $np_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailpengetahuan->save()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                            $mean = $mean + $request->{$field}[$key];
                        }
                        $means = $mean / $request->jumlahkd;
                        $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                        $updatepengetahuan->mean = $means;
                        $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                        $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                        if ($rangenilai) {
                            if ($nilaiakhir >= $rangenilai->range_a) {
                                $predikat = "A";
                            } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                $predikat = "B";
                            } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                $predikat = "C";
                            } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                $predikat = "D";
                            }
                        } else {
                            if ($nilaiakhir >= 85) {
                                $predikat = "A";
                            } elseif ($nilaiakhir >= 75) {
                                $predikat = "B";
                            } elseif ($nilaiakhir >= 65) {
                                $predikat = "C";
                            } elseif ($nilaiakhir >= 0) {
                                $predikat = "D";
                            }
                        }
                        $kelass = Kelas::where('id', $class_id)->first();
                        $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                        if($rpd){
                            $updatepengetahuan->rpd_id = $rpd->id;
                            $updatepengetahuan->score_knowledge = $nilaiakhir;
                        }
                        if($rpd && $updatepengetahuan->update()) {
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

                    $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                    $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                    if ($rangenilai) {
                        if ($nilaiakhir >= $rangenilai->range_a) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= $rangenilai->range_b) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= $rangenilai->range_c) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= $rangenilai->range_d) {
                            $predikat = "D";
                        }
                    } else {
                        if ($nilaiakhir >= 85) {
                            $predikat = "A";
                        } elseif ($nilaiakhir >= 75) {
                            $predikat = "B";
                        } elseif ($nilaiakhir >= 65) {
                            $predikat = "C";
                        } elseif ($nilaiakhir >= 0) {
                            $predikat = "D";
                        }
                    }

                    $kelass = Kelas::where('id', $class_id)->first();
                    $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                    if($rpd){
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'mean' => $means,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                            'rpd_id' => $rpd->id,
                            'score_knowledge' => $nilaiakhir
                        ]);
                        $iserror = FALSE;
                    }
                    else {
                        $iserror = TRUE;
                        break;
                    }

                    if ($nilaipengetahuan->save()) {
                        $np_id = $nilaipengetahuan->id;
                        foreach ($request->fieldkd as $field) {
                            $detailpengetahuan = NilaiPengetahuanDetail::create([
                                'score_knowledge_id' => $np_id,
                                'score' => $request->{$field}[$key]
                            ]);
                            if ($detailpengetahuan->save()) {
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
            return redirect('/kependidikan/penilaianmapel/nilaipengetahuan')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaipengetahuan')->with(['error' => 'Data gagal disimpan']);
        }
    }

    public function storekepsek(Request $request)
    {
        $pwedit = md5($request->pwedit);
        $semester_id = $request->semester_id;
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $pts = $request->pts;
        $pas = $request->pas;
        $project = $request->project;
        $iserror = FALSE;

        $pass = auth()->user()->pegawai->verification_password;
        if ($pwedit == $pass) {
            foreach ($siswa_id as $key => $siswas_id) {
                $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $siswass = Siswa::where('id', $siswas_id)->first();
                    $semester = Semester::where('id', $semester_id)->first();
                    $kelasHistory = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'class_id' => $class_id, 'student_id' => $siswas_id])->first();
                    $kelas = $kelasHistory->kelas;
                    $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                    $namawali = $wali->name;
                    $nilairapor->hr_name = $namawali;
                    $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($nilairapor->update() && $countpengetahuan == 0) {
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                        ]);
                        if ($nilaipengetahuan->save()) {
                            $np_id = $nilaipengetahuan->id;
                            $mean = 0;
                            foreach ($request->fieldkd as $field) {
                                $detailpengetahuan = NilaiPengetahuanDetail::create([
                                    'score_knowledge_id' => $np_id,
                                    'score' => $request->{$field}[$key]
                                ]);
                                if ($detailpengetahuan->save()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                                $mean = $mean + $request->{$field}[$key];
                            }
                            $means = $mean / $request->jumlahkd;
                            $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                            $updatepengetahuan->mean = $means;
                            $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                            if ($rangenilai) {
                                if ($nilaiakhir >= $rangenilai->range_a) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                    $predikat = "D";
                                }
                            } else {
                                if ($nilaiakhir >= 85) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= 75) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= 65) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= 0) {
                                    $predikat = "D";
                                }
                            }
                            $kelass = Kelas::where('id', $class_id)->first();
                            $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                            if($rpd){
                                $updatepengetahuan->rpd_id = $rpd->id;
                                $updatepengetahuan->score_knowledge = $nilaiakhir;
                            }
                            if ($rpd && $updatepengetahuan->update()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
                            }
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    } else {
                        $nilaipengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                        $nilaipengetahuan->pts = $pts[$key];
                        $nilaipengetahuan->pas = $pas[$key];
                        $nilaipengetahuan->project = $project[$key];
                        $nilaipengetahuan->precentage_kd = $request->persenkd;
                        $nilaipengetahuan->precentage_pts = $request->persenpts;
                        $nilaipengetahuan->precentage_pas = $request->persenpas;
                        $nilaipengetahuan->precentage_project = $request->persenproject;
                        if ($nilaipengetahuan->update()) {
                            $np_id = $nilaipengetahuan->id;
                            $hapusdetailpengetahuan = NilaiPengetahuanDetail::where('score_knowledge_id', $np_id)->delete();
                            if ($hapusdetailpengetahuan) {
                                $mean = 0;
                                foreach ($request->fieldkd as $field) {
                                    $detailpengetahuan = NilaiPengetahuanDetail::create([
                                        'score_knowledge_id' => $np_id,
                                        'score' => $request->{$field}[$key]
                                    ]);
                                    if ($detailpengetahuan->save()) {
                                        $iserror = FALSE;
                                    } else {
                                        $iserror = TRUE;
                                        break;
                                    }
                                    $mean = $mean + $request->{$field}[$key];
                                }
                                $means = $mean / $request->jumlahkd;
                                $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                                $updatepengetahuan->mean = $means;
                                $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                                $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                                if ($rangenilai) {
                                    if ($nilaiakhir >= $rangenilai->range_a) {
                                        $predikat = "A";
                                    } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                        $predikat = "B";
                                    } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                        $predikat = "C";
                                    } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                        $predikat = "D";
                                    }
                                } else {
                                    if ($nilaiakhir >= 85) {
                                        $predikat = "A";
                                    } elseif ($nilaiakhir >= 75) {
                                        $predikat = "B";
                                    } elseif ($nilaiakhir >= 65) {
                                        $predikat = "C";
                                    } elseif ($nilaiakhir >= 0) {
                                        $predikat = "D";
                                    }
                                }
                                $kelass = Kelas::where('id', $class_id)->first();
                                $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                                if($rpd){
                                    $updatepengetahuan->rpd_id = $rpd->id;
                                    $updatepengetahuan->score_knowledge = $nilaiakhir;
                                }
                                if ($rpd && $updatepengetahuan->update()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
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
                        $nilaipengetahuan = NilaiPengetahuan::create([
                            'score_id' => $nilairapor->id,
                            'subject_id' => $subject_id,
                            'pts' => $pts[$key],
                            'pas' => $pas[$key],
                            'project' => $project[$key],
                            'precentage_kd' => $request->persenkd,
                            'precentage_pts' => $request->persenpts,
                            'precentage_pas' => $request->persenpas,
                            'precentage_project' => $request->persenproject,
                        ]);
                        if ($nilaipengetahuan->save()) {
                            $np_id = $nilaipengetahuan->id;
                            $mean = 0;
                            foreach ($request->fieldkd as $field) {
                                $detailpengetahuan = NilaiPengetahuanDetail::create([
                                    'score_knowledge_id' => $np_id,
                                    'score' => $request->{$field}[$key]
                                ]);
                                if ($detailpengetahuan->save()) {
                                    $iserror = FALSE;
                                } else {
                                    $iserror = TRUE;
                                    break;
                                }
                                $mean = $mean + $request->{$field}[$key];
                            }
                            $means = $mean / $request->jumlahkd;
                            $updatepengetahuan = NilaiPengetahuan::where('id', $np_id)->first();
                            $updatepengetahuan->mean = $means;
                            $nilaiakhir = (($request->persenkd / 100) * $means) + (($request->persenpts / 100) * $pts[$key]) + (($request->persenpas / 100) * $pas[$key]) + (($request->persenproject / 100) * $project[$key]);
                            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelas->level_id]])->first();
                            if ($rangenilai) {
                                if ($nilaiakhir >= $rangenilai->range_a) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= $rangenilai->range_b) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= $rangenilai->range_c) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= $rangenilai->range_d) {
                                    $predikat = "D";
                                }
                            } else {
                                if ($nilaiakhir >= 85) {
                                    $predikat = "A";
                                } elseif ($nilaiakhir >= 75) {
                                    $predikat = "B";
                                } elseif ($nilaiakhir >= 65) {
                                    $predikat = "C";
                                } elseif ($nilaiakhir >= 0) {
                                    $predikat = "D";
                                }
                            }
                            $kelass = Kelas::where('id', $class_id)->first();
                            $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $kelass->level_id], ['predicate', 'like', $predikat]])->first();
                            if($rpd){
                                $updatepengetahuan->rpd_id = $rpd->id;
                                $updatepengetahuan->score_knowledge = $nilaiakhir;
                            }
                            if ($rpd && $updatepengetahuan->update()) {
                                $iserror = FALSE;
                            } else {
                                $iserror = TRUE;
                                break;
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
                return redirect('/kependidikan/penilaiankepsek/nilaipengetahuan')->with(['sukses' => 'Data berhasil disimpan']);
            } else {
                return redirect('/kependidikan/penilaiankepsek/nilaipengetahuan')->with(['error' => 'Data gagal disimpan']);
            }
        } else {
            return redirect('/kependidikan/penilaiankepsek/nilaipengetahuan')->with(['error' => 'Password Verifikasi Tidak Sesuai! Harap mengisi password dengan benar dan pastikan Anda telah mengkonfigurasi password verifikasi!']);
        }
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

    public function getkelaskepsek(Request $request)
    {
        $kelas = Kelas::where('unit_id', auth()->user()->pegawai->unit_id)->where('level_id', $request->level_id)->get();
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
                $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($countpengetahuan > 0) {
                    $persentase = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                } else {
                    $persentase = FALSE;
                }
            } else {
                $persentase = FALSE;
            }
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($countpengetahuan > 0) {
                        $nilaipengetahuan[$key] = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->with('nilaipengetahuandetail')->first();
                    } else {
                        $nilaipengetahuan[$key] = FALSE;
                    }
                } else {
                    $nilaipengetahuan[$key] = FALSE;
                }
            }
            $validasi = NilaiRapor::where([['semester_id', $semester_id], ['class_id', $class_id], ['report_status_id', 0]])->count();
        }


        $jumlahkd = KDSetting::where([['kd_type_id', 1], ['employee_id', $employee_id], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
        if ($jumlahkd == NULL) {
            $jumlahkd = FALSE;
        }

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 4], ['employee_id', $employee_id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['level_id', $level_id]])->count();
        $kkm = KkmPelajaran::where(['subject_id' => $subject_id, 'semester_id' => $semester_id])->first();

        $view = view('penilaian.getpengetahuan', compact('rpd', 'jumlahkd', 'siswa', 'persentase', 'nilaipengetahuan', 'validasi', 'countrapor','kkm'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswakepsek(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = $request->semester_id;

        $raporsiswa = NilaiRapor::where([['class_id', $class_id], ['semester_id', $semester_id]])->pluck('student_id');
        $siswa = Siswa::whereIn('id', $raporsiswa)->get();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $countrapor = NilaiRapor::where([['student_id', $siswa[0]->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswa[0]->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($countpengetahuan > 0) {
                    $persentase = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                } else {
                    $persentase = FALSE;
                }
            } else {
                $persentase = FALSE;
            }
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $countpengetahuan = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($countpengetahuan > 0) {
                        $nilaipengetahuan[$key] = NilaiPengetahuan::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->with('nilaipengetahuandetail')->first();
                    } else {
                        $nilaipengetahuan[$key] = FALSE;
                    }
                } else {
                    $nilaipengetahuan[$key] = FALSE;
                }
            }
            $validasi = NilaiRapor::where([['semester_id', $semester_id], ['class_id', $class_id], ['report_status_id', 0]])->count();
        }

        $kkm = KkmPelajaran::where(['subject_id' => $subject_id, 'semester_id' => $semester_id])->first();
        $jumlahkd = KDSetting::where([['kd_type_id', 1], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
        if ($jumlahkd == NULL) {
            $jumlahkd = FALSE;
        }
        $view = view('penilaian.getpengetahuankepsek', compact('jumlahkd', 'siswa', 'persentase', 'nilaipengetahuan', 'validasi', 'countrapor', 'semester_id', 'kkm'))->render();
        return response()->json(array('success' => true, 'html' => $view));
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

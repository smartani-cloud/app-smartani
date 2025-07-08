<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use Illuminate\Http\Request;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\NilaiIklas;
use App\Models\Penilaian\ScoreIklas;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RpdType;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiSertifIklas;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\SertifIklas;
use App\Models\Rekrutmen\Pegawai;

use App\Models\Unit;

class IklasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // check class
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->latest()->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::where('id', $smt_aktif)->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }
        $rpd = PredikatDeskripsi::select('predicate')->where([
            'level_id' => $kelas->level_id,
            'semester_id' => $semester->id,
            'rpd_type_id' => $type->id,
        ])->orderBy('predicate', 'ASC')->get();

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }

        return view('penilaian.iklas', compact('siswa', 'kelas', 'semester', 'rpd'));
    }

    public function sertif()
    {
        $employee_id = auth()->user()->pegawai->id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // check class
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::where('id', $smt_aktif)->first();

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }

        return view('penilaian.sertifiklas', compact('siswa', 'kelas', 'semester'));
    }

    public function getNilai(Request $request)
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
            $nilaiiklas = NilaiIklas::where([['score_id', $nilairapor->id]])->count();
            if ($nilaiiklas > 0) {
                $nilaiiklas = NilaiIklas::where([['score_id', $nilairapor->id]])->first();
                $scoreiklas = ScoreIklas::where([['iklas_id', $nilaiiklas->id]])->orderBy('iklas_ref_id', 'ASC')->get();
            } else {
                $scoreiklas = FALSE;
            }
        } else {
            $scoreiklas = FALSE;
        }
        $view = view('penilaian.getnilaiiklas')->with('scoreiklas', $scoreiklas)->with('siswa', $siswa)->with('validasi', $validasi)->with('countrapor', $countrapor)->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function sertifgetNilai(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit_id = auth()->user()->pegawai->unit_id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $siswa = Siswa::where('id', $siswa_id)->first();

        $countsertif = SertifIklas::where([['student_id', $request->siswa_id], ['unit_id', $unit_id]])->count();
        if ($countsertif > 0) {
            $sertif = SertifIklas::where([['student_id', $request->siswa_id], ['unit_id', $unit_id]])->first();
            $scoreiklas = NilaiSertifIklas::where([['iklas_certificate_id', $sertif->id]])->count();
            if ($scoreiklas > 0) {
                $scoreiklas = NilaiSertifIklas::where([['iklas_certificate_id', $sertif->id]])->orderBy('iklas_ref_id', 'ASC')->get();
            } else {
                $scoreiklas = FALSE;
            }
        } else {
            $scoreiklas = FALSE;
        }
        $view = view('penilaian.getnilaisertifiklas')->with('scoreiklas', $scoreiklas)->with('siswa', $siswa)->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $rating = $request->rating;
        $errorsimpan = FALSE;
        $iklas = RefIklas::select('id','iklas_cat','iklas_no')->get();

        $siswa = Siswa::where([['id', $request->siswa_id], ['class_id', $class_id]])->first();

        $nilairapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $namawali = auth()->user()->pegawai->name;
            $nilairapor->hr_name = $namawali;
            $nilaiiklas = NilaiIklas::where([['score_id', $nilairapor->id]])->count();
            if ($nilairapor->update() && $nilaiiklas > 0) {
                $nilaiiklas = NilaiIklas::where([['score_id', $nilairapor->id]])->first();
                foreach($iklas as $i){
                    $scoreIklas = $nilaiiklas->detail()->where('iklas_ref_id',$i->id)->first();
                    $thisRating = isset($rating[$i->iklas_cat][$i->iklas_no]) ? $rating[$i->iklas_cat][$i->iklas_no] : null;
                    if($scoreIklas){
                        $scoreIklas->predicate = $thisRating;
                        if($scoreIklas->update()){
                            $errorsimpan = FALSE;
                        }
                        else{
                            $errorsimpan = TRUE;
                            break;
                        }
                    }
                    elseif($thisRating){
                        $scoreiklas = ScoreIklas::create([
                            'iklas_id' => $nilaiiklas->id,
                            'iklas_ref_id' => $i->id,
                            'predicate' => $thisRating
                        ]);
                    }
                }
            } else {
                $nilaiiklas = NilaiIklas::create([
                    'score_id' => $nilairapor->id
                ]);
                if ($nilaiiklas->save()) {
                    $errorsimpan = FALSE;
                } else {
                    $errorsimpan = TRUE;
                }
                for ($i = 1; $i <= 5; $i++) {
                    for ($x = 1; $x <= 5; $x++) {
                        if (isset($rating[$i][$x])) {
                            if ($i == 1) {
                                if ($x == 1) {
                                    $iklas_ref_id = 1;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 2;
                                } elseif ($x == 3) {
                                    $iklas_ref_id = 3;
                                } elseif ($x == 4) {
                                    $iklas_ref_id = 4;
                                }
                            } elseif ($i == 2) {
                                if ($x == 1) {
                                    $iklas_ref_id = 5;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 16;
                                } elseif ($x == 3) {
                                    $iklas_ref_id = 6;
                                } elseif ($x == 4) {
                                    $iklas_ref_id = 7;
                                } elseif ($x == 5) {
                                    $iklas_ref_id = 8;
                                } elseif ($x == 6) {
                                    $iklas_ref_id = 9;
                                }
                            } elseif ($i == 3) {
                                if ($x == 1) {
                                    $iklas_ref_id = 10;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 11;
                                }
                            } elseif ($i == 4) {
                                if ($x == 1) {
                                    $iklas_ref_id = 12;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 13;
                                } elseif ($x == 3) {
                                    $iklas_ref_id = 14;
                                } elseif ($x == 4) {
                                    $iklas_ref_id = 15;
                                }
                            }
                            $scoreiklas = ScoreIklas::create([
                                'iklas_id' => $nilaiiklas->id,
                                'iklas_ref_id' => $iklas_ref_id,
                                'predicate' => $rating[$i][$x]
                            ]);
                            if ($scoreiklas->save()) {
                                $errorsimpan = FALSE;
                            } else {
                                $errorsimpan = TRUE;
                                break;
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }
        } else {
            $namawali = auth()->user()->pegawai->name;
            $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
            $namakepsek = $kepsek->name;
            $nilairapor = NilaiRapor::create([
                'student_id' => $siswa->id,
                'semester_id' => $smt_aktif,
                'class_id' => $siswa->class_id,
                'report_status_id' => 0,
                'acc_id' => 0,
                'unit_id' => $siswa->unit_id,
                'hr_name' => $namawali,
                'hm_name' => $namakepsek
            ]);
            if ($nilairapor->save()) {
                $errorsimpan = FALSE;
            } else {
                $errorsimpan = TRUE;
            }
            $nilaiiklas = NilaiIklas::create([
                'score_id' => $nilairapor->id
            ]);
            if ($nilaiiklas->save()) {
                $errorsimpan = FALSE;
            } else {
                $errorsimpan = TRUE;
            }
            for ($i = 1; $i <= 5; $i++) {
                for ($x = 1; $x <= 5; $x++) {
                    if (isset($rating[$i][$x])) {
                        if ($i == 1) {
                            if ($x == 1) {
                                $iklas_ref_id = 1;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 2;
                            } elseif ($x == 3) {
                                $iklas_ref_id = 3;
                            } elseif ($x == 4) {
                                $iklas_ref_id = 4;
                            }
                        } elseif ($i == 2) {
                            if ($x == 1) {
                                $iklas_ref_id = 5;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 16;
                            } elseif ($x == 3) {
                                $iklas_ref_id = 6;
                            } elseif ($x == 4) {
                                $iklas_ref_id = 7;
                            } elseif ($x == 5) {
                                $iklas_ref_id = 8;
                            } elseif ($x == 6) {
                                $iklas_ref_id = 9;
                            }
                        } elseif ($i == 3) {
                            if ($x == 1) {
                                $iklas_ref_id = 10;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 11;
                            }
                        } elseif ($i == 4) {
                            if ($x == 1) {
                                $iklas_ref_id = 12;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 13;
                            } elseif ($x == 3) {
                                $iklas_ref_id = 14;
                            } elseif ($x == 4) {
                                $iklas_ref_id = 15;
                            }
                        }
                        $scoreiklas = ScoreIklas::create([
                            'iklas_id' => $nilaiiklas->id,
                            'iklas_ref_id' => $iklas_ref_id,
                            'predicate' => $rating[$i][$x]
                        ]);
                        if ($scoreiklas->save()) {
                            $errorsimpan = FALSE;
                        } else {
                            $errorsimpan = TRUE;
                            break;
                        }
                    } else {
                        continue;
                    }
                }
            }
        }

        if ($errorsimpan == FALSE) {
            return redirect('/kependidikan/penilaian/iklas')->with(['sukses' => 'Data berhasil disimpan']);
        } elseif ($errorsimpan == TRUE) {
            return redirect('/kependidikan/penilaian/iklas')->with(['gagal' => 'Data gagal disimpan']);
        }
    }

    public function sertifcreate(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = Semester::where('id', session('semester_aktif'))->first();
        $rating = $request->rating;
        $errorsimpan = FALSE;

        $siswa = Siswa::where([['id', $request->siswa_id], ['class_id', $class_id]])->first();

        $sertif = SertifIklas::where([['student_id', $siswa->id], ['unit_id', $siswa->unit_id]])->count();
        if ($sertif > 0) {
            $sertif = SertifIklas::where([['student_id', $siswa->id], ['unit_id', $siswa->unit_id]])->first();
            $nilaiiklas = NilaiSertifIklas::where([['iklas_certificate_id', $sertif->id]])->count();
            if ($nilaiiklas > 0) {
                $nilaiiklas = NilaiSertifIklas::where([['iklas_certificate_id', $sertif->id]])->get();
                foreach ($nilaiiklas as $scoreiklas) {
                    if ($scoreiklas->iklas_ref_id == 1) {
                        $scoreiklas->predicate = $rating[1][1];
                    } elseif ($scoreiklas->iklas_ref_id == 2) {
                        $scoreiklas->predicate = $rating[1][2];
                    } elseif ($scoreiklas->iklas_ref_id == 3) {
                        $scoreiklas->predicate = $rating[1][3];
                    } elseif ($scoreiklas->iklas_ref_id == 4) {
                        $scoreiklas->predicate = $rating[1][4];
                    } elseif ($scoreiklas->iklas_ref_id == 5) {
                        $scoreiklas->predicate = $rating[2][1];
                    } elseif ($scoreiklas->iklas_ref_id == 6) {
                        $scoreiklas->predicate = $rating[2][2];
                    } elseif ($scoreiklas->iklas_ref_id == 7) {
                        $scoreiklas->predicate = $rating[2][3];
                    } elseif ($scoreiklas->iklas_ref_id == 8) {
                        $scoreiklas->predicate = $rating[2][4];
                    } elseif ($scoreiklas->iklas_ref_id == 9) {
                        $scoreiklas->predicate = $rating[2][5];
                    } elseif ($scoreiklas->iklas_ref_id == 10) {
                        $scoreiklas->predicate = $rating[3][1];
                    } elseif ($scoreiklas->iklas_ref_id == 11) {
                        $scoreiklas->predicate = $rating[3][2];
                    } elseif ($scoreiklas->iklas_ref_id == 12) {
                        $scoreiklas->predicate = $rating[4][1];
                    } elseif ($scoreiklas->iklas_ref_id == 13) {
                        $scoreiklas->predicate = $rating[4][2];
                    } elseif ($scoreiklas->iklas_ref_id == 14) {
                        $scoreiklas->predicate = $rating[4][3];
                    } elseif ($scoreiklas->iklas_ref_id == 15) {
                        $scoreiklas->predicate = $rating[4][4];
                    }
                    if ($scoreiklas->update()) {
                        $errorsimpan = FALSE;
                    } else {
                        $errorsimpan = TRUE;
                        break;
                    }
                }
            } else {
                for ($i = 1; $i <= 5; $i++) {
                    for ($x = 1; $x <= 5; $x++) {
                        if (isset($rating[$i][$x])) {
                            if ($i == 1) {
                                if ($x == 1) {
                                    $iklas_ref_id = 1;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 2;
                                } elseif ($x == 3) {
                                    $iklas_ref_id = 3;
                                } elseif ($x == 4) {
                                    $iklas_ref_id = 4;
                                }
                            } elseif ($i == 2) {
                                if ($x == 1) {
                                    $iklas_ref_id = 5;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 6;
                                } elseif ($x == 3) {
                                    $iklas_ref_id = 7;
                                } elseif ($x == 4) {
                                    $iklas_ref_id = 8;
                                } elseif ($x == 5) {
                                    $iklas_ref_id = 9;
                                }
                            } elseif ($i == 3) {
                                if ($x == 1) {
                                    $iklas_ref_id = 10;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 11;
                                }
                            } elseif ($i == 4) {
                                if ($x == 1) {
                                    $iklas_ref_id = 12;
                                } elseif ($x == 2) {
                                    $iklas_ref_id = 13;
                                } elseif ($x == 3) {
                                    $iklas_ref_id = 14;
                                } elseif ($x == 4) {
                                    $iklas_ref_id = 15;
                                }
                            }
                            $scoreiklas = NilaiSertifIklas::create([
                                'iklas_certificate_id' => $sertif->id,
                                'iklas_ref_id' => $iklas_ref_id,
                                'predicate' => $rating[$i][$x]
                            ]);
                            if ($scoreiklas->save()) {
                                $errorsimpan = FALSE;
                            } else {
                                $errorsimpan = TRUE;
                                break;
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }
        } else {
            $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
            $namakepsek = $kepsek->name;
            $sertif = SertifIklas::create([
                'student_id' => $siswa->id,
                'academic_year_id' => $smt_aktif->academic_year_id,
                'unit_id' => $siswa->unit_id,
                'hm_name' => $namakepsek
            ]);
            if ($sertif->save()) {
                $errorsimpan = FALSE;
            } else {
                $errorsimpan = TRUE;
            }
            for ($i = 1; $i <= 5; $i++) {
                for ($x = 1; $x <= 5; $x++) {
                    if (isset($rating[$i][$x])) {
                        if ($i == 1) {
                            if ($x == 1) {
                                $iklas_ref_id = 1;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 2;
                            } elseif ($x == 3) {
                                $iklas_ref_id = 3;
                            } elseif ($x == 4) {
                                $iklas_ref_id = 4;
                            }
                        } elseif ($i == 2) {
                            if ($x == 1) {
                                $iklas_ref_id = 5;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 6;
                            } elseif ($x == 3) {
                                $iklas_ref_id = 7;
                            } elseif ($x == 4) {
                                $iklas_ref_id = 8;
                            } elseif ($x == 5) {
                                $iklas_ref_id = 9;
                            }
                        } elseif ($i == 3) {
                            if ($x == 1) {
                                $iklas_ref_id = 10;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 11;
                            }
                        } elseif ($i == 4) {
                            if ($x == 1) {
                                $iklas_ref_id = 12;
                            } elseif ($x == 2) {
                                $iklas_ref_id = 13;
                            } elseif ($x == 3) {
                                $iklas_ref_id = 14;
                            } elseif ($x == 4) {
                                $iklas_ref_id = 15;
                            }
                        }
                        $scoreiklas = NilaiSertifIklas::create([
                            'iklas_certificate_id' => $sertif->id,
                            'iklas_ref_id' => $iklas_ref_id,
                            'predicate' => $rating[$i][$x]
                        ]);
                        if ($scoreiklas->save()) {
                            $errorsimpan = FALSE;
                        } else {
                            $errorsimpan = TRUE;
                            break;
                        }
                    } else {
                        continue;
                    }
                }
            }
        }

        if ($errorsimpan == FALSE) {
            return redirect('/kependidikan/sertifiklas/nilai')->with(['sukses' => 'Data berhasil disimpan']);
        } elseif ($errorsimpan == TRUE) {
            return redirect('/kependidikan/sertifiklas/nilai')->with(['gagal' => 'Data gagal disimpan']);
        }
    }

    public function sertifcetak()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::orderBy('semester_id', 'ASC')->get();
        $semesteraktif = Semester::where('id', $smt_aktif)->first();
        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        foreach ($siswa as $key => $siswas) {
            $sertif[$key] = SertifIklas::where([['student_id', $siswas->id], ['academic_year_id', $semesteraktif->academic_year_id]])->first();
        }

        return view('penilaian.cetaksertifiklas', compact('siswa', 'semester', 'semesteraktif', 'kelas', 'sertif'));
    }

    public function sertifprint(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $iklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();
                $sertif = SertifIklas::where([['student_id', $siswa->id], ['unit_id', $unit->id]])->first();
                if($sertif){
                    $rapor = $siswa->nilaiRapor()->where([
                        'semester_id' => $semester->id,
                        'report_status_id' => 1,
                        'report_status_pts_id' => 1,
                        'unit_id' => $unit->id
                    ])->first();
                    $nilai = $rapor ? $rapor->iklas : null;
                }

                if($nilai) return view('penilaian.sertifikat_iklas', compact('iklas','sertif','nilai'));
            }
        }

        return redirect('/kependidikan/sertifiklas/cetak');
    }

    public function set_tanggal(Request $request)
    {
        $class_id = $request->class_id;
        $siswas = Siswa::where('class_id', $class_id)->get();
        $tanggal = $request->tanggal_sertif;
        $semester = Semester::where('id', session('semester_aktif'))->first();

        foreach ($siswas as $siswa) {
            $sertif = SertifIklas::where([['student_id', $siswa->id], ['unit_id', auth()->user()->pegawai->unit_id], ['academic_year_id', $semester->academic_year_id]])->first();
            if ($sertif) {
                $sertif->certificate_date = $tanggal;
                if ($sertif->update()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            } else {
                continue;
            }
        }

        if (isset($iserror)) {
            if ($iserror) {
                return redirect('/kependidikan/sertifiklas/cetak')->with(['error' => 'Gagal mengatur tanggal!']);
            } else {
                return redirect('/kependidikan/sertifiklas/cetak')->with(['sukses' => 'Berhasil mengatur tanggal']);
            }
        } else {
            return redirect('/kependidikan/serifiklas/cetak')->with(['error' => 'Data sertifikat tidak ditemukan']);
        }
    }

    public function sertifKepsek()
    {
        $tahunsekarang = TahunAjaran::where('is_active', 1)->latest()->first();
        $tahun = TahunAjaran::orderBy('created_at')->get();

        $sertif = SertifIklas::where([['academic_year_id', $tahunsekarang->id],['unit_id', auth()->user()->pegawai->unit->id]])->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name')->values();

        return view('penilaian.cetaksertifiklaskepsek', compact('tahunsekarang','tahun','sertif'));
    }

    public function sertifKepsekGetSiswa(Request $request)
    {
        $tahunajaran = str_replace("-","/",$request->tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $tahunsekarang = TahunAjaran::where('is_active', 1)->latest()->first();
        $sertif = SertifIklas::where([['academic_year_id', $aktif->id],['unit_id', $request->unit_id]])->get();

        $view = view('penilaian.getsiswasertifiklas',compact('tahunsekarang','sertif'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function sertifPrintKepsek(Request $request)
    {
        $iklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();
        $sertif = isset($request->id) ? SertifIklas::find($request->id) : null;
        if($sertif){
            $semester = $sertif->tahunAjaran->semester->where('semester','Genap')->first();
            $unit = $sertif->unit->id;
            $rapor = $sertif->siswa->nilaiRapor()->where([
                'semester_id' => $semester->id,
                'report_status_id' => 1,
                'report_status_pts_id' => 1,
                'unit_id' => $sertif->unit->id
            ])->first();
            $nilai = $rapor ? $rapor->iklas : null;
        }

        if($nilai) return view('penilaian.sertifikat_iklas', compact('iklas','sertif','nilai'));
        else return redirect('/kependidikan/sertifiklaskepsek');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\DeskripsiIndikator;
use App\Models\Penilaian\IndikatorAspek;
use App\Models\Penilaian\NilaiPASTK;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PasTK;
use App\Models\Penilaian\RefIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiIndikatorController extends Controller
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

        $siswa = Siswa::where('class_id', $class_id)->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        return view('penilaian.nilaiindikator', compact('siswa', 'kelas', 'semester'));
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
        $rpd = DeskripsiIndikator::where([['employee_id', $employee_id], ['is_deleted', 0]])->orderBy('predicate', 'ASC')->get();

        foreach ($aspek as $key => $aspeks) {
            $dataindikator = IndikatorAspek::where([['is_deleted', 0], ['development_aspect_id', $aspeks->id], ['level_id', $kelas->level_id]])->get();
            if ($dataindikator->isEmpty()) {
                $indikator[$key] = NULL;
            } else {
                $indikator[$key] = IndikatorAspek::where([['is_deleted', 0], ['development_aspect_id', $aspeks->id], ['level_id', $kelas->level_id]])->get();
            }
        }

        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $countnilaiindikator = PasTK::where([['score_id', $nilairapor->id]])->count();
            if ($countnilaiindikator > 0) {
                $nilaiindikator = PasTK::where([['score_id', $nilairapor->id]])->with('nilai')->first();
            } else {
                $nilaiindikator = FALSE;
            }
        } else {
            $nilaiindikator = FALSE;
        }
        $view = view('penilaian.getnilaiindikator')->with('aspek', $aspek)->with('nilaiindikator', $nilaiindikator)->with('siswa_id', $siswa_id)->with('rpd', $rpd)->with('indikator', $indikator)->render();
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
        $indikator_id = $request->indikator_id;
        $predikat = $request->predikat;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $namawali = auth()->user()->pegawai->name;
            $nilairapor->hr_name = $namawali;
            $countnilaiindikator = PasTK::where([['score_id', $nilairapor->id]])->with('nilai')->count();
            if ($nilairapor->update() && $countnilaiindikator == 0) {
                $nilaiindikator = PasTK::create([
                    'score_id' => $nilairapor->id,
                ]);
                if ($nilaiindikator->save()) {
                    $iserror = FALSE;
                    foreach ($indikator_id as $key => $indikator_ids) {
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $detailindikator = NilaiPASTK::create([
                            'report_final_kg_id' => $nilaiindikator->id,
                            'aspect_indicator_id' => $indikator_ids,
                            'indicator_description_id' => $rpd->id,
                            'predicate' => $rpd->predicate,
                            'description' => $rpd->description
                        ]);
                        if ($detailindikator->save()) {
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
                $nilaiindikator = PasTK::where('score_id', $nilairapor->id)->first();
                //$detailindikator = NilaiPASTK::where('report_final_kg_id', $nilaiindikator->id)->get();
                foreach ($indikator_id as $key => $indikator_ids) {
                    $detailindikators = NilaiPASTK::where([['report_final_kg_id', $nilaiindikator->id], ['aspect_indicator_id', $indikator_ids]]);
                    if($detailindikators->count() > 1) $detailindikators->orderBy('id','desc')->skip(1)->delete();
                    
                    $detailindikator = NilaiPASTK::where([['report_final_kg_id', $nilaiindikator->id], ['aspect_indicator_id', $indikator_ids]])->first();
                    if($detailindikator) {
                        $detailindikator->aspect_indicator_id = $indikator_ids;
                        $detailindikator->indicator_description_id = $predikat[$key];
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $detailindikator->predicate = $rpd->predicate;
                        $detailindikator->description = $rpd->description;
                        if ($detailindikator->update()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                    else{
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $newdetailindikator = NilaiPASTK::create([
                            'report_final_kg_id' => $nilaiindikator->id,
                            'aspect_indicator_id' => $indikator_ids,
                            'indicator_description_id' => $rpd->id,
                            'predicate' => $rpd->predicate,
                            'description' => $rpd->description
                        ]);
                        if ($newdetailindikator->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
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
                $nilaiindikator = PasTK::create([
                    'score_id' => $nilairapor->id,
                ]);
                if ($nilaiindikator->save()) {
                    $iserror = FALSE;
                    foreach ($indikator_id as $key => $indikator_ids) {
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $detailindikator = NilaiPASTK::create([
                            'report_final_kg_id' => $nilaiindikator->id,
                            'aspect_indicator_id' => $indikator_ids,
                            'indicator_description_id' => $rpd->id,
                            'predicate' => $rpd->predicate,
                            'description' => $rpd->description
                        ]);
                        if ($detailindikator->save()) {
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
            return redirect('/kependidikan/penilaiantk/nilaiindikator')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaiantk/nilaiindikator')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\DeskripsiIndikator;
use App\Models\Penilaian\IndikatorAspek;
use App\Models\Penilaian\NilaiPASTK;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PasTK;
use App\Models\Penilaian\RefIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiIndikatorController extends Controller
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

        $siswa = Siswa::where('class_id', $class_id)->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        return view('penilaian.nilaiindikator', compact('siswa', 'kelas', 'semester'));
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
        $rpd = DeskripsiIndikator::where([['employee_id', $employee_id], ['is_deleted', 0]])->orderBy('predicate', 'ASC')->get();

        foreach ($aspek as $key => $aspeks) {
            $dataindikator = IndikatorAspek::where([['is_deleted', 0], ['development_aspect_id', $aspeks->id], ['level_id', $kelas->level_id]])->get();
            if ($dataindikator->isEmpty()) {
                $indikator[$key] = NULL;
            } else {
                $indikator[$key] = IndikatorAspek::where([['is_deleted', 0], ['development_aspect_id', $aspeks->id], ['level_id', $kelas->level_id]])->get();
            }
        }

        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $countnilaiindikator = PasTK::where([['score_id', $nilairapor->id]])->count();
            if ($countnilaiindikator > 0) {
                $nilaiindikator = PasTK::where([['score_id', $nilairapor->id]])->with('nilai')->first();
            } else {
                $nilaiindikator = FALSE;
            }
        } else {
            $nilaiindikator = FALSE;
        }
        $view = view('penilaian.getnilaiindikator')->with('aspek', $aspek)->with('nilaiindikator', $nilaiindikator)->with('siswa_id', $siswa_id)->with('rpd', $rpd)->with('indikator', $indikator)->render();
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
        $indikator_id = $request->indikator_id;
        $predikat = $request->predikat;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $namawali = auth()->user()->pegawai->name;
            $nilairapor->hr_name = $namawali;
            $countnilaiindikator = PasTK::where([['score_id', $nilairapor->id]])->with('nilai')->count();
            if ($nilairapor->update() && $countnilaiindikator == 0) {
                $nilaiindikator = PasTK::create([
                    'score_id' => $nilairapor->id,
                ]);
                if ($nilaiindikator->save()) {
                    $iserror = FALSE;
                    foreach ($indikator_id as $key => $indikator_ids) {
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $detailindikator = NilaiPASTK::create([
                            'report_final_kg_id' => $nilaiindikator->id,
                            'aspect_indicator_id' => $indikator_ids,
                            'indicator_description_id' => $rpd->id,
                            'predicate' => $rpd->predicate,
                            'description' => $rpd->description
                        ]);
                        if ($detailindikator->save()) {
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
                $nilaiindikator = PasTK::where('score_id', $nilairapor->id)->first();
                //$detailindikator = NilaiPASTK::where('report_final_kg_id', $nilaiindikator->id)->get();
                foreach ($indikator_id as $key => $indikator_ids) {
                    $detailindikators = NilaiPASTK::where([['report_final_kg_id', $nilaiindikator->id], ['aspect_indicator_id', $indikator_ids]]);
                    if($detailindikators->count() > 1) $detailindikators->orderBy('id','desc')->skip(1)->delete();
                    
                    $detailindikator = NilaiPASTK::where([['report_final_kg_id', $nilaiindikator->id], ['aspect_indicator_id', $indikator_ids]])->first();
                    if($detailindikator) {
                        $detailindikator->aspect_indicator_id = $indikator_ids;
                        $detailindikator->indicator_description_id = $predikat[$key];
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $detailindikator->predicate = $rpd->predicate;
                        $detailindikator->description = $rpd->description;
                        if ($detailindikator->update()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
                    }
                    else{
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $newdetailindikator = NilaiPASTK::create([
                            'report_final_kg_id' => $nilaiindikator->id,
                            'aspect_indicator_id' => $indikator_ids,
                            'indicator_description_id' => $rpd->id,
                            'predicate' => $rpd->predicate,
                            'description' => $rpd->description
                        ]);
                        if ($newdetailindikator->save()) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                            break;
                        }
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
                $nilaiindikator = PasTK::create([
                    'score_id' => $nilairapor->id,
                ]);
                if ($nilaiindikator->save()) {
                    $iserror = FALSE;
                    foreach ($indikator_id as $key => $indikator_ids) {
                        $rpd = DeskripsiIndikator::where('id', $predikat[$key])->first();
                        $detailindikator = NilaiPASTK::create([
                            'report_final_kg_id' => $nilaiindikator->id,
                            'aspect_indicator_id' => $indikator_ids,
                            'indicator_description_id' => $rpd->id,
                            'predicate' => $rpd->predicate,
                            'description' => $rpd->description
                        ]);
                        if ($detailindikator->save()) {
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
            return redirect('/kependidikan/penilaiantk/nilaiindikator')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaiantk/nilaiindikator')->with(['gagal' => 'Data gagal disimpan']);
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

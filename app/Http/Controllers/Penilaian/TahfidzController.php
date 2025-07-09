<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Alquran\Juz;
use App\Models\Alquran\StatusHafalan;
use App\Models\Alquran\Surat;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use Illuminate\Http\Request;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Surah;
use App\Models\Penilaian\Tahfidz;
use App\Models\Penilaian\TargetTahfidz;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Rekrutmen\Pegawai;
use Illuminate\Support\Facades\Auth;

class TahfidzController extends Controller
{
    public function index()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $quran = MataPelajaran::where([['subject_name', 'like', "Qur'an"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $quran->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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

        return view('penilaian.hafalan', compact('semester', 'level', 'quran'));
    }

    public function getHafalan(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $subject_id = $request->mapel_id;

        $juz = Juz::orderBy('id','desc')->get();
        $surat = Surat::all();
        $status = StatusHafalan::orderBy('id','desc')->get();

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 6], ['employee_id', $employee_id], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $smt_aktif]])->orderBy('description', 'ASC')->get();
        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $countrapor = NilaiRapor::where([['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $validasi = NilaiRapor::where([['report_status_id', 0], ['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $counthafalan = Tahfidz::where([['score_id', $nilairapor->id]])->count();
            if ($counthafalan > 0) {
                $hafalan = Tahfidz::where([['score_id', $nilairapor->id]])->with('surah')->first();
            } else {
                $hafalan = FALSE;
            }
        } else {
            $hafalan = FALSE;
        }
        $view = view('penilaian.gethafalan')->with('hafalan', $hafalan)->with('siswa_id', $siswa_id)->with('rpd', $rpd)->with('nilairapor', $nilairapor)->with('validasi', $validasi)->with('juz', $juz)->with('surat', $surat)->with('status', $status)->render();
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
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        $view = view('penilaian.getsiswahafalan', compact('siswa'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function store(Request $request)
    {
        $class_id = $request->class_id;
        $smt_aktif = session("semester_aktif");
        $siswa_id = $request->siswa_id;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $siswass = Siswa::where('id', $siswa_id)->first();
            $kelas = Kelas::where('id', $siswass->class_id)->first();
            $wali = Pegawai::where('id', $kelas->teacher_id)->first();
            $namawali = $wali->name;
            $nilairapor->hr_name = $namawali;
            $countahfidz = Tahfidz::where('score_id', $nilairapor->id)->count();
            if ($nilairapor->update() && $countahfidz > 0) {
                $tahfidz = Tahfidz::where('score_id', $nilairapor->id)->first();
                $tahfidz->rpd_id = $request->deskripsi;
                if ($tahfidz->update()) {
                    $countsurah = Surah::where('report_tahfidz_id', $tahfidz->id)->count();

                    if ($countsurah > 0) {
                        $hapussurah = Surah::where('report_tahfidz_id', $tahfidz->id)->delete();

                        if ($hapussurah) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                        }
                    }
                    // if ($request->hafalan) {
                    //     $predikat = $request->predikat;
                    //     foreach ($request->hafalan as $key => $hafalan) {
                    //         $simpanhafalan = Surah::create([
                    //             'report_tahfidz_id' => $tahfidz->id,
                    //             'surah' => $hafalan,
                    //             'predicate' => $predikat[$key]
                    //         ]);
                    //         if ($simpanhafalan->save()) {
                    //             $iserror = FALSE;
                    //         } else {
                    //             $iserror = TRUE;
                    //             break;
                    //         }
                    //     }
                    // }
                    if ($request->jenis) {
                        $predikat = $request->predikat;
                        foreach ($request->jenis as $key => $jenis) {
                            if($jenis == 'surat'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'surah_id' => $request->surat[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            elseif($jenis == 'juz'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'juz_id' => $request->juz[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            if ($simpanhafalan->save()) {
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
            } else {
                $tahfidz = Tahfidz::create([
                    'score_id' => $nilairapor->id,
                    'rpd_id' => $request->deskripsi
                ]);
                if ($tahfidz->save()) {
                    if ($request->jenis) {
                        $predikat = $request->predikat;
                        foreach ($request->jenis as $key => $jenis) {
                            if($jenis == 'surat'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'surah_id' => $request->surat[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            elseif($jenis == 'juz'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'juz_id' => $request->juz[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            if ($simpanhafalan->save()) {
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
            $tahfidz = Tahfidz::create([
                'score_id' => $nilairapor->id,
                'rpd_id' => $request->deskripsi
            ]);
            if ($tahfidz->save()) {
                if ($request->jenis) {
                    $predikat = $request->predikat;
                    foreach ($request->jenis as $key => $jenis) {
                        if($jenis == 'surat'){
                            $simpanhafalan = Surah::create([
                                'report_tahfidz_id' => $tahfidz->id,
                                'surah_id' => $request->surat[$key],
                                'status_id' => $request->status[$key],
                                'predicate' => $predikat[$key]
                            ]);
                        }
                        elseif($jenis == 'juz'){
                            $simpanhafalan = Surah::create([
                                'report_tahfidz_id' => $tahfidz->id,
                                'juz_id' => $request->juz[$key],
                                'status_id' => $request->status[$key],
                                'predicate' => $predikat[$key]
                            ]);
                        }
                        if ($simpanhafalan->save()) {
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
            return redirect('/kependidikan/penilaianmapel/nilaihafalan')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaihafalan')->with(['gagal' => 'Data gagal disimpan']);
        }
    }

    public function desc()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $quran = MataPelajaran::where([['subject_name', 'like', "Qur'an"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $quran->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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

        return view('penilaian.deschafal', compact('semester', 'level', 'quran'));
    }

    public function getdesc(Request $request)
    {
        $mapel_id = $request->mapel_id;
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 6], ['employee_id', auth()->user()->pegawai->id], ['level_id', $request->level_id], ['semester_id', session('semester_aktif')], ['subject_id', $mapel_id]])->orderBy('created_at', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }

        $view = view('penilaian.getdeschafalan', compact('rpd'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpanDeskripsi(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
        ]);

        //Jenis predikat deskripsi untuk ekstra
        $rpd_type_id = 6;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = PredikatDeskripsi::create([
            'subject_id' => $request->mapel_id,
            'level_id' => $request->level_id,
            'semester_id' => session('semester_aktif'),
            'description' => $request->deskripsi,
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/deshafal')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusDeskripsi(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['error' => 'Data gagal dihapus']);
        }
    }

    //Ubah Predikat Deskripsi
    public function ubahDeskripsi(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->description = $request->deskripsi;

        if ($query->update()) {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['error' => 'Data gagal diubah']);
        }
    }

    public function target()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $quran = MataPelajaran::where([['subject_name', 'like', "Qur'an"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $quran->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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

        return view('penilaian.targettahfidz', compact('semester', 'level'));
    }

    public function gettarget(Request $request)
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $tahfidz = TargetTahfidz::where([['semester_id', $semester_id], ['level_id', $request->level_id], ['unit_id', $unit_id]])->first();
        if (!$tahfidz) {
            $tahfidz = FALSE;
        }

        $view = view('penilaian.gettarget', compact('tahfidz'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpanTarget(Request $request)
    {
        $semester_id = session('semester_aktif');

        $tahfidz = TargetTahfidz::where([['semester_id', $semester_id], ['level_id', $request->idlevel], ['unit_id', $request->user()->pegawai->unit_id]])->first();
        if (isset($request->tahfidz_id) && $tahfidz != NULL) {
            $tahfidz = TargetTahfidz::where('id', $request->tahfidz_id)->first();
            $tahfidz->target = $request->target;
            if ($tahfidz->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $tahfidz = TargetTahfidz::create([
                'level_id' => $request->idlevel,
                'semester_id' => $semester_id,
                'unit_id' => $request->user()->pegawai->unit_id,
                'target' => $request->target
            ]);
            if ($tahfidz->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/targettahfidz')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/targettahfidz')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Alquran\Juz;
use App\Models\Alquran\StatusHafalan;
use App\Models\Alquran\Surat;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use Illuminate\Http\Request;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Siswa\Siswa;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Surah;
use App\Models\Penilaian\Tahfidz;
use App\Models\Penilaian\TargetTahfidz;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Rekrutmen\Pegawai;
use Illuminate\Support\Facades\Auth;

class TahfidzController extends Controller
{
    public function index()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $quran = MataPelajaran::where([['subject_name', 'like', "Qur'an"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $quran->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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

        return view('penilaian.hafalan', compact('semester', 'level', 'quran'));
    }

    public function getHafalan(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $smt_aktif = session('semester_aktif');
        $siswa_id = $request->siswa_id;
        $subject_id = $request->mapel_id;

        $juz = Juz::orderBy('id','desc')->get();
        $surat = Surat::all();
        $status = StatusHafalan::orderBy('id','desc')->get();

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 6], ['employee_id', $employee_id], ['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $smt_aktif]])->orderBy('description', 'ASC')->get();
        $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $countrapor = NilaiRapor::where([['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        $validasi = NilaiRapor::where([['report_status_id', 0], ['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $request->siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $counthafalan = Tahfidz::where([['score_id', $nilairapor->id]])->count();
            if ($counthafalan > 0) {
                $hafalan = Tahfidz::where([['score_id', $nilairapor->id]])->with('surah')->first();
            } else {
                $hafalan = FALSE;
            }
        } else {
            $hafalan = FALSE;
        }
        $view = view('penilaian.gethafalan')->with('hafalan', $hafalan)->with('siswa_id', $siswa_id)->with('rpd', $rpd)->with('nilairapor', $nilairapor)->with('validasi', $validasi)->with('juz', $juz)->with('surat', $surat)->with('status', $status)->render();
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
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }
        $view = view('penilaian.getsiswahafalan', compact('siswa'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function store(Request $request)
    {
        $class_id = $request->class_id;
        $smt_aktif = session("semester_aktif");
        $siswa_id = $request->siswa_id;
        $iserror = FALSE;

        $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        if ($nilairapor > 0) {
            $nilairapor = NilaiRapor::where([['student_id', $siswa_id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
            $siswass = Siswa::where('id', $siswa_id)->first();
            $kelas = Kelas::where('id', $siswass->class_id)->first();
            $wali = Pegawai::where('id', $kelas->teacher_id)->first();
            $namawali = $wali->name;
            $nilairapor->hr_name = $namawali;
            $countahfidz = Tahfidz::where('score_id', $nilairapor->id)->count();
            if ($nilairapor->update() && $countahfidz > 0) {
                $tahfidz = Tahfidz::where('score_id', $nilairapor->id)->first();
                $tahfidz->rpd_id = $request->deskripsi;
                if ($tahfidz->update()) {
                    $countsurah = Surah::where('report_tahfidz_id', $tahfidz->id)->count();

                    if ($countsurah > 0) {
                        $hapussurah = Surah::where('report_tahfidz_id', $tahfidz->id)->delete();

                        if ($hapussurah) {
                            $iserror = FALSE;
                        } else {
                            $iserror = TRUE;
                        }
                    }
                    // if ($request->hafalan) {
                    //     $predikat = $request->predikat;
                    //     foreach ($request->hafalan as $key => $hafalan) {
                    //         $simpanhafalan = Surah::create([
                    //             'report_tahfidz_id' => $tahfidz->id,
                    //             'surah' => $hafalan,
                    //             'predicate' => $predikat[$key]
                    //         ]);
                    //         if ($simpanhafalan->save()) {
                    //             $iserror = FALSE;
                    //         } else {
                    //             $iserror = TRUE;
                    //             break;
                    //         }
                    //     }
                    // }
                    if ($request->jenis) {
                        $predikat = $request->predikat;
                        foreach ($request->jenis as $key => $jenis) {
                            if($jenis == 'surat'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'surah_id' => $request->surat[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            elseif($jenis == 'juz'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'juz_id' => $request->juz[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            if ($simpanhafalan->save()) {
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
            } else {
                $tahfidz = Tahfidz::create([
                    'score_id' => $nilairapor->id,
                    'rpd_id' => $request->deskripsi
                ]);
                if ($tahfidz->save()) {
                    if ($request->jenis) {
                        $predikat = $request->predikat;
                        foreach ($request->jenis as $key => $jenis) {
                            if($jenis == 'surat'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'surah_id' => $request->surat[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            elseif($jenis == 'juz'){
                                $simpanhafalan = Surah::create([
                                    'report_tahfidz_id' => $tahfidz->id,
                                    'juz_id' => $request->juz[$key],
                                    'status_id' => $request->status[$key],
                                    'predicate' => $predikat[$key]
                                ]);
                            }
                            if ($simpanhafalan->save()) {
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
            $tahfidz = Tahfidz::create([
                'score_id' => $nilairapor->id,
                'rpd_id' => $request->deskripsi
            ]);
            if ($tahfidz->save()) {
                if ($request->jenis) {
                    $predikat = $request->predikat;
                    foreach ($request->jenis as $key => $jenis) {
                        if($jenis == 'surat'){
                            $simpanhafalan = Surah::create([
                                'report_tahfidz_id' => $tahfidz->id,
                                'surah_id' => $request->surat[$key],
                                'status_id' => $request->status[$key],
                                'predicate' => $predikat[$key]
                            ]);
                        }
                        elseif($jenis == 'juz'){
                            $simpanhafalan = Surah::create([
                                'report_tahfidz_id' => $tahfidz->id,
                                'juz_id' => $request->juz[$key],
                                'status_id' => $request->status[$key],
                                'predicate' => $predikat[$key]
                            ]);
                        }
                        if ($simpanhafalan->save()) {
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
            return redirect('/kependidikan/penilaianmapel/nilaihafalan')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaihafalan')->with(['gagal' => 'Data gagal disimpan']);
        }
    }

    public function desc()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $quran = MataPelajaran::where([['subject_name', 'like', "Qur'an"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $quran->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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

        return view('penilaian.deschafal', compact('semester', 'level', 'quran'));
    }

    public function getdesc(Request $request)
    {
        $mapel_id = $request->mapel_id;
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 6], ['employee_id', auth()->user()->pegawai->id], ['level_id', $request->level_id], ['semester_id', session('semester_aktif')], ['subject_id', $mapel_id]])->orderBy('created_at', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }

        $view = view('penilaian.getdeschafalan', compact('rpd'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpanDeskripsi(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
        ]);

        //Jenis predikat deskripsi untuk ekstra
        $rpd_type_id = 6;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = PredikatDeskripsi::create([
            'subject_id' => $request->mapel_id,
            'level_id' => $request->level_id,
            'semester_id' => session('semester_aktif'),
            'description' => $request->deskripsi,
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/deshafal')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusDeskripsi(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['error' => 'Data gagal dihapus']);
        }
    }

    //Ubah Predikat Deskripsi
    public function ubahDeskripsi(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->description = $request->deskripsi;

        if ($query->update()) {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaianmapel/deschafal')->with(['error' => 'Data gagal diubah']);
        }
    }

    public function target()
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $quran = MataPelajaran::where([['subject_name', 'like', "Qur'an"], ['unit_id', $unit_id]])->first();
        $jadwal = JadwalPelajaran::where([['subject_id', $quran->id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->groupBy('level_id')->get();
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

        return view('penilaian.targettahfidz', compact('semester', 'level'));
    }

    public function gettarget(Request $request)
    {
        $semester_id = session('semester_aktif');
        $unit_id = auth()->user()->pegawai->unit_id;
        $tahfidz = TargetTahfidz::where([['semester_id', $semester_id], ['level_id', $request->level_id], ['unit_id', $unit_id]])->first();
        if (!$tahfidz) {
            $tahfidz = FALSE;
        }

        $view = view('penilaian.gettarget', compact('tahfidz'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpanTarget(Request $request)
    {
        $semester_id = session('semester_aktif');

        $tahfidz = TargetTahfidz::where([['semester_id', $semester_id], ['level_id', $request->idlevel], ['unit_id', $request->user()->pegawai->unit_id]])->first();
        if (isset($request->tahfidz_id) && $tahfidz != NULL) {
            $tahfidz = TargetTahfidz::where('id', $request->tahfidz_id)->first();
            $tahfidz->target = $request->target;
            if ($tahfidz->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $tahfidz = TargetTahfidz::create([
                'level_id' => $request->idlevel,
                'semester_id' => $semester_id,
                'unit_id' => $request->user()->pegawai->unit_id,
                'target' => $request->target
            ]);
            if ($tahfidz->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/targettahfidz')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/targettahfidz')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

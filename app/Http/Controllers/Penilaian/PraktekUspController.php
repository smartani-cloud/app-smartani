<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\NilaiPraktekUsp;
use App\Models\Penilaian\NilaiSKHB;
use App\Models\Penilaian\SKHB;
use App\Models\Siswa\Siswa;
use App\Models\Skbm\Skbm;
use Illuminate\Http\Request;

class PraktekUspController extends Controller
{
    public function praktek()
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

        return view('penilaian.nilaipraktek', compact('mapel', 'semester', 'level'));
    }

    public function usp()
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

        return view('penilaian.nilaiusp', compact('mapel', 'semester', 'level'));
    }

    public function getkelas(Request $request)
    {
        $jadwal = JadwalPelajaran::where([['level_id', $request->level_id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->get();
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

    public function getsiswapraktek(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit_id = auth()->user()->pegawai->unit_id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            foreach ($siswa as $key => $siswas) {
                $countnilai = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 1]])->count();
                if ($countnilai > 0) {
                    $nilaipraktek[$key] = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 1]])->first();
                } else {
                    $nilaipraktek[$key] = FALSE;
                }
            }
        }

        $view = view('penilaian.getpraktek', compact('siswa', 'nilaipraktek'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswausp(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit_id = auth()->user()->pegawai->unit_id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            foreach ($siswa as $key => $siswas) {
                $countnilai = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 2]])->count();
                if ($countnilai > 0) {
                    $nilaiusp[$key] = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 2]])->first();
                } else {
                    $nilaiusp[$key] = FALSE;
                }
            }
        }

        $view = view('penilaian.getusp', compact('siswa', 'nilaiusp'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function storepraktek(Request $request)
    {
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $praktek = $request->praktek;
        $semester_id = session('semester_aktif');

        foreach ($siswa_id as $key => $siswas_id) {
            $ceknilai = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 1]])->count();
            if ($ceknilai > 0) {
                $nilaipraktek = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 1]])->first();
                $nilaipraktek->score = $praktek[$key];
                if ($nilaipraktek->update()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            } else {
                $nilaipraktek = NilaiPraktekUsp::create([
                    'student_id' => $siswas_id,
                    'class_id' => $class_id,
                    'semester_id' => $semester_id,
                    'subject_id' => $subject_id,
                    'score' => $praktek[$key],
                    'type' => 1
                ]);
                if ($nilaipraktek->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/ijazahmapel/nilaipraktek')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/ijazahmapel/nilaipraktek')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function storeusp(Request $request)
    {
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $usp = $request->usp;
        $semester_id = session('semester_aktif');

        foreach ($siswa_id as $key => $siswas_id) {
            $ceknilai = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 2]])->count();
            if ($ceknilai > 0) {
                $nilaiusp = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 2]])->first();
                $nilaiusp->score = $usp[$key];
                if ($nilaiusp->update()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            } else {
                $nilaiusp = NilaiPraktekUsp::create([
                    'student_id' => $siswas_id,
                    'class_id' => $class_id,
                    'semester_id' => $semester_id,
                    'subject_id' => $subject_id,
                    'score' => $usp[$key],
                    'type' => 2
                ]);
                if ($nilaiusp->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/ijazahmapel/nilaiusp')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/ijazahmapel/nilaiusp')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\NilaiPraktekUsp;
use App\Models\Penilaian\NilaiSKHB;
use App\Models\Penilaian\SKHB;
use App\Models\Siswa\Siswa;
use App\Models\Skbm\Skbm;
use Illuminate\Http\Request;

class PraktekUspController extends Controller
{
    public function praktek()
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

        return view('penilaian.nilaipraktek', compact('mapel', 'semester', 'level'));
    }

    public function usp()
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

        return view('penilaian.nilaiusp', compact('mapel', 'semester', 'level'));
    }

    public function getkelas(Request $request)
    {
        $jadwal = JadwalPelajaran::where([['level_id', $request->level_id], ['teacher_id', auth()->user()->pegawai->id], ['semester_id', session('semester_aktif')]])->get();
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

    public function getsiswapraktek(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit_id = auth()->user()->pegawai->unit_id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            foreach ($siswa as $key => $siswas) {
                $countnilai = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 1]])->count();
                if ($countnilai > 0) {
                    $nilaipraktek[$key] = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 1]])->first();
                } else {
                    $nilaipraktek[$key] = FALSE;
                }
            }
        }

        $view = view('penilaian.getpraktek', compact('siswa', 'nilaipraktek'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswausp(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit_id = auth()->user()->pegawai->unit_id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = session('semester_aktif');

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            foreach ($siswa as $key => $siswas) {
                $countnilai = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 2]])->count();
                if ($countnilai > 0) {
                    $nilaiusp[$key] = NilaiPraktekUsp::where([['student_id', $siswas->id], ['subject_id', $subject_id], ['semester_id', $semester_id], ['type', 2]])->first();
                } else {
                    $nilaiusp[$key] = FALSE;
                }
            }
        }

        $view = view('penilaian.getusp', compact('siswa', 'nilaiusp'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function storepraktek(Request $request)
    {
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $praktek = $request->praktek;
        $semester_id = session('semester_aktif');

        foreach ($siswa_id as $key => $siswas_id) {
            $ceknilai = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 1]])->count();
            if ($ceknilai > 0) {
                $nilaipraktek = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 1]])->first();
                $nilaipraktek->score = $praktek[$key];
                if ($nilaipraktek->update()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            } else {
                $nilaipraktek = NilaiPraktekUsp::create([
                    'student_id' => $siswas_id,
                    'class_id' => $class_id,
                    'semester_id' => $semester_id,
                    'subject_id' => $subject_id,
                    'score' => $praktek[$key],
                    'type' => 1
                ]);
                if ($nilaipraktek->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/ijazahmapel/nilaipraktek')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/ijazahmapel/nilaipraktek')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function storeusp(Request $request)
    {
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $usp = $request->usp;
        $semester_id = session('semester_aktif');

        foreach ($siswa_id as $key => $siswas_id) {
            $ceknilai = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 2]])->count();
            if ($ceknilai > 0) {
                $nilaiusp = NilaiPraktekUsp::where([['student_id', $siswas_id], ['class_id', $class_id], ['semester_id', $semester_id], ['subject_id', $subject_id], ['type', 2]])->first();
                $nilaiusp->score = $usp[$key];
                if ($nilaiusp->update()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            } else {
                $nilaiusp = NilaiPraktekUsp::create([
                    'student_id' => $siswas_id,
                    'class_id' => $class_id,
                    'semester_id' => $semester_id,
                    'subject_id' => $subject_id,
                    'score' => $usp[$key],
                    'type' => 2
                ]);
                if ($nilaiusp->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/ijazahmapel/nilaiusp')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/ijazahmapel/nilaiusp')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

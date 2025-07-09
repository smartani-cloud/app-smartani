<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\RangePredikat;
use App\Models\Skbm\Skbm;
use Illuminate\Http\Request;

class RangePredikatController extends Controller
{
    public function index()
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

        return view('penilaian.rangenilai', compact('semester', 'mapel', 'level'));
    }

    public function indexkepsek()
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit = auth()->user()->pegawai->unit->id;
        $semester_id = session('semester_aktif');
        $mapel = MataPelajaran::where('unit_id', $unit)->get();
        $semester = Semester::where('id', $semester_id)->first();
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.rangenilaikepsek', compact('semester', 'mapel', 'level'));
    }


    public function levelkepsek(Request $request)
    {
        $semester_id = session('semester_aktif');

        $level = Level::where('unit_id', auth()->user()->pegawai->unit_id)->get();

        $view = view('penilaian.getlevelkd', compact('level', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function level(Request $request)
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

    public function getrange(Request $request)
    {
        $semester_id = session('semester_aktif');
        $countrange = RangePredikat::where([['subject_id', $request->mapel_id], ['level_id', $request->level_id], ['semester_id', $semester_id]])->count();
        if ($countrange == 0) {
            $rangenilai = FALSE;
            $range_a = FALSE;
            $range_b = FALSE;
            $range_c = FALSE;
            $range_d = FALSE;
        } else {
            $rangenilai = RangePredikat::where([['subject_id', $request->mapel_id], ['level_id', $request->level_id], ['semester_id', $semester_id]])->first();
            $range_a = $rangenilai->range_a;
            $range_b = $rangenilai->range_b;
            $range_c = $rangenilai->range_c;
            $range_d = $rangenilai->range_d;
        }
        $view = view('penilaian.getrange', compact('rangenilai', 'range_a', 'range_b', 'range_c', 'range_d'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }


    public function simpan(Request $request)
    {
        $subject_id = $request->mapel;
        $level_id = $request->kelas;
        $range_a = $request->range_a;
        $range_b = $request->range_b;
        $range_c = $request->range_c;
        $range_d = $request->range_d;
        $semester_id = session('semester_aktif');

        $countrange = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->count();
        if ($countrange == 0) {
            $rangenilai = RangePredikat::create([
                'subject_id' => $subject_id,
                'level_id' => $level_id,
                'semester_id' => $semester_id,
                'range_a' => $range_a,
                'range_b' => $range_b,
                'range_c' => $range_c,
                'range_d' => $range_d
            ]);

            if ($rangenilai->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
            $rangenilai->range_a = $range_a;
            $rangenilai->range_b = $range_b;
            $rangenilai->range_c = $range_c;
            $rangenilai->range_d = $range_d;

            if ($rangenilai->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/rangepredikat')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/rangepredikat')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function simpankepsek(Request $request)
    {
        $subject_id = $request->mapel;
        $level_id = $request->kelas;
        $range_a = $request->range_a;
        $range_b = $request->range_b;
        $range_c = $request->range_c;
        $range_d = $request->range_d;
        $semester_id = session('semester_aktif');

        $countrange = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->count();
        if ($countrange == 0) {
            $rangenilai = RangePredikat::create([
                'subject_id' => $subject_id,
                'level_id' => $level_id,
                'semester_id' => $semester_id,
                'range_a' => $range_a,
                'range_b' => $range_b,
                'range_c' => $range_c,
                'range_d' => $range_d
            ]);

            if ($rangenilai->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
            $rangenilai->range_a = $range_a;
            $rangenilai->range_b = $range_b;
            $rangenilai->range_c = $range_c;
            $rangenilai->range_d = $range_d;

            if ($rangenilai->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaiankepsek/rangepredikat')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/rangepredikat')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\RangePredikat;
use App\Models\Skbm\Skbm;
use Illuminate\Http\Request;

class RangePredikatController extends Controller
{
    public function index()
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

        return view('penilaian.rangenilai', compact('semester', 'mapel', 'level'));
    }

    public function indexkepsek()
    {
        $employee_id = auth()->user()->pegawai->id;
        $unit = auth()->user()->pegawai->unit->id;
        $semester_id = session('semester_aktif');
        $mapel = MataPelajaran::where('unit_id', $unit)->get();
        $semester = Semester::where('id', $semester_id)->first();
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.rangenilaikepsek', compact('semester', 'mapel', 'level'));
    }


    public function levelkepsek(Request $request)
    {
        $semester_id = session('semester_aktif');

        $level = Level::where('unit_id', auth()->user()->pegawai->unit_id)->get();

        $view = view('penilaian.getlevelkd', compact('level', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function level(Request $request)
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

    public function getrange(Request $request)
    {
        $semester_id = session('semester_aktif');
        $countrange = RangePredikat::where([['subject_id', $request->mapel_id], ['level_id', $request->level_id], ['semester_id', $semester_id]])->count();
        if ($countrange == 0) {
            $rangenilai = FALSE;
            $range_a = FALSE;
            $range_b = FALSE;
            $range_c = FALSE;
            $range_d = FALSE;
        } else {
            $rangenilai = RangePredikat::where([['subject_id', $request->mapel_id], ['level_id', $request->level_id], ['semester_id', $semester_id]])->first();
            $range_a = $rangenilai->range_a;
            $range_b = $rangenilai->range_b;
            $range_c = $rangenilai->range_c;
            $range_d = $rangenilai->range_d;
        }
        $view = view('penilaian.getrange', compact('rangenilai', 'range_a', 'range_b', 'range_c', 'range_d'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }


    public function simpan(Request $request)
    {
        $subject_id = $request->mapel;
        $level_id = $request->kelas;
        $range_a = $request->range_a;
        $range_b = $request->range_b;
        $range_c = $request->range_c;
        $range_d = $request->range_d;
        $semester_id = session('semester_aktif');

        $countrange = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->count();
        if ($countrange == 0) {
            $rangenilai = RangePredikat::create([
                'subject_id' => $subject_id,
                'level_id' => $level_id,
                'semester_id' => $semester_id,
                'range_a' => $range_a,
                'range_b' => $range_b,
                'range_c' => $range_c,
                'range_d' => $range_d
            ]);

            if ($rangenilai->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
            $rangenilai->range_a = $range_a;
            $rangenilai->range_b = $range_b;
            $rangenilai->range_c = $range_c;
            $rangenilai->range_d = $range_d;

            if ($rangenilai->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/rangepredikat')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/rangepredikat')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function simpankepsek(Request $request)
    {
        $subject_id = $request->mapel;
        $level_id = $request->kelas;
        $range_a = $request->range_a;
        $range_b = $request->range_b;
        $range_c = $request->range_c;
        $range_d = $request->range_d;
        $semester_id = session('semester_aktif');

        $countrange = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->count();
        if ($countrange == 0) {
            $rangenilai = RangePredikat::create([
                'subject_id' => $subject_id,
                'level_id' => $level_id,
                'semester_id' => $semester_id,
                'range_a' => $range_a,
                'range_b' => $range_b,
                'range_c' => $range_c,
                'range_d' => $range_d
            ]);

            if ($rangenilai->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        } else {
            $rangenilai = RangePredikat::where([['subject_id', $subject_id], ['level_id', $level_id], ['semester_id', $semester_id]])->first();
            $rangenilai->range_a = $range_a;
            $rangenilai->range_b = $range_b;
            $rangenilai->range_c = $range_c;
            $rangenilai->range_d = $range_d;

            if ($rangenilai->update()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaiankepsek/rangepredikat')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/rangepredikat')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\DeskripsiAspek;
use Illuminate\Http\Request;

class AspekController extends Controller
{
    public function index()
    {
        $semester_id = session('semester_aktif');
        $semester = Semester::where('id', $semester_id)->first();
        $level = Level::where('unit_id', 1)->get();
        $kelasampu = Kelas::where('teacher_id', auth()->user()->id)->latest()->first();
        $aspek = AspekPerkembangan::where('is_deleted', 0)->get();
        $desc = DeskripsiAspek::where('level_id', $kelasampu->level_id)->where('is_deleted', 0)->orderBy('development_aspect_id', 'asc')->get();

        if ($level->isEmpty()) {
            $level = FALSE;
        }

        return view('penilaian.descaspek', compact('semester', 'level', 'kelasampu', 'aspek', 'desc'));
    }

    public function getubah(Request $request)
    {
        $data = DeskripsiAspek::where('id', $request->id)->first();
        $aspek = AspekPerkembangan::where('is_deleted', 0)->get();

        $view = view('penilaian.getubahdescaspek', compact('data', 'aspek'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getdeskripsi(Request $request)
    {
        $level_id = $request->level_id;

        $desc = DeskripsiAspek::where('level_id', $level_id)->where('is_deleted', 0)->orderBy('development_aspect_id', 'asc')->get();

        $view = view('penilaian.getdescaspek', compact('desc'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpan(Request $request)
    {
        $level_id = $request->level_id;
        $aspek_id = $request->aspek_id;
        $predikat = $request->predikat;
        $deskripsi = $request->deskripsi;

        $query = DeskripsiAspek::create([
            'level_id' => $level_id,
            'development_aspect_id' => $aspek_id,
            'predicate' => $predikat,
            'description' => $deskripsi
        ]);

        if ($query->save()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiantk/descaspek')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaiantk/descaspek')->with(['error' => 'Data gagal disimpan']);
        }
    }

    public function ubah(Request $request)
    {
        $indikator_id = $request->id;
        $aspek_id = $request->aspek_id;
        $predikat = $request->predikat;
        $deskripsi = $request->deskripsi;

        $query = DeskripsiAspek::where('id', $indikator_id)->first();
        $query->description = $deskripsi;
        $query->predicate = $predikat;
        $query->development_aspect_id = $aspek_id;

        if ($query->update()) {
            return redirect('/kependidikan/penilaiantk/descaspek')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaiantk/descaspek')->with(['error' => 'Data gagal diubah']);
        }
    }

    public function hapus(Request $request)
    {
        $desc_id = $request->id;

        $query = DeskripsiAspek::where('id', $desc_id)->first();
        $query->is_deleted = 1;

        if ($query->update()) {
            return redirect('/kependidikan/penilaiantk/descaspek')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaiantk/descaspek')->with(['error' => 'Data gagal dihapus']);
        }
    }
}

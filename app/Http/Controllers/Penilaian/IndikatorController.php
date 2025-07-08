<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Level;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\DeskripsiIndikator;
use App\Models\Penilaian\IndikatorAspek;
use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function index()
    {
        $semester_id = session('semester_aktif');
        $semester = Semester::where('id', $semester_id)->first();

        $kelas = auth()->user()->pegawai->kelas->where('academic_year_id', $semester->tahunAjaran->id)->first();

        $level = $kelas ? Level::find($kelas->level_id) : null;
        $aspek = AspekPerkembangan::where('is_deleted', 0)->get();

        $indikator = $level ? IndikatorAspek::where('level_id', $level->id)->where('is_deleted', 0)->orderBy('development_aspect_id', 'asc')->get() : null;

        return view('penilaian.indikator', compact('semester', 'level', 'aspek', 'indikator'));
    }

    public function getindikator(Request $request)
    {
        $level_id = $request->level_id;

        $indikator = IndikatorAspek::where('level_id', $level_id)->where('is_deleted', 0)->orderBy('development_aspect_id', 'asc')->get();

        $view = view('penilaian.getindikator', compact('indikator'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpan(Request $request)
    {
        $level_id = $request->level_id;
        $aspek_id = $request->aspek_id;
        $indikator = $request->indikator;

        $query = IndikatorAspek::create([
            'level_id' => $level_id,
            'development_aspect_id' => $aspek_id,
            'indicator' => $indikator
        ]);

        if ($query->save()) {
            return redirect('/kependidikan/penilaiantk/indikator')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaiantk/indikator')->with(['error' => 'Data gagal disimpan']);
        }
    }

    public function ubah(Request $request)
    {
        $indikator_id = $request->id;
        $aspek_id = $request->aspek_id;
        $indikator = $request->indikator;

        $query = IndikatorAspek::where('id', $indikator_id)->first();
        $query->indicator = $indikator;
        $query->development_aspect_id = $aspek_id;

        if ($query->update()) {
            return redirect('/kependidikan/penilaiantk/indikator')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaiantk/indikator')->with(['error' => 'Data gagal diubah']);
        }
    }

    public function hapus(Request $request)
    {
        $indikator_id = $request->id;

        $query = IndikatorAspek::where('id', $indikator_id)->first();
        $query->is_deleted = 1;

        if ($query->update()) {
            return redirect('/kependidikan/penilaiantk/indikator')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaiantk/indikator')->with(['error' => 'Data gagal dihapus']);
        }
    }

    public function desc()
    {

        $employee_id = auth()->user()->pegawai->id;
        $desc = DeskripsiIndikator::where([['employee_id', $employee_id], ['is_deleted', 0]])->orderBy('predicate', 'ASC')->get();

        if ($desc->isEmpty()) {
            $desc = FALSE;
        }

        return view('penilaian.descindikator', compact('desc'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpandesc(Request $request)
    {
        $request->validate([
            'predikat' => 'required',
            'deskripsi' => 'required',
        ]);

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = DeskripsiIndikator::create([
            'predicate' => $request->predikat,
            'description' => $request->deskripsi,
            'employee_id' => $employee_id,
            'is_deleted' => 0
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaiantk/descindikator')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaiantk/descindikator')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function ubahdesc(Request $request)
    {
        $query = DeskripsiIndikator::where('id', $request->id)->first();

        $query->predicate = $request->predikat;
        $query->description = $request->deskripsi;

        if ($query->update()) {
            return redirect('/kependidikan/penilaiantk/descindikator')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaiantk/descindikator')->with(['error' => 'Data gagal diubah']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusdesc(Request $request)
    {

        $query = DeskripsiIndikator::where('id', $request->id)->first();
        $query->is_deleted = 1;
        if ($query->update()) {
            return redirect('/kependidikan/penilaiantk/descindikator')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaiantk/descindikator')->with(['error' => 'Data gagal dihapus']);
        }
    }
}

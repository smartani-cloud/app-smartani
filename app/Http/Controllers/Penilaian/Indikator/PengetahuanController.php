<?php

namespace App\Http\Controllers\Penilaian\Indikator;

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

//Baru
use App\Models\Penilaian\IndikatorPengetahuan;
use App\Models\Penilaian\IndikatorPengetahuanDetail;


class PengetahuanController extends Controller
{
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $skbm = Skbm::aktif()->where('unit_id', auth()->user()->pegawai->unit_id)->first();
        $mapelskbm = $skbm->detail->where('employee_id', $employee_id)->pluck('subject_id');
        $semester = Semester::where('is_active', 1)->first();
        if (empty($mapelskbm)) {
            $mapel = NULL;
        } else {
            $mapel = MataPelajaran::whereIn('id', $mapelskbm)->where('subject_name', 'not like', "Qur'an")->get();
        }
        $semester = Semester::where('id', $semester->id)->first();
        $unit = auth()->user()->pegawai->unit->id;
        $level = auth()->user()->pegawai->jadwalPelajarans()->select('id','semester_id','level_id','class_id','teacher_id','subject_id')->where('semester_id', $semester->id)->whereIn('subject_id',$mapel->pluck('id'))->with('level')->get()->pluck('level')->unique();
        $indikator = IndikatorPengetahuan::where('semester_id', $semester->id)->whereIn('level_id',$level->pluck('id'))->whereIn('subject_id',$mapel->pluck('id'))->has('detail')->get();

        return view('penilaian.indikator.pengetahuan_index', compact('indikator', 'mapel','level'));
    }

    public function tambahindikator(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester = Semester::where('is_active', 1)->first();

        $countindikator = IndikatorPengetahuan::where([['semester_id', $semester->id], ['level_id', $request->level], ['subject_id', $request->subject]])->count();
        if ($countindikator == 0) {
            $buatindikator = IndikatorPengetahuan::create([
                'semester_id' => $semester->id,
                'level_id' => $request->level,
                'subject_id' => $request->subject
            ]);
            if ($buatindikator->save()) {
                $detail = IndikatorPengetahuanDetail::create([
                    'rki_id' => $buatindikator->id,
                    'indicator' => $request->indikator,
                    'employee_id' => $employee_id
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
            $indikator = IndikatorPengetahuan::where([['semester_id', $semester->id], ['level_id', $request->level], ['subject_id', $request->subject]])->first();
            $detail = IndikatorPengetahuanDetail::create([
                    'rki_id' => $indikator->id,
                    'indicator' => $request->indikator,
                    'employee_id' => $employee_id
            ]);
            if ($detail->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/indikator/mapel')->with(['error' => 'Data gagal disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/indikator/mapel')->with(['sukses' => 'Data berhasil disimpan']);
        }
    }

    public function ubahindikator(Request $request)
    {
        $detail = IndikatorPengetahuanDetail::where('id', $request->id)->first();
        $detail->indicator = $request->indikator;
        $detail->employee_id = $request->user()->pegawai->id;
        if ($detail->update()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror) {
            return redirect('/kependidikan/penilaianmapel/indikator/mapel')->with(['error' => 'Data gagal diubah']);
        } else {
            return redirect('/kependidikan/penilaianmapel/indikator/mapel')->with(['sukses' => 'Data berhasil diubah']);
        }
    }

    public function hapusindikator(Request $request)
    {
        $query = IndikatorPengetahuanDetail::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaianmapel/indikator/mapel')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaianmapel/indikator/mapel')->with(['error' => 'Data gagal dihapus']);
        }
    }
}

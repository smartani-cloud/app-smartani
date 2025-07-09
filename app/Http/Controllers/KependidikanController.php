<?php

namespace App\Http\Controllers;

use App\Http\Services\Psb\CounterPsb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Unit;

class KependidikanController extends Controller
{
    public function index(Request $request)
    {

        $datas = CounterPsb::list($request);
        $unit = Auth::user()->pegawai->unit_id;

        // jml siswa
        if ($unit == 5) {
            $namaunit = "TK, SD, SMP, SMA";
            $jmlsiswa = Siswa::whereHas('identitas', function($q){
                $q->where('is_lulus', 0);
            })->count();
            $jmlsiswalaki = Siswa::whereHas('identitas', function($q){
                $q->where('gender_id', 1)->where('is_lulus', 0);
            })->count();
            $jmlsiswacewe = Siswa::whereHas('identitas', function($q){
                $q->where('gender_id', 2)->where('is_lulus', 0);
            })->count();
            $jmlsiswalaki1 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 1)->where('is_lulus', 0)->where('gender_id', 1);
            })->count();
            $jmlsiswacewe1 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 1)->where('is_lulus', 0)->where('gender_id', 2);
            })->count();
            $jmlsiswalaki2 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 2)->where('is_lulus', 0)->where('gender_id', 1);
            })->count();
            $jmlsiswacewe2 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 2)->where('is_lulus', 0)->where('gender_id', 2);
            })->count();
            $jmlsiswalaki3 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 3)->where('is_lulus', 0)->where('gender_id', 1);
            })->count();
            $jmlsiswacewe3 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 3)->where('is_lulus', 0)->where('gender_id', 2);
            })->count();
            $jmlsiswalaki4 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 4)->where('is_lulus', 0)->where('gender_id', 1);
            })->count();
            $jmlsiswacewe4 = Siswa::whereHas('identitas', function($q){
                $q->where('unit_id', 4)->where('is_lulus', 0)->where('gender_id', 2);
            })->count();
            // $jmlsiswa = Siswa::where('is_lulus', 0)->count();
            // $jmlsiswalaki = Siswa::where('gender_id', 1)->where('is_lulus', 0)->count();
            // $jmlsiswacewe = Siswa::where('gender_id', 2)->where('is_lulus', 0)->count();
            // $jmlsiswalaki1 = Siswa::where('unit_id', 1)->where('gender_id', 1)->where('is_lulus', 0)->count();
            // $jmlsiswacewe1 = Siswa::where('unit_id', 1)->where('gender_id', 2)->where('is_lulus', 0)->count();
            // $jmlsiswalaki2 = Siswa::where('unit_id', 2)->where('gender_id', 1)->where('is_lulus', 0)->count();
            // $jmlsiswacewe2 = Siswa::where('unit_id', 2)->where('gender_id', 2)->where('is_lulus', 0)->count();
            // $jmlsiswalaki3 = Siswa::where('unit_id', 3)->where('gender_id', 1)->where('is_lulus', 0)->count();
            // $jmlsiswacewe3 = Siswa::where('unit_id', 3)->where('gender_id', 2)->where('is_lulus', 0)->count();
            // $jmlsiswalaki4 = Siswa::where('unit_id', 4)->where('gender_id', 1)->where('is_lulus', 0)->count();
            // $jmlsiswacewe4 = Siswa::where('unit_id', 4)->where('gender_id', 2)->where('is_lulus', 0)->count();
        } else {
            $jmlsiswa = Siswa::whereHas('identitas', function($q) use ($unit) {
                $q->where('unit_id', $unit)->where('is_lulus', 0);
            })->count();
            // $jmlsiswa = Siswa::where('unit_id', $unit)->where('is_lulus', 0)->count();
            // $jmlsiswalaki = Siswa::where('unit_id', $unit)->where('gender_id', 1)->where('is_lulus', 0)->count();
            // $jmlsiswacewe = Siswa::where('unit_id', $unit)->where('gender_id', 2)->where('is_lulus', 0)->count();
            $jmlsiswalaki = Siswa::whereHas('identitas', function($q) use ($unit){
                $q->where('gender_id', 1)->where('unit_id', $unit)->where('gender_id', 1)->where('is_lulus', 0);
            })->count();
            $jmlsiswacewe = Siswa::whereHas('identitas', function($q) use ($unit){
                $q->where('gender_id', 2)->where('unit_id', $unit)->where('gender_id', 2)->where('is_lulus', 0);
            })->count();
            $units = Unit::find($unit);
            $namaunit = $units->name;
        }

        // jml mapel
        // if ($unit == 5) {
        //     $jmlmapel = MataPelajaran::count();
        //     $jmlmapel1 = MataPelajaran::where('unit_id', 1)->count();
        //     $jmlmapel2 = MataPelajaran::where('unit_id', 2)->count();
        //     $jmlmapel3 = MataPelajaran::where('unit_id', 3)->count();
        //     $jmlmapel4 = MataPelajaran::where('unit_id', 4)->count();
        // } else {
        //     $jmlmapel = MataPelajaran::where('unit_id', $unit)->count();
        // }

        $tahun = TahunAjaran::where('is_active', 1)->first();

        // jml kelas
        if ($unit == 5) {
            // $jmlkelas = Kelas::where('academic_year_id', $tahun->id)->count();
            // $jmlkelas1 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 1)->count();
            // $jmlkelas2 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 2)->count();
            // $jmlkelas3 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 3)->count();
            // $jmlkelas4 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 4)->count();
            return view('kependidikan', compact(
                'jmlsiswa',
                // 'jmlmapel',
                // 'jmlmapel1',
                // 'jmlmapel2',
                // 'jmlmapel3',
                // 'jmlmapel4',
                // 'jmlkelas',
                // 'jmlkelas1',
                // 'jmlkelas2',
                // 'jmlkelas3',
                // 'jmlkelas4',
                'namaunit',
                'tahun',
                'jmlsiswalaki',
                'jmlsiswacewe',
                'jmlsiswalaki1',
                'jmlsiswacewe1',
                'jmlsiswalaki2',
                'jmlsiswacewe2',
                'jmlsiswalaki3',
                'jmlsiswacewe3',
                'jmlsiswalaki4',
                'jmlsiswacewe4',
                'unit',
                'datas',
                'request',
            ));
        } else {
            // $jmlkelas = Kelas::where('unit_id', $unit)->where('academic_year_id', $tahun->id)->count();
            return view('kependidikan', compact('jmlsiswa', 'namaunit', 'tahun', 'jmlsiswalaki', 'jmlsiswacewe', 'unit', 'datas', 'request'));
        }
    }

    public function penilaianmapel()
    {
        $sidebarmapel = TRUE;
        $unit = Auth::user()->pegawai->unit_id;

        // jml siswa
        if ($unit == 5) {
            $namaunit = "TK, SD, SMP, SMA";
            $jmlsiswa = Siswa::where('is_lulus', 0)->count();
            $jmlsiswalaki = Siswa::whereHas('identitas',function($q){$q->where('gender_id', 1);})->where('is_lulus', 0)->count();
            $jmlsiswacewe = Siswa::whereHas('identitas',function($q){$q->where('gender_id', 2);})->where('is_lulus', 0)->count();
            $jmlsiswalaki1 = Siswa::where('unit_id', 1)->whereHas('identitas',function($q){$q->where('gender_id', 1);})->where('is_lulus', 0)->count();
            $jmlsiswacewe1 = Siswa::where('unit_id', 1)->whereHas('identitas',function($q){$q->where('gender_id', 2);})->where('is_lulus', 0)->count();
            $jmlsiswalaki2 = Siswa::where('unit_id', 2)->whereHas('identitas',function($q){$q->where('gender_id', 1);})->where('is_lulus', 0)->count();
            $jmlsiswacewe2 = Siswa::where('unit_id', 2)->whereHas('identitas',function($q){$q->where('gender_id', 2);})->where('is_lulus', 0)->count();
            $jmlsiswalaki3 = Siswa::where('unit_id', 3)->whereHas('identitas',function($q){$q->where('gender_id', 1);})->where('is_lulus', 0)->count();
            $jmlsiswacewe3 = Siswa::where('unit_id', 3)->whereHas('identitas',function($q){$q->where('gender_id', 2);})->where('is_lulus', 0)->count();
            $jmlsiswalaki4 = Siswa::where('unit_id', 4)->whereHas('identitas',function($q){$q->where('gender_id', 1);})->where('is_lulus', 0)->count();
            $jmlsiswacewe4 = Siswa::where('unit_id', 4)->whereHas('identitas',function($q){$q->where('gender_id', 2);})->where('is_lulus', 0)->count();
        } else {
            $jmlsiswa = Siswa::where('unit_id', $unit)->where('is_lulus', 0)->count();
            $jmlsiswalaki = Siswa::where('unit_id', $unit)->whereHas('identitas',function($q){$q->where('gender_id', 1);})->where('is_lulus', 0)->count();
            $jmlsiswacewe = Siswa::where('unit_id', $unit)->whereHas('identitas',function($q){$q->where('gender_id', 2);})->where('is_lulus', 0)->count();
            $units = Unit::find($unit);
            $namaunit = $units->name;
        }

        // jml mapel
        if ($unit == 5) {
            $jmlmapel = MataPelajaran::count();
            $jmlmapel1 = MataPelajaran::where('unit_id', 1)->count();
            $jmlmapel2 = MataPelajaran::where('unit_id', 2)->count();
            $jmlmapel3 = MataPelajaran::where('unit_id', 3)->count();
            $jmlmapel4 = MataPelajaran::where('unit_id', 4)->count();
        } else {
            $jmlmapel = MataPelajaran::where('unit_id', $unit)->count();
        }

        $tahun = TahunAjaran::where('is_active', 1)->first();

        // jml kelas
        if ($unit == 5) {
            $jmlkelas = Kelas::where('academic_year_id', $tahun->id)->count();
            $jmlkelas1 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 1)->count();
            $jmlkelas2 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 2)->count();
            $jmlkelas3 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 3)->count();
            $jmlkelas4 = Kelas::where('academic_year_id', $tahun->id)->where('unit_id', 4)->count();
            return view('kependidikan', compact(
                'jmlsiswa',
                'jmlmapel',
                'jmlmapel1',
                'jmlmapel2',
                'jmlmapel3',
                'jmlmapel4',
                'jmlkelas',
                'jmlkelas1',
                'jmlkelas2',
                'jmlkelas3',
                'jmlkelas4',
                'namaunit',
                'tahun',
                'jmlsiswalaki',
                'jmlsiswacewe',
                'jmlsiswalaki1',
                'jmlsiswacewe1',
                'jmlsiswalaki2',
                'jmlsiswacewe2',
                'jmlsiswalaki3',
                'jmlsiswacewe3',
                'jmlsiswalaki4',
                'jmlsiswacewe4',
                'unit'
            ));
        } else {
            $jmlkelas = Kelas::where('unit_id', $unit)->where('academic_year_id', $tahun->id)->count();
            return view('kependidikan', compact('jmlsiswa', 'jmlmapel', 'jmlkelas', 'namaunit', 'tahun', 'jmlsiswalaki', 'jmlsiswacewe', 'unit', 'sidebarmapel'));
        }
    }
}

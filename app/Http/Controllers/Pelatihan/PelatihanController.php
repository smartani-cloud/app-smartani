<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Pelatihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pelatihan\Pelatihan;

class PelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $pegawai = $request->user()->pegawai;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        $pelatihan = $aktif->pelatihan()->aktif()->whereHas('sasaran',function($query)use($pegawai){
            $query->whereHas('jabatan',function($query)use($pegawai){
                $query->where([
                    'unit_id' => $pegawai->unit_id,
                    'position_id' => $pegawai->position_id
                ]);
            });
        })->whereNotNull([
            'education_acc_id',
            'education_acc_time'
        ])->where('education_acc_status_id',1)->orderBy('date','desc')->get();
        
        return view('kepegawaian.read-only.pelatihan_saya_index', compact('aktif','pelatihan'));
    }

    public function history(Request $request)
    {
        $role = $request->user()->role->name;
        $pegawai = $request->user()->pegawai;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        if($request->tahunajaran && $request->tahunajaran != 'semua'){
            $tahunajaran = str_replace("-","/",$request->tahunajaran);
            if($tahunajaran != $aktif->academic_year){
                $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
            }

            if(!$aktif) return redirect()->route('pelatihan.saya.index');
        }
        else $request->tahunajaran = 'semua';

        if($request->tahunajaran == 'semua'){
            $aktif = 'semua';
            $pelatihan = Pelatihan::selesai();
        }
        else
            $pelatihan = $aktif->pelatihan()->selesai();

        $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($pegawai){
            $query->whereHas('jabatan',function($query)use($pegawai){
                $query->where([
                    'unit_id' => $pegawai->unit_id,
                    'position_id' => $pegawai->position_id
                ]);
            });
        })->whereHas('presensi',function($query)use($pegawai){
            $query->where([
                'employee_id' => $pegawai->id
            ]);
        })->whereNotNull([
            'education_acc_id',
            'education_acc_time'
        ])->where('education_acc_status_id',1)->orderBy('date','desc')->get();

        $tahun_tersedia = $pelatihan->pluck('academic_year_id')->unique()->all();

        $tahun = TahunAjaran::whereIn('id',$tahun_tersedia)->orderBy('academic_year')->get();
        
        return view('kepegawaian.read-only.pelatihan_saya_riwayat', compact('aktif','tahun','pelatihan'));
    }
}
=======
<?php

namespace App\Http\Controllers\Pelatihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pelatihan\Pelatihan;

class PelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $pegawai = $request->user()->pegawai;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        $pelatihan = $aktif->pelatihan()->aktif()->whereHas('sasaran',function($query)use($pegawai){
            $query->whereHas('jabatan',function($query)use($pegawai){
                $query->where([
                    'unit_id' => $pegawai->unit_id,
                    'position_id' => $pegawai->position_id
                ]);
            });
        })->whereNotNull([
            'education_acc_id',
            'education_acc_time'
        ])->where('education_acc_status_id',1)->orderBy('date','desc')->get();
        
        return view('kepegawaian.read-only.pelatihan_saya_index', compact('aktif','pelatihan'));
    }

    public function history(Request $request)
    {
        $role = $request->user()->role->name;
        $pegawai = $request->user()->pegawai;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        if($request->tahunajaran && $request->tahunajaran != 'semua'){
            $tahunajaran = str_replace("-","/",$request->tahunajaran);
            if($tahunajaran != $aktif->academic_year){
                $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
            }

            if(!$aktif) return redirect()->route('pelatihan.saya.index');
        }
        else $request->tahunajaran = 'semua';

        if($request->tahunajaran == 'semua'){
            $aktif = 'semua';
            $pelatihan = Pelatihan::selesai();
        }
        else
            $pelatihan = $aktif->pelatihan()->selesai();

        $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($pegawai){
            $query->whereHas('jabatan',function($query)use($pegawai){
                $query->where([
                    'unit_id' => $pegawai->unit_id,
                    'position_id' => $pegawai->position_id
                ]);
            });
        })->whereHas('presensi',function($query)use($pegawai){
            $query->where([
                'employee_id' => $pegawai->id
            ]);
        })->whereNotNull([
            'education_acc_id',
            'education_acc_time'
        ])->where('education_acc_status_id',1)->orderBy('date','desc')->get();

        $tahun_tersedia = $pelatihan->pluck('academic_year_id')->unique()->all();

        $tahun = TahunAjaran::whereIn('id',$tahun_tersedia)->orderBy('academic_year')->get();
        
        return view('kepegawaian.read-only.pelatihan_saya_riwayat', compact('aktif','tahun','pelatihan'));
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

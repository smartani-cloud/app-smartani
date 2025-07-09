<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use App\Models\Kbm\HistoryKelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;

class PengajuanKelasController extends Controller
{
    public function index()
    {
        // check unit_id user
        // $unit = 4;
        $unit = Auth::user()->pegawai->unit_id;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // status kelas = mengajukan
        $status = 2;

        if($unit == 5){
            $kelases = Kelas::where('status',$status)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $setuju = Kelas::where('status',3)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $tunggu = Kelas::whereIn('status',[1,4])
            ->where('academic_year_id',$tahunsekarang->id)
            ->orderBy('level_id','asc')
            ->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('status',$status)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $setuju = Kelas::where('unit_id',$unit)->where('status',3)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $tunggu = Kelas::where('unit_id',$unit)
            ->whereIn('status',[1,4])
            ->where('academic_year_id',$tahunsekarang->id)
            ->orderBy('level_id','asc')
            ->get();
        }

        return view('kbm.pengajuan.index',compact('kelases','setuju','tunggu'));
    }

    public function lihat($id)
    {

        // check class
        $kelas = Kelas::where('id',$id)->first();

        // search student class
        $siswas = Siswa::where('class_id',$kelas->id)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');
        // search student class
        $siswakosong = Siswa::whereNull('class_id')->where('is_lulus',0)->where('level_id',$kelas->level_id)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');

        return view('kbm.pengajuan.lihat', compact('siswas','kelas','siswakosong'));
    }

    public function setuju($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 3;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas')->with('sukses','Setujui Kelas Berhasil');
    }

    public function tolak($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 4;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas')->with('sukses','Tolak Kelas Berhasil');
    }

    public function tambah(Request $request, $id)
    {
        // $siswa = Siswa::find($request->siswa);
        // $siswa->class_id = $id;
        // $siswa->save();

        $kelas = Kelas::find($id);
        $siswa = Siswa::find($request->siswa);
        if($siswa && $kelas){
            if($kelas->level_id == $siswa->level_id){
                
                $siswa->class_id = $id;
                $siswa->save();

                // check tahun akademik yg sedang aktif
                $semester = Semester::where('is_active',1)->first();
                
                if($semester->semester == 'Ganjil'){
                    $history = HistoryKelas::create([
                        'class_id' => $id,
                        'student_id' => $siswa->id,
                        'semester_id' => $semester->id,
                        'level_id' => $siswa->level_id,
                        'unit_id' => $siswa->unit_id,
                    ]);
                    $history = HistoryKelas::create([
                        'class_id' => $id,
                        'student_id' => $siswa->id,
                        'semester_id' => $semester->id + 1,
                        'level_id' => $siswa->level_id,
                        'unit_id' => $siswa->unit_id,
                    ]);
                }else{
                    $history = HistoryKelas::create([
                        'class_id' => $id,
                        'student_id' => $siswa->id,
                        'semester_id' => $semester->id,
                        'level_id' => $siswa->level_id,
                        'unit_id' => $siswa->unit_id,
                    ]);
                }
            }else{
                // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
                return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$id)->with('gagal','Tambah Siswa Gagal');
            }
        }else{
            // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
            return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$id)->with('gagal','Tambah Siswa Gagal');
        }
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$id)->with('sukses','Tambah Siswa Berhasil');
    }

    public function hapus($kelas, $id)
    {
        $siswa = Siswa::find($id);
        // $siswa->class_id = null;
        // $siswa->save();
        if($siswa){
            // $history = HistoryKelas::where('siswa_id',$siswa->id)->where('class_id',$siswa->class_id)->first();
            // if($history){
            //     $history->delete();
            // }

            // check tahun akademik yg sedang aktif
            $semester = Semester::where('is_active',1)->first();

            if($semester->semester == 'Ganjil'){
                $history1 = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id)->first();
                if($history1){
                    $history1->delete();
                }
                $history2 = HistoryKelas::where('student_id',$siswa->id)->where('class_id',$semester->id + 1)->first();
                if($history2){
                    $history2->delete();
                }
            }else{
                $history = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id)->first();
                if($history){
                    $history->delete();
                }
            }
            $siswa->class_id = null;
            $siswa->save();
            // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Hapus Siswa Berhasil');
            return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$kelas)->with('gagal','Hapus Siswa Gagal');
        }else{
            // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Hapus Siswa Gagal');
            return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$kelas)->with('gagal','Hapus Siswa Gagal');
        }
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$kelas)->with('sukses','Hapus Siswa Berhasil');
    }

    public function cetakPDF($id)
    {

        // teacher = employee_id
        $teacher = $id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        // check class
        $kelas = Kelas::where('id',$id)->first();

        // search student class
        $siswas = Siswa::where('class_id',$kelas->id)->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name');

        $pdf = PDF::loadView('kbm.ampu.pdf',compact('siswas','kelas'));
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Daftar Siswa Kelas '.$kelas->level->level.' '.$kelas->namakelases->class_name.'.pdf');
        
        // return view('kbm.ampu.index', compact('siswas','kelas'));
    }
}
=======
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use App\Models\Kbm\HistoryKelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;

class PengajuanKelasController extends Controller
{
    public function index()
    {
        // check unit_id user
        // $unit = 4;
        $unit = Auth::user()->pegawai->unit_id;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // status kelas = mengajukan
        $status = 2;

        if($unit == 5){
            $kelases = Kelas::where('status',$status)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $setuju = Kelas::where('status',3)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $tunggu = Kelas::whereIn('status',[1,4])
            ->where('academic_year_id',$tahunsekarang->id)
            ->orderBy('level_id','asc')
            ->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('status',$status)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $setuju = Kelas::where('unit_id',$unit)->where('status',3)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
    
            $tunggu = Kelas::where('unit_id',$unit)
            ->whereIn('status',[1,4])
            ->where('academic_year_id',$tahunsekarang->id)
            ->orderBy('level_id','asc')
            ->get();
        }

        return view('kbm.pengajuan.index',compact('kelases','setuju','tunggu'));
    }

    public function lihat($id)
    {

        // check class
        $kelas = Kelas::where('id',$id)->first();

        // search student class
        $siswas = Siswa::where('class_id',$kelas->id)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');
        // search student class
        $siswakosong = Siswa::whereNull('class_id')->where('is_lulus',0)->where('level_id',$kelas->level_id)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');

        return view('kbm.pengajuan.lihat', compact('siswas','kelas','siswakosong'));
    }

    public function setuju($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 3;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas')->with('sukses','Setujui Kelas Berhasil');
    }

    public function tolak($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 4;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas')->with('sukses','Tolak Kelas Berhasil');
    }

    public function tambah(Request $request, $id)
    {
        // $siswa = Siswa::find($request->siswa);
        // $siswa->class_id = $id;
        // $siswa->save();

        $kelas = Kelas::find($id);
        $siswa = Siswa::find($request->siswa);
        if($siswa && $kelas){
            if($kelas->level_id == $siswa->level_id){
                
                $siswa->class_id = $id;
                $siswa->save();

                // check tahun akademik yg sedang aktif
                $semester = Semester::where('is_active',1)->first();
                
                if($semester->semester == 'Ganjil'){
                    $history = HistoryKelas::create([
                        'class_id' => $id,
                        'student_id' => $siswa->id,
                        'semester_id' => $semester->id,
                        'level_id' => $siswa->level_id,
                        'unit_id' => $siswa->unit_id,
                    ]);
                    $history = HistoryKelas::create([
                        'class_id' => $id,
                        'student_id' => $siswa->id,
                        'semester_id' => $semester->id + 1,
                        'level_id' => $siswa->level_id,
                        'unit_id' => $siswa->unit_id,
                    ]);
                }else{
                    $history = HistoryKelas::create([
                        'class_id' => $id,
                        'student_id' => $siswa->id,
                        'semester_id' => $semester->id,
                        'level_id' => $siswa->level_id,
                        'unit_id' => $siswa->unit_id,
                    ]);
                }
            }else{
                // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
                return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$id)->with('gagal','Tambah Siswa Gagal');
            }
        }else{
            // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
            return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$id)->with('gagal','Tambah Siswa Gagal');
        }
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$id)->with('sukses','Tambah Siswa Berhasil');
    }

    public function hapus($kelas, $id)
    {
        $siswa = Siswa::find($id);
        // $siswa->class_id = null;
        // $siswa->save();
        if($siswa){
            // $history = HistoryKelas::where('siswa_id',$siswa->id)->where('class_id',$siswa->class_id)->first();
            // if($history){
            //     $history->delete();
            // }

            // check tahun akademik yg sedang aktif
            $semester = Semester::where('is_active',1)->first();

            if($semester->semester == 'Ganjil'){
                $history1 = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id)->first();
                if($history1){
                    $history1->delete();
                }
                $history2 = HistoryKelas::where('student_id',$siswa->id)->where('class_id',$semester->id + 1)->first();
                if($history2){
                    $history2->delete();
                }
            }else{
                $history = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id)->first();
                if($history){
                    $history->delete();
                }
            }
            $siswa->class_id = null;
            $siswa->save();
            // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Hapus Siswa Berhasil');
            return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$kelas)->with('gagal','Hapus Siswa Gagal');
        }else{
            // return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Hapus Siswa Gagal');
            return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$kelas)->with('gagal','Hapus Siswa Gagal');
        }
        return redirect('/kependidikan/kbm/kelas/pengajuan-kelas/lihat/'.$kelas)->with('sukses','Hapus Siswa Berhasil');
    }

    public function cetakPDF($id)
    {

        // teacher = employee_id
        $teacher = $id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        // check class
        $kelas = Kelas::where('id',$id)->first();

        // search student class
        $siswas = Siswa::where('class_id',$kelas->id)->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name');

        $pdf = PDF::loadView('kbm.ampu.pdf',compact('siswas','kelas'));
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Daftar Siswa Kelas '.$kelas->level->level.' '.$kelas->namakelases->class_name.'.pdf');
        
        // return view('kbm.ampu.index', compact('siswas','kelas'));
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

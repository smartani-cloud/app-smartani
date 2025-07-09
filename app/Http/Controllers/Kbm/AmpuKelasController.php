<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use App\Models\Kbm\HistoryKelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
// use PDF;
use Barryvdh\DomPDF\Facade as PDF;

class AmpuKelasController extends Controller
{
    public function index()
    {
        // teacher = employee_id
        $teacher = Auth::user()->user_id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // check class
        $kelas = Kelas::where('teacher_id',$teacher)->where('academic_year_id',$tahunsekarang->id)->first();

        if($kelas == null){
            $siswas = null;
            $siswakosong = null;
        }else{
            // search student class
            $siswas = Siswa::where('class_id',$kelas->id)->where('is_lulus',0)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');
            // search student class
            $siswakosong = Siswa::whereNull('class_id')->where('is_lulus',0)->where('level_id',$kelas->level_id)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');
        }

        return view('kbm.ampu.index', compact('siswas','kelas','siswakosong'));
    }

    public function ajukan($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 2;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Ajukan Kelas Berhasil');
    }

    public function hapus($id)
    {
        $siswa = Siswa::find($id);
        if($siswa){

            // check tahun akademik yg sedang aktif
            $semester = Semester::where('is_active',1)->first();

            if($semester->semester == 'Ganjil'){
                $history1 = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id)->first();
                if($history1){
                    $history1->delete();
                }
                $history2 = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id + 1)->first();
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
            return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Hapus Siswa Berhasil');
        }else{
            return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Hapus Siswa Gagal');
        }
    }

    public function tambah(Request $request, $id)
    {
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
                return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
            }
        }else{
            return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
        }
        return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Tambah Siswa Berhasil');
    }

    public function cetakPDF()
    {

        // teacher = employee_id
        $teacher = Auth::user()->user_id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // check class
        $kelas = Kelas::where('teacher_id',$teacher)->where('academic_year_id',$tahunsekarang->id)->first();

        // check siswa
        $siswas = Siswa::where('class_id',$kelas->id)->where('is_lulus',0)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');

        $pdf = PDF::loadView('kbm.ampu.pdf',compact('siswas','kelas','tahunsekarang'));
        
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

use App\Models\Kbm\TahunAjaran;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
// use PDF;
use Barryvdh\DomPDF\Facade as PDF;

class AmpuKelasController extends Controller
{
    public function index()
    {
        // teacher = employee_id
        $teacher = Auth::user()->user_id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // check class
        $kelas = Kelas::where('teacher_id',$teacher)->where('academic_year_id',$tahunsekarang->id)->first();

        if($kelas == null){
            $siswas = null;
            $siswakosong = null;
        }else{
            // search student class
            $siswas = Siswa::where('class_id',$kelas->id)->where('is_lulus',0)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');
            // search student class
            $siswakosong = Siswa::whereNull('class_id')->where('is_lulus',0)->where('level_id',$kelas->level_id)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');
        }

        return view('kbm.ampu.index', compact('siswas','kelas','siswakosong'));
    }

    public function ajukan($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 2;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Ajukan Kelas Berhasil');
    }

    public function hapus($id)
    {
        $siswa = Siswa::find($id);
        if($siswa){

            // check tahun akademik yg sedang aktif
            $semester = Semester::where('is_active',1)->first();

            if($semester->semester == 'Ganjil'){
                $history1 = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id)->first();
                if($history1){
                    $history1->delete();
                }
                $history2 = HistoryKelas::where('student_id',$siswa->id)->where('semester_id',$semester->id + 1)->first();
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
            return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Hapus Siswa Berhasil');
        }else{
            return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Hapus Siswa Gagal');
        }
    }

    public function tambah(Request $request, $id)
    {
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
                return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
            }
        }else{
            return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('gagal_tambah','Tambah Siswa Gagal');
        }
        return redirect('/kependidikan/kbm/kelas/kelas-diampu')->with('sukses','Tambah Siswa Berhasil');
    }

    public function cetakPDF()
    {

        // teacher = employee_id
        $teacher = Auth::user()->user_id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // check class
        $kelas = Kelas::where('teacher_id',$teacher)->where('academic_year_id',$tahunsekarang->id)->first();

        // check siswa
        $siswas = Siswa::where('class_id',$kelas->id)->where('is_lulus',0)->with('identitas:id,student_name,birth_place,birth_date,gender_id')-> get()->sortBy('identitas.student_name');

        $pdf = PDF::loadView('kbm.ampu.pdf',compact('siswas','kelas','tahunsekarang'));
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Daftar Siswa Kelas '.$kelas->level->level.' '.$kelas->namakelases->class_name.'.pdf');
        
        // return view('kbm.ampu.index', compact('siswas','kelas'));
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

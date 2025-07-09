<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Rekrutmen\Pegawai;
use App\Models\Kbm\TahunAjaran;
use App\Models\Kbm\NamaKelas;
use App\Models\Kbm\Kelas;
use App\Models\Jurusan;
use App\Models\Level;

class KelasController extends Controller
{
    public function index()
    {
        // check unit_id user
        $unit = auth()->user()->pegawai->unit_id;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->orderBy('major_id','asc')->get();
        }
        return view('kbm.kelas.index',compact('kelases'));
    }

    public function create()
    {
        //check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        $levels = Level::where('unit_id',$unit)->orderBy('level','asc')->get();
        $namakelases = NamaKelas::orderBy('class_name','asc')->where('unit_id',$unit)->get();
        // cek daftar guru
        $gurus = Pegawai::where('unit_id',$unit)->whereIn('position_id',[3,4,5,6,7])->where('active_status_id',1)->get();

        $jurusans = Jurusan::all();

        return view('kbm.kelas.tambah',compact('levels','namakelases','gurus','jurusans'));
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'kelas' => 'required',
            'nama_kelas' => 'required',
            'wali_kelas' => 'required',
        ]);
            
        //check unit_id user
        $unit = Auth::user()->pegawai->unit_id;
        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        // initiate status dari 1
        $status = 1;
        
        // create to table
        Kelas::create([
            'level_id' => $request->kelas,
            'class_name_id' => $request->nama_kelas,
            'unit_id' => $unit,
            'teacher_id' => $request->wali_kelas,
            'academic_year_id' => $tahunsekarang->id,
            'status' => '1',
            'major_id' => $request->jurusan,
        ]);

        // return with create success notification
        return redirect('/kependidikan/kbm/kelas/daftar-kelas')->with('sukses','Tambah Kelas Berhasil');
    }

    public function edit($id)
    {
        //check unit_id user
        $unit = Auth::user()->pegawai->unit_id;
        $levels = Level::where('unit_id',$unit)->orderBy('level','asc')->get();
        $namakelases = NamaKelas::orderBy('class_name','asc')->where('unit_id',$unit)->get();
        $kelas = Kelas::find($id);

        $jurusans = Jurusan::all();
        // cek daftar guru
        $gurus = Pegawai::where('unit_id',$unit)->whereIn('position_id',[3,4,5,6,7])->where('active_status_id',1)->get();
        return view('kbm.kelas.ubah',compact('levels','namakelases','kelas','gurus','jurusans'));
    }

    public function update(Request $request, $id)
    {
        // Validate
        $request->validate([
            'kelas' => 'required',
            'nama_kelas' => 'required',
            'wali_kelas' => 'required',
        ]);

        // update kelas
    	$kelas = Kelas::find($id);
        $kelas->level_id = $request->kelas;
        $kelas->class_name_id = $request->nama_kelas;
        $kelas->teacher_id = $request->wali_kelas;
        $kelas->major_id = $request->jurusan;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/daftar-kelas')->with('sukses','Ubah Kelas Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    	$kelas = Kelas::find($id);
        $kelas->delete();
        
        return redirect('/kependidikan/kbm/kelas/daftar-kelas')->with('sukses','Hapus Kelas Berhasil');
    }
}
=======
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Rekrutmen\Pegawai;
use App\Models\Kbm\TahunAjaran;
use App\Models\Kbm\NamaKelas;
use App\Models\Kbm\Kelas;
use App\Models\Jurusan;
use App\Models\Level;

class KelasController extends Controller
{
    public function index()
    {
        // check unit_id user
        $unit = auth()->user()->pegawai->unit_id;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->orderBy('major_id','asc')->get();
        }
        return view('kbm.kelas.index',compact('kelases'));
    }

    public function create()
    {
        //check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        $levels = Level::where('unit_id',$unit)->orderBy('level','asc')->get();
        $namakelases = NamaKelas::orderBy('class_name','asc')->where('unit_id',$unit)->get();
        // cek daftar guru
        $gurus = Pegawai::where('unit_id',$unit)->whereIn('position_id',[3,4,5,6,7])->where('active_status_id',1)->get();

        $jurusans = Jurusan::all();

        return view('kbm.kelas.tambah',compact('levels','namakelases','gurus','jurusans'));
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'kelas' => 'required',
            'nama_kelas' => 'required',
            'wali_kelas' => 'required',
        ]);
            
        //check unit_id user
        $unit = Auth::user()->pegawai->unit_id;
        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();
        // initiate status dari 1
        $status = 1;
        
        // create to table
        Kelas::create([
            'level_id' => $request->kelas,
            'class_name_id' => $request->nama_kelas,
            'unit_id' => $unit,
            'teacher_id' => $request->wali_kelas,
            'academic_year_id' => $tahunsekarang->id,
            'status' => '1',
            'major_id' => $request->jurusan,
        ]);

        // return with create success notification
        return redirect('/kependidikan/kbm/kelas/daftar-kelas')->with('sukses','Tambah Kelas Berhasil');
    }

    public function edit($id)
    {
        //check unit_id user
        $unit = Auth::user()->pegawai->unit_id;
        $levels = Level::where('unit_id',$unit)->orderBy('level','asc')->get();
        $namakelases = NamaKelas::orderBy('class_name','asc')->where('unit_id',$unit)->get();
        $kelas = Kelas::find($id);

        $jurusans = Jurusan::all();
        // cek daftar guru
        $gurus = Pegawai::where('unit_id',$unit)->whereIn('position_id',[3,4,5,6,7])->where('active_status_id',1)->get();
        return view('kbm.kelas.ubah',compact('levels','namakelases','kelas','gurus','jurusans'));
    }

    public function update(Request $request, $id)
    {
        // Validate
        $request->validate([
            'kelas' => 'required',
            'nama_kelas' => 'required',
            'wali_kelas' => 'required',
        ]);

        // update kelas
    	$kelas = Kelas::find($id);
        $kelas->level_id = $request->kelas;
        $kelas->class_name_id = $request->nama_kelas;
        $kelas->teacher_id = $request->wali_kelas;
        $kelas->major_id = $request->jurusan;
        $kelas->save();
        return redirect('/kependidikan/kbm/kelas/daftar-kelas')->with('sukses','Ubah Kelas Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    	$kelas = Kelas::find($id);
        $kelas->delete();
        
        return redirect('/kependidikan/kbm/kelas/daftar-kelas')->with('sukses','Hapus Kelas Berhasil');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

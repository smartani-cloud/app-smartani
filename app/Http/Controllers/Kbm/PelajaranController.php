<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Session;

Use App\Models\Skbm\Skbm;
use App\Models\Kbm\KelompokMataPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\KkmPelajaran;
use App\Models\Kbm\MapelKelas;
use App\Models\Kbm\Semester;
use App\Models\Jurusan;
use App\Models\Level;
use App\Models\Rekrutmen\Pegawai;

class PelajaranController extends Controller
{
    public function index()
    {
        $unit = Auth::user()->pegawai->unit_id;
        if(Auth::user()->role_id == 5){
            $employee = Auth::user()->user_id;
            $skbm = Skbm::aktif()->where('unit_id',$unit)->first();
            // dd(Auth::user()->pegawai());
            $mapellist = $skbm->detail->where('employee_id', $employee);
            // dd($mapellist);
            // if()
            
        }else{
            // memanggil semua mapel
            if($unit == 5){
                $mapellist = MataPelajaran::all();
            }else{
                $mapellist = MataPelajaran::where('unit_id',$unit)->get();
            }
        }

        // inisialisasi
        $smsaktif = Semester::where('is_active',1)->first();
        $kkm = [];
        $used = null;
        foreach($mapellist as $index => $mapel){
            if(Auth::user()->role_id == 5){
                $checkkkm = KkmPelajaran::where('subject_id',$mapel->subject_id)->where('semester_id',$smsaktif->id)->first();
            }
            else{
                $checkkkm = KkmPelajaran::where('subject_id',$mapel->id)->where('semester_id',$smsaktif->id)->first();   
            }
            if($checkkkm){
                $kkm[$index] = $checkkkm->kkm;
            }else{
                $kkm[$index] = 'Belum diatur';
            }
            
            if($mapel->jadwalPelajaran()->count() > 0 || $mapel->skbmDetail()->count() > 0) $used[$mapel->id] = 1;
            else $used[$mapel->id] = 0;
        }
        // dd($mapellist[0]);

        return view('kbm.matapelajaran.index',compact('mapellist','unit','kkm','smsaktif','used'));
    }

    public function create()
    {
        $unit = Auth::user()->pegawai->unit_id;
        // menampilkan form tambah
        $kmplists = kelompokMataPelajaran::where('unit_id',$unit)->get();
        $levels = Level::where('unit_id',$unit)->get();
        return view('kbm.matapelajaran.tambah',compact('kmplists','unit','levels'));
    }

    public function store(Request $request)
    {
        // dd($request);

        $unit = Auth::user()->pegawai->unit_id;

        $messages = [
            'nama_mapel.required' => 'Mohon tuliskan nama mata pelajaran',
            'kode_mapel.required' => 'Mohon tuliskan kode mata pelajaran',
            'nomor_mapel.required' => 'Mohon tuliskan nomor urut mata pelajaran',
            'nomor_mapel.numeric' => 'Pastikan nomor urut mata pelajaran hanya mengandung angka',
            'nomor_mapel.min' => 'Pastikan nomor urut mata pelajaran minimal 1',
            'kmp_id.required' => 'Mohon pilih salah satu kelompok mata pelajaran',
            'kkm.required' => 'Mohon tentukan KKM mata pelajaran ini',
            'kkm.numeric' => 'Pastikan KKM hanya mengandung angka',
            'kkm.min' => 'Pastikan KKM dalam rentang 51-100',
            'kkm.max' => 'Pastikan KKM dalam rentang 51-100',
        ];

        // validate dari form tambah
        if($unit == 1){
            $request->validate([
                'nama_mapel' => 'required',
                'kmp_id' => 'required',
            ], $messages);
            // create to table
            MataPelajaran::create([
                'subject_name' => $request->nama_mapel,
                'group_subject_id' => $request->kmp_id,
                'unit_id' => $unit,
            ]);
        }else{
            $request->validate([
                'nama_mapel' => 'required',
                'kode_mapel' => 'required',
                'nomor_mapel' => 'required|numeric|min:1',
                'kmp_id' => 'required',
                'kkm' => 'required|numeric|min:51|max:100',
            ], $messages);

            if($request->kkm <= 50){
                return redirect()->back()->with('error', 'KKM Harus Lebih Dari 50');
            }

            // create to table
            $mapel = MataPelajaran::create([
                'subject_number' => $request->nomor_mapel,
                'subject_name' => $request->nama_mapel,
                'subject_acronym' => $request->kode_mapel,
                'group_subject_id' => $request->kmp_id,
                'kkm' => $request->kkm,
                'unit_id' => $unit,
                'is_mulok' => $request->mulok == 1 ? $request->mulok : null
            ]);

            // check semester yg sedang aktif
            $smsaktif = Semester::where('is_active',1)->first();

            // create to kkm table
            $kkm = KkmPelajaran::create([
                'subject_id' => $mapel->id,
                'kkm' => $mapel->kkm,
                'semester_id' => $smsaktif->id,
            ]);
            if($unit == 2){
                $kelases = $request->input('kelas');
                foreach($kelases as $kelas){
                    MapelKelas::create([
                        'level_id' => $kelas,
                        'subject_id' => $mapel->id
                    ]);
                }
            }
        }


        return redirect('/kependidikan/kbm/pelajaran/mata-pelajaran')->with('success','Tambah Mata Pelajaran Berhasil');
    }

    public function edit($id)
    {
        $unit = Auth::user()->pegawai->unit_id;
        // menampilkan form terisi data yang akan diubah
        $kmplists = kelompokMataPelajaran::where('unit_id',$unit)->get();
        $unit = Auth::user()->pegawai->unit_id;
        $mapel = MataPelajaran::find($id);
        $levels = Level::where('unit_id',$unit)->get();
        $mapellevels = MapelKelas::where('subject_id',$id)->pluck('level_id');

        // check semester yg sedang aktif
        $smsaktif = Semester::where('is_active',1)->first();

        // KKM
        $checkkkm = KkmPelajaran::where('subject_id',$id)->where('semester_id',$smsaktif->id)->first();
        if($checkkkm){
            $kkm = $checkkkm->kkm;
        }else{
            $kkm = null;
        }

        // dd($mapellevels);
        return view('kbm.matapelajaran.ubah',compact('kmplists','mapel','unit','levels','mapellevels','kkm'));
    }

    public function update($id, Request $request)
    {
        $unit = Auth::user()->pegawai->unit_id;

        $messages = [
            'nama_mapel.required' => 'Mohon tuliskan nama mata pelajaran',
            'kode_mapel.required' => 'Mohon tuliskan kode mata pelajaran',
            'nomor_mapel.required' => 'Mohon tuliskan nomor urut mata pelajaran',
            'nomor_mapel.numeric' => 'Pastikan nomor urut mata pelajaran hanya mengandung angka',
            'nomor_mapel.min' => 'Pastikan nomor urut mata pelajaran minimal 1',
            'kmp_id.required' => 'Mohon pilih salah satu kelompok mata pelajaran',
            'kkm.required' => 'Mohon tentukan KKM mata pelajaran ini',
            'kkm.numeric' => 'Pastikan KKM hanya mengandung angka',
            'kkm.min' => 'Pastikan KKM dalam rentang 51-100',
            'kkm.max' => 'Pastikan KKM dalam rentang 51-100',
        ];

        // validate dari form tambah
        if(Auth::user()->role_id == 5){
            $request->validate([
                'kkm' => 'required|numeric|min:51|max:100',
            ]);            

            if($request->kkm <= 50){
                return redirect()->back()->with('error', 'KKM Harus Lebih Dari 50');
            }

            $mapel = MataPelajaran::find($id);
            $mapel->kkm = $request->kkm;
            $mapel->save();

            // check semester yg sedang aktif
            $smsaktif = Semester::where('is_active',1)->first();

            // KKM
            $kkm = KkmPelajaran::where('subject_id',$mapel->id)->where('semester_id',$smsaktif->id)->first();
            
            // cek ada engga
            if($kkm){
                $kkm->kkm = $mapel->kkm;
                $kkm->save();
            }else{
                // create to kkm table
                $kkm = KkmPelajaran::create([
                    'subject_id' => $mapel->id,
                    'kkm' => $mapel->kkm,
                    'semester_id' => $smsaktif->id,
                ]);
            }

        }else if($unit == 1){
            $request->validate([
                'kmp_id' => 'required',
            ]);            
            $mapel = MataPelajaran::find($id);
            $mapel->group_subject_id = $request->kmp_id;
            $mapel->save();
        }else{
            // dd($request);
            $request->validate([
                'kode_mapel' => 'required',
                'nomor_mapel' => 'required|numeric|min:1',
                'kmp_id' => 'required',
                'kkm' => 'required|numeric|min:51|max:100',
            ]);

            if($request->kkm <= 50){
                return redirect()->back()->with('error', 'KKM Harus Lebih Dari 50');
            }
            
            $mapel = MataPelajaran::find($id);
            $mapel->subject_acronym = $request->kode_mapel;
            if(in_array(auth()->user()->role_id, array(1,2))){
                $mapel->subject_number = $request->nomor_mapel;
                $mapel->group_subject_id = $request->kmp_id;
            }
            $mapel->is_mulok = $request->mulok == 1 ? $request->mulok : null;
            $mapel->kkm = $request->kkm;
            $mapel->save();

            // check semester yg sedang aktif
            $smsaktif = Semester::where('is_active',1)->first();

            // KKM
            $kkm = KkmPelajaran::where('subject_id',$mapel->id)->where('semester_id',$smsaktif->id)->first();
            // cek ada engga
            if($kkm){
                $kkm->kkm = $mapel->kkm;
                $kkm->save();
            }else{
                // create to kkm table
                $kkm = KkmPelajaran::create([
                    'subject_id' => $mapel->id,
                    'kkm' => $mapel->kkm,
                    'semester_id' => $smsaktif->id,
                ]);
            }

            if(in_array(auth()->user()->role_id, array(1,2)) && $unit == 2){
                MapelKelas::where('subject_id',$id)->delete();

                $kelases = $request->kelas;
                foreach($kelases as $kelas){
                    MapelKelas::create([
                        'level_id' => $kelas,
                        'subject_id' => $id
                    ]);
                }
            }

        }

        return redirect('/kependidikan/kbm/pelajaran/mata-pelajaran')->with('success','Ubah Mata Pelajaran Berhasil');
    }

    public function destroy($id)
    {
        // menghapus data yang dipilih
    	$mapel = MataPelajaran::find($id);
        $mapel->delete();
        
        return redirect('/kependidikan/kbm/pelajaran/mata-pelajaran')->with('success','Hapus Mata Pelajaran Berhasil');
    }

    // BAGIAN KELOMPOK MATA PELAJARAN
    // MOVE CONTROLLER SOON
    
    // Daftar Kelompok Mata Pelajaran
    public function kelompokMataPelajaran()
    {
        //
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $lists = kelompokMataPelajaran::all();
        }else{
            $lists = kelompokMataPelajaran::where('unit_id',$unit)->get();
        }
        $jurusans = Jurusan::all();
        return view('kbm.kelompokmatapelajaran.index',compact('lists','jurusans'));
    }

    public function kelompokMataPelajaranTambah()
    {
        //
        $jurusans = Jurusan::all();
        return view('kbm.kelompokmatapelajaran.tambah',compact('jurusans'));
    }
    
    public function kelompokMataPelajaranStore(Request $request)
    {
        // Validate
        $request->validate([
            'kelompok' => 'required',
        ]);

        $unit = Auth::user()->pegawai->unit_id;
        // create to table
        $kmp = kelompokMataPelajaran::create([
            'group_subject_name' => $request->kelompok,
            'unit_id' => $unit,
            'major_id' => $request->jurusan,
        ]);
        // dd($kmp);

        // return with create success notification
        return redirect('/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran')->with('success','Tambah Kelompok Mata Pelajaran Berhasil');
    }
    
    public function kelompokMataPelajaranHapus($id)
    {
        // destroy
    	$kmp = kelompokMataPelajaran::find($id);
    	$kmp->delete();

        // return with destroy success notification
        return redirect('/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran')->with('success','Hapus Kelompok Mata Pelajaran Berhasil');
    }
    
    public function kelompokMataPelajaranUbah($id, Request $request)
    {
        // Validate
        // dd($request);
        $request->validate([
            'kelompok' => 'required',
        ]);

        // update kelompok mata pelajaran
    	$kmp = kelompokMataPelajaran::find($id);
        $kmp->group_subject_name = $request->kelompok;
        $kmp->major_id = $request->jurusan;
        $kmp->save();

        // update success notification
        return redirect('/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran')->with('success','Ubah Kelompok Mata Pelajaran Berhasil');
    }
}
=======
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Session;

Use App\Models\Skbm\Skbm;
use App\Models\Kbm\KelompokMataPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\KkmPelajaran;
use App\Models\Kbm\MapelKelas;
use App\Models\Kbm\Semester;
use App\Models\Jurusan;
use App\Models\Level;
use App\Models\Rekrutmen\Pegawai;

class PelajaranController extends Controller
{
    public function index()
    {
        $unit = Auth::user()->pegawai->unit_id;
        if(Auth::user()->role_id == 5){
            $employee = Auth::user()->user_id;
            $skbm = Skbm::aktif()->where('unit_id',$unit)->first();
            // dd(Auth::user()->pegawai());
            $mapellist = $skbm->detail->where('employee_id', $employee);
            // dd($mapellist);
            // if()
            
        }else{
            // memanggil semua mapel
            if($unit == 5){
                $mapellist = MataPelajaran::all();
            }else{
                $mapellist = MataPelajaran::where('unit_id',$unit)->get();
            }
        }

        // inisialisasi
        $smsaktif = Semester::where('is_active',1)->first();
        $kkm = [];
        $used = null;
        foreach($mapellist as $index => $mapel){
            if(Auth::user()->role_id == 5){
                $checkkkm = KkmPelajaran::where('subject_id',$mapel->subject_id)->where('semester_id',$smsaktif->id)->first();
            }
            else{
                $checkkkm = KkmPelajaran::where('subject_id',$mapel->id)->where('semester_id',$smsaktif->id)->first();   
            }
            if($checkkkm){
                $kkm[$index] = $checkkkm->kkm;
            }else{
                $kkm[$index] = 'Belum diatur';
            }
            
            if($mapel->jadwalPelajaran()->count() > 0 || $mapel->skbmDetail()->count() > 0) $used[$mapel->id] = 1;
            else $used[$mapel->id] = 0;
        }
        // dd($mapellist[0]);

        return view('kbm.matapelajaran.index',compact('mapellist','unit','kkm','smsaktif','used'));
    }

    public function create()
    {
        $unit = Auth::user()->pegawai->unit_id;
        // menampilkan form tambah
        $kmplists = kelompokMataPelajaran::where('unit_id',$unit)->get();
        $levels = Level::where('unit_id',$unit)->get();
        return view('kbm.matapelajaran.tambah',compact('kmplists','unit','levels'));
    }

    public function store(Request $request)
    {
        // dd($request);

        $unit = Auth::user()->pegawai->unit_id;

        $messages = [
            'nama_mapel.required' => 'Mohon tuliskan nama mata pelajaran',
            'kode_mapel.required' => 'Mohon tuliskan kode mata pelajaran',
            'nomor_mapel.required' => 'Mohon tuliskan nomor urut mata pelajaran',
            'nomor_mapel.numeric' => 'Pastikan nomor urut mata pelajaran hanya mengandung angka',
            'nomor_mapel.min' => 'Pastikan nomor urut mata pelajaran minimal 1',
            'kmp_id.required' => 'Mohon pilih salah satu kelompok mata pelajaran',
            'kkm.required' => 'Mohon tentukan KKM mata pelajaran ini',
            'kkm.numeric' => 'Pastikan KKM hanya mengandung angka',
            'kkm.min' => 'Pastikan KKM dalam rentang 51-100',
            'kkm.max' => 'Pastikan KKM dalam rentang 51-100',
        ];

        // validate dari form tambah
        if($unit == 1){
            $request->validate([
                'nama_mapel' => 'required',
                'kmp_id' => 'required',
            ], $messages);
            // create to table
            MataPelajaran::create([
                'subject_name' => $request->nama_mapel,
                'group_subject_id' => $request->kmp_id,
                'unit_id' => $unit,
            ]);
        }else{
            $request->validate([
                'nama_mapel' => 'required',
                'kode_mapel' => 'required',
                'nomor_mapel' => 'required|numeric|min:1',
                'kmp_id' => 'required',
                'kkm' => 'required|numeric|min:51|max:100',
            ], $messages);

            if($request->kkm <= 50){
                return redirect()->back()->with('error', 'KKM Harus Lebih Dari 50');
            }

            // create to table
            $mapel = MataPelajaran::create([
                'subject_number' => $request->nomor_mapel,
                'subject_name' => $request->nama_mapel,
                'subject_acronym' => $request->kode_mapel,
                'group_subject_id' => $request->kmp_id,
                'kkm' => $request->kkm,
                'unit_id' => $unit,
                'is_mulok' => $request->mulok == 1 ? $request->mulok : null
            ]);

            // check semester yg sedang aktif
            $smsaktif = Semester::where('is_active',1)->first();

            // create to kkm table
            $kkm = KkmPelajaran::create([
                'subject_id' => $mapel->id,
                'kkm' => $mapel->kkm,
                'semester_id' => $smsaktif->id,
            ]);
            if($unit == 2){
                $kelases = $request->input('kelas');
                foreach($kelases as $kelas){
                    MapelKelas::create([
                        'level_id' => $kelas,
                        'subject_id' => $mapel->id
                    ]);
                }
            }
        }


        return redirect('/kependidikan/kbm/pelajaran/mata-pelajaran')->with('success','Tambah Mata Pelajaran Berhasil');
    }

    public function edit($id)
    {
        $unit = Auth::user()->pegawai->unit_id;
        // menampilkan form terisi data yang akan diubah
        $kmplists = kelompokMataPelajaran::where('unit_id',$unit)->get();
        $unit = Auth::user()->pegawai->unit_id;
        $mapel = MataPelajaran::find($id);
        $levels = Level::where('unit_id',$unit)->get();
        $mapellevels = MapelKelas::where('subject_id',$id)->pluck('level_id');

        // check semester yg sedang aktif
        $smsaktif = Semester::where('is_active',1)->first();

        // KKM
        $checkkkm = KkmPelajaran::where('subject_id',$id)->where('semester_id',$smsaktif->id)->first();
        if($checkkkm){
            $kkm = $checkkkm->kkm;
        }else{
            $kkm = null;
        }

        // dd($mapellevels);
        return view('kbm.matapelajaran.ubah',compact('kmplists','mapel','unit','levels','mapellevels','kkm'));
    }

    public function update($id, Request $request)
    {
        $unit = Auth::user()->pegawai->unit_id;

        $messages = [
            'nama_mapel.required' => 'Mohon tuliskan nama mata pelajaran',
            'kode_mapel.required' => 'Mohon tuliskan kode mata pelajaran',
            'nomor_mapel.required' => 'Mohon tuliskan nomor urut mata pelajaran',
            'nomor_mapel.numeric' => 'Pastikan nomor urut mata pelajaran hanya mengandung angka',
            'nomor_mapel.min' => 'Pastikan nomor urut mata pelajaran minimal 1',
            'kmp_id.required' => 'Mohon pilih salah satu kelompok mata pelajaran',
            'kkm.required' => 'Mohon tentukan KKM mata pelajaran ini',
            'kkm.numeric' => 'Pastikan KKM hanya mengandung angka',
            'kkm.min' => 'Pastikan KKM dalam rentang 51-100',
            'kkm.max' => 'Pastikan KKM dalam rentang 51-100',
        ];

        // validate dari form tambah
        if(Auth::user()->role_id == 5){
            $request->validate([
                'kkm' => 'required|numeric|min:51|max:100',
            ]);            

            if($request->kkm <= 50){
                return redirect()->back()->with('error', 'KKM Harus Lebih Dari 50');
            }

            $mapel = MataPelajaran::find($id);
            $mapel->kkm = $request->kkm;
            $mapel->save();

            // check semester yg sedang aktif
            $smsaktif = Semester::where('is_active',1)->first();

            // KKM
            $kkm = KkmPelajaran::where('subject_id',$mapel->id)->where('semester_id',$smsaktif->id)->first();
            
            // cek ada engga
            if($kkm){
                $kkm->kkm = $mapel->kkm;
                $kkm->save();
            }else{
                // create to kkm table
                $kkm = KkmPelajaran::create([
                    'subject_id' => $mapel->id,
                    'kkm' => $mapel->kkm,
                    'semester_id' => $smsaktif->id,
                ]);
            }

        }else if($unit == 1){
            $request->validate([
                'kmp_id' => 'required',
            ]);            
            $mapel = MataPelajaran::find($id);
            $mapel->group_subject_id = $request->kmp_id;
            $mapel->save();
        }else{
            // dd($request);
            $request->validate([
                'kode_mapel' => 'required',
                'nomor_mapel' => 'required|numeric|min:1',
                'kmp_id' => 'required',
                'kkm' => 'required|numeric|min:51|max:100',
            ]);

            if($request->kkm <= 50){
                return redirect()->back()->with('error', 'KKM Harus Lebih Dari 50');
            }
            
            $mapel = MataPelajaran::find($id);
            $mapel->subject_acronym = $request->kode_mapel;
            if(in_array(auth()->user()->role_id, array(1,2))){
                $mapel->subject_number = $request->nomor_mapel;
                $mapel->group_subject_id = $request->kmp_id;
            }
            $mapel->is_mulok = $request->mulok == 1 ? $request->mulok : null;
            $mapel->kkm = $request->kkm;
            $mapel->save();

            // check semester yg sedang aktif
            $smsaktif = Semester::where('is_active',1)->first();

            // KKM
            $kkm = KkmPelajaran::where('subject_id',$mapel->id)->where('semester_id',$smsaktif->id)->first();
            // cek ada engga
            if($kkm){
                $kkm->kkm = $mapel->kkm;
                $kkm->save();
            }else{
                // create to kkm table
                $kkm = KkmPelajaran::create([
                    'subject_id' => $mapel->id,
                    'kkm' => $mapel->kkm,
                    'semester_id' => $smsaktif->id,
                ]);
            }

            if(in_array(auth()->user()->role_id, array(1,2)) && $unit == 2){
                MapelKelas::where('subject_id',$id)->delete();

                $kelases = $request->kelas;
                foreach($kelases as $kelas){
                    MapelKelas::create([
                        'level_id' => $kelas,
                        'subject_id' => $id
                    ]);
                }
            }

        }

        return redirect('/kependidikan/kbm/pelajaran/mata-pelajaran')->with('success','Ubah Mata Pelajaran Berhasil');
    }

    public function destroy($id)
    {
        // menghapus data yang dipilih
    	$mapel = MataPelajaran::find($id);
        $mapel->delete();
        
        return redirect('/kependidikan/kbm/pelajaran/mata-pelajaran')->with('success','Hapus Mata Pelajaran Berhasil');
    }

    // BAGIAN KELOMPOK MATA PELAJARAN
    // MOVE CONTROLLER SOON
    
    // Daftar Kelompok Mata Pelajaran
    public function kelompokMataPelajaran()
    {
        //
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $lists = kelompokMataPelajaran::all();
        }else{
            $lists = kelompokMataPelajaran::where('unit_id',$unit)->get();
        }
        $jurusans = Jurusan::all();
        return view('kbm.kelompokmatapelajaran.index',compact('lists','jurusans'));
    }

    public function kelompokMataPelajaranTambah()
    {
        //
        $jurusans = Jurusan::all();
        return view('kbm.kelompokmatapelajaran.tambah',compact('jurusans'));
    }
    
    public function kelompokMataPelajaranStore(Request $request)
    {
        // Validate
        $request->validate([
            'kelompok' => 'required',
        ]);

        $unit = Auth::user()->pegawai->unit_id;
        // create to table
        $kmp = kelompokMataPelajaran::create([
            'group_subject_name' => $request->kelompok,
            'unit_id' => $unit,
            'major_id' => $request->jurusan,
        ]);
        // dd($kmp);

        // return with create success notification
        return redirect('/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran')->with('success','Tambah Kelompok Mata Pelajaran Berhasil');
    }
    
    public function kelompokMataPelajaranHapus($id)
    {
        // destroy
    	$kmp = kelompokMataPelajaran::find($id);
    	$kmp->delete();

        // return with destroy success notification
        return redirect('/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran')->with('success','Hapus Kelompok Mata Pelajaran Berhasil');
    }
    
    public function kelompokMataPelajaranUbah($id, Request $request)
    {
        // Validate
        // dd($request);
        $request->validate([
            'kelompok' => 'required',
        ]);

        // update kelompok mata pelajaran
    	$kmp = kelompokMataPelajaran::find($id);
        $kmp->group_subject_name = $request->kelompok;
        $kmp->major_id = $request->jurusan;
        $kmp->save();

        // update success notification
        return redirect('/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran')->with('success','Ubah Kelompok Mata Pelajaran Berhasil');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

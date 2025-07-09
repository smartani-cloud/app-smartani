<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Predikat;

use Illuminate\Http\Request;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Semester;
use App\Models\Penilaian\NilaiSikap;
use App\Models\Penilaian\NilaiRapor;
use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\NilaiPengetahuan;
use App\Models\Penilaian\NilaiSikapPts;
use App\Models\Penilaian\RaporPts;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Skbm\Skbm;
use Illuminate\Support\Facades\Auth;

//Used
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RpdType;


class NilaiIklasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $semester = Semester::where('is_active',1)->latest()->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }
        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;
        $rpd = PredikatDeskripsi::where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'rpd_type_id' => $type->id,
        ])->orderBy('predicate', 'ASC')->get();

        return view('penilaian.predikat.nilai_iklas_index', compact('rpd'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->predikat || !$request->deskripsi){
            return redirect()->back()->with(['error' => 'Data predikat gagal ditambahkan, form belum terisi dengan baik']);
        }

        // Cek isinya sesuai dengan yg ada di form
        if(!in_array($request->predikat, array('1','2','3','4','5'))){
            return redirect()->back()->with(['error' => 'Data predikat gagal ditambahkan']);
        }

        $semester = Semester::where('is_active',1)->latest()->first();

        // ID pegawai
        $employee_id = $request->user()->pegawai->id;

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }

        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;

        $rpd = PredikatDeskripsi::select('predicate')->where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'rpd_type_id' => $type->id,
        ])->orderBy('predicate')->pluck('predicate')->toArray();

        if(!in_array($request->predikat, $rpd) && !(implode(',',$rpd) == '1,2,3,4,5')){
            $query = PredikatDeskripsi::create([
                'level_id' => $level,
                'semester_id' => $semester->id,
                'predicate' => $request->predikat,
                'description' => preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi)),
                'rpd_type_id' => $type->id,
                'employee_id' => $employee_id
            ]);
            return redirect()->back()->with(['sukses' => 'Data deskripsi predikat berhasil ditambahkan']);
        } else {
            return redirect()->back()->with(['error' => 'Data deskripsi predikat gagal ditambahkan']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        // Cek isinya sesuai dengan yg ada di form
        if(!in_array($request->id, array('1','2','3','4','5'))){
            return "Hanya predikat yang valid yang dapat diubah.";
        }

        $semester = Semester::where('is_active',1)->latest()->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }

        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;

        $rpd = $request->id ? PredikatDeskripsi::where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'predicate' => $request->id,
            'rpd_type_id' => $type->id,
        ])->latest()->first() : null;

        if($rpd){
            return view('penilaian.predikat.nilai_iklas_ubah', compact('rpd'));
        }
        else{
            return "Ups! Predikat tidak ditemukan. Coba muat ulang halaman.";
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Cek isinya sesuai dengan yg ada di form
        if(!$request->id || ($request->id && !in_array($request->id, array('1','2','3','4','5')))){
            return redirect()->back()->with(['error' => 'Data predikat gagal diubah']);
        }

        if(!$request->deskripsi){
            return redirect()->back()->with(['error' => 'Data predikat gagal diubah, deksripsi tidak boleh kosong']);
        }

        $semester = Semester::where('is_active',1)->latest()->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }

        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;

        $rpd = $request->id ? PredikatDeskripsi::where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'predicate' => $request->id,
            'rpd_type_id' => $type->id,
        ])->latest()->first() : null;

        if($rpd){
            $rpd->description = preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi));
            $rpd->employee_id = $request->user()->pegawai->id;
            $rpd->save();

            return redirect()->back()->with(['sukses' => 'Data deskripsi predikat bintang '.$rpd->predicate.' berhasil diperbarui']);
        } else {
            return redirect()->back()->with(['error' => 'Data deskripsi predikat gagal diperbarui']);
        }
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
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Predikat;

use Illuminate\Http\Request;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Semester;
use App\Models\Penilaian\NilaiSikap;
use App\Models\Penilaian\NilaiRapor;
use App\Http\Controllers\Controller;
use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\Penilaian\KDSetting;
use App\Models\Penilaian\NilaiPengetahuan;
use App\Models\Penilaian\NilaiSikapPts;
use App\Models\Penilaian\RaporPts;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Skbm\Skbm;
use Illuminate\Support\Facades\Auth;

//Used
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RpdType;


class NilaiIklasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $semester = Semester::where('is_active',1)->latest()->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }
        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;
        $rpd = PredikatDeskripsi::where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'rpd_type_id' => $type->id,
        ])->orderBy('predicate', 'ASC')->get();

        return view('penilaian.predikat.nilai_iklas_index', compact('rpd'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->predikat || !$request->deskripsi){
            return redirect()->back()->with(['error' => 'Data predikat gagal ditambahkan, form belum terisi dengan baik']);
        }

        // Cek isinya sesuai dengan yg ada di form
        if(!in_array($request->predikat, array('1','2','3','4','5'))){
            return redirect()->back()->with(['error' => 'Data predikat gagal ditambahkan']);
        }

        $semester = Semester::where('is_active',1)->latest()->first();

        // ID pegawai
        $employee_id = $request->user()->pegawai->id;

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }

        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;

        $rpd = PredikatDeskripsi::select('predicate')->where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'rpd_type_id' => $type->id,
        ])->orderBy('predicate')->pluck('predicate')->toArray();

        if(!in_array($request->predikat, $rpd) && !(implode(',',$rpd) == '1,2,3,4,5')){
            $query = PredikatDeskripsi::create([
                'level_id' => $level,
                'semester_id' => $semester->id,
                'predicate' => $request->predikat,
                'description' => preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi)),
                'rpd_type_id' => $type->id,
                'employee_id' => $employee_id
            ]);
            return redirect()->back()->with(['sukses' => 'Data deskripsi predikat berhasil ditambahkan']);
        } else {
            return redirect()->back()->with(['error' => 'Data deskripsi predikat gagal ditambahkan']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        // Cek isinya sesuai dengan yg ada di form
        if(!in_array($request->id, array('1','2','3','4','5'))){
            return "Hanya predikat yang valid yang dapat diubah.";
        }

        $semester = Semester::where('is_active',1)->latest()->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }

        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;

        $rpd = $request->id ? PredikatDeskripsi::where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'predicate' => $request->id,
            'rpd_type_id' => $type->id,
        ])->latest()->first() : null;

        if($rpd){
            return view('penilaian.predikat.nilai_iklas_ubah', compact('rpd'));
        }
        else{
            return "Ups! Predikat tidak ditemukan. Coba muat ulang halaman.";
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Cek isinya sesuai dengan yg ada di form
        if(!$request->id || ($request->id && !in_array($request->id, array('1','2','3','4','5')))){
            return redirect()->back()->with(['error' => 'Data predikat gagal diubah']);
        }

        if(!$request->deskripsi){
            return redirect()->back()->with(['error' => 'Data predikat gagal diubah, deksripsi tidak boleh kosong']);
        }

        $semester = Semester::where('is_active',1)->latest()->first();

        $type = RpdType::where('rpd_type','Nilai IKLaS')->first();
        if(!$type){
            $type = new RpdType();
            $type->rpd_type = 'Nilai IKLaS';
            $type->save();

            $type->fresh();
        }

        $level = Kelas::select('level_id')->where('teacher_id', $request->user()->pegawai->id)->where('academic_year_id', $semester->tahunAjaran->id)->whereNotNull('level_id')->latest()->first()->level_id;

        $rpd = $request->id ? PredikatDeskripsi::where([
            'level_id' => $level,
            'semester_id' => $semester->id,
            'predicate' => $request->id,
            'rpd_type_id' => $type->id,
        ])->latest()->first() : null;

        if($rpd){
            $rpd->description = preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi));
            $rpd->employee_id = $request->user()->pegawai->id;
            $rpd->save();

            return redirect()->back()->with(['sukses' => 'Data deskripsi predikat bintang '.$rpd->predicate.' berhasil diperbarui']);
        } else {
            return redirect()->back()->with(['error' => 'Data deskripsi predikat gagal diperbarui']);
        }
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
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

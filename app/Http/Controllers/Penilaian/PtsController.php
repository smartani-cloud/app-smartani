<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Jenssegers\Date\Date;

use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
//
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\TilawahType;
//
use App\Models\Penilaian\NilaiPengetahuanDetail;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\RaporPts;
use App\Models\Penilaian\TanggalRapor;
//
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use function PHPUnit\Framework\isEmpty;

class PtsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::where('id', $smt_aktif)->first();

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 3], ['employee_id', $employee_id]])->orderBy('created_at', 'ASC')->get();
        //$siswa = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->has('siswa')->with('siswa:id,student_id,unit_id')->get()->pluck('siswa')->sortBy(function($query){return $query->identitas->student_name;});
        $siswa = $kelas->siswa()->select('id','student_id','unit_id')->get()->sortBy(function($query){return $query->identitas->student_name;});

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
            $deskripsipts = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
                    $countdeskripsipts = RaporPts::where('score_id', $nilairapor->id)->count();
                    if ($countdeskripsipts > 0) {
                        $deskripsipts[$key] = RaporPts::where('score_id', $nilairapor->id)->first();
                    } else {
                        $deskripsipts[$key] = FALSE;
                    }
                } else {
                    $deskripsipts = FALSE;
                }
            }
        }

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }

        return view('penilaian.deskripsipts', compact('rpd', 'siswa', 'deskripsipts', 'kelas', 'semester', 'countrapor', 'validasi'));
    }

    public function descpts()
    {
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 3], ['employee_id', auth()->user()->pegawai->id]])->orderBy('created_at', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        return view('penilaian.descpts', compact('rpd'));
    }

    public function simpanDeskripsi(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
        ]);

        //Jenis predikat deskripsi untuk ekstra
        $rpd_type_id = 3;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = PredikatDeskripsi::create([
            'description' => $request->deskripsi,
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaian/descpts')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaian/descpts')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusDeskripsi(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaian/descpts')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaian/descpts')->with(['error' => 'Data gagal dihapus']);
        }
    }

    //Ubah Predikat Deskripsi
    public function ubahDeskripsi(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->description = $request->deskripsi;

        if ($query->save()) {
            return redirect('/kependidikan/penilaian/descpts')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaian/descpts')->with(['error' => 'Data gagal diubah']);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $smt_aktif = session('semester_aktif');
        $iserror = FALSE;

        $siswa_id = $request->siswa_id;
        $deskripsi = $request->deskripsi;

        foreach ($siswa_id as $key => $siswas_id) {
            $siswa = Siswa::where('id', $siswas_id)->first();
            $rpd = PredikatDeskripsi::where('id', $deskripsi[$key])->first();
            $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['class_id', $siswa->class_id], ['semester_id', $smt_aktif]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['class_id', $siswa->class_id], ['semester_id', $smt_aktif]])->first();
                
                $namawali = auth()->user()->pegawai->name;
                $nilairapor->hr_name = $namawali;
                $countpts = RaporPts::where('score_id', $nilairapor->id)->count();
                if ($nilairapor->save() && $countpts > 0) {
                    $deskripsipts = RaporPts::where('score_id', $nilairapor->id)->first();
                    $deskripsipts->rpd_id = $deskripsi[$key] ? $deskripsi[$key] : null;
                    $deskripsipts->description = $rpd ? $rpd->description : null;
                    
                    if ($deskripsipts->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    $deskripsipts = RaporPts::create([
                        'score_id' => $nilairapor->id,
                        'rpd_id' => $deskripsi[$key] ? $deskripsi[$key] : null,
                        'description' => $rpd ? $rpd->description : null
                    ]);
                    if ($deskripsipts->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            } else {
                $namawali = auth()->user()->pegawai->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $nilairapor = NilaiRapor::create([
                    'student_id' => $siswas_id,
                    'semester_id' => $smt_aktif,
                    'class_id' => $siswa->class_id,
                    'report_status_id' => 0,
                    'acc_id' => 0,
                    'unit_id' => $siswa->unit_id,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek
                ]);
                $nilairapor->save();
                $deskripsipts = RaporPts::create([
                    'score_id' => $nilairapor->id,
                    'rpd_id' => $deskripsi[$key] ? $deskripsi[$key] : null,
                    'description' => $rpd ? $rpd->description : null
                ]);
                if ($deskripsipts->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaian/deskripsipts')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaian/deskripsipts')->with(['error' => 'Data gagal disimpan']);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function cetakpts(Request $request)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::aktif()->first();
        $kelas = $tahunsekarang->kelas()->where('teacher_id', $employee_id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::find($smt_aktif);
        $siswa = $kelas->siswa()->select('id','student_id','unit_id')->get()->sortBy(function($query){return $query->identitas->student_name;});
        if ($siswa->isEmpty()) {
            $nilairapor = FALSE;
        } else {
            foreach ($siswa as $key => $siswas) {
                $datarapor = $siswas->nilaiRapor()->where('semester_id', $semester->id)->first();
                if ($datarapor) {
                    $nilairapor[$key] = $datarapor;
                } else {
                    $nilairapor[$key] = FALSE;
                }
            }
        }
        return view('penilaian.walas.pts_index', compact('siswa', 'semester', 'kelas', 'nilairapor'));
    }

    /**
     * Print cover the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cover(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;
            if($unit) {
                $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_pts_id' => 1])->first();

                if($rapor){
                    return view('penilaian.pts_cover', compact('siswa', 'unit', 'semester'));
                }
                else{
                    Session::flash('danger', 'Nilai laporan tengah semester Ananda '.$siswa->student_name.' belum divalidasi');
                }
            }
        }

        return redirect()->route('pts.cetak');
    }

    /**
     * Print the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporan(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;

            if($unit) {
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                // Components
                $subject_ids = $rapor->pengetahuan()->pluck('id');
                $nilai_harian = NilaiPengetahuanDetail::whereIn('score_knowledge_id', $subject_ids)->whereNotNull('score')->get();

                $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;

                $kelompok = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where(function ($query) use ($siswa) {
                    $query->where(function ($query) {
                        $query->whereDoesntHave('jurusan');
                    })->orWhere(function ($query) use ($siswa) {
                        $query->where('major_id', $siswa->kelas->major_id);
                    });
                })->get();
                
                $matapelajarans = MataPelajaran::whereIn('group_subject_id', $kelompok->pluck('id'));
                if($unit->name == 'SD'){
                    $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                        $q->where('level_id',$rapor->kelas->level_id);
                    });
                }

                $mata_pelajaran = $matapelajarans->count();

                $mulok = $matapelajarans->mulok()->count() > 0 ? 1 : 0;

                $total_rows = $kelompok->count() + $mata_pelajaran + $mulok;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    return view('penilaian.pts_laporan', compact('pts_date', 'siswa', 'unit', 'semester', 'rapor', 'nilai_harian', 'kelompok', 'total_rows', 'digital'));
                }
                else{
                    Session::flash('danger', 'Nilai laporan tengah semester Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pts.cetak');
    }

    public function laporantk(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;

            if($unit) {
                // Components
                $aspek = AspekPerkembangan::aktif()->get();

                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    return view('penilaian.pts_laporan_tk', compact('pts_date', 'siswa', 'unit', 'semester', 'rapor', 'aspek', 'digital'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pts.cetak');
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Jenssegers\Date\Date;

use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
//
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\TilawahType;
//
use App\Models\Penilaian\NilaiPengetahuanDetail;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\RaporPts;
use App\Models\Penilaian\TanggalRapor;
//
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use function PHPUnit\Framework\isEmpty;

class PtsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::where('id', $smt_aktif)->first();

        $rpd = PredikatDeskripsi::where([['rpd_type_id', 3], ['employee_id', $employee_id]])->orderBy('created_at', 'ASC')->get();
        //$siswa = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->has('siswa')->with('siswa:id,student_id,unit_id')->get()->pluck('siswa')->sortBy(function($query){return $query->identitas->student_name;});
        $siswa = $kelas->siswa()->select('id','student_id','unit_id')->get()->sortBy(function($query){return $query->identitas->student_name;});

        if ($siswa->isEmpty()) {
            $siswa = FALSE;
            $deskripsipts = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
                    $countdeskripsipts = RaporPts::where('score_id', $nilairapor->id)->count();
                    if ($countdeskripsipts > 0) {
                        $deskripsipts[$key] = RaporPts::where('score_id', $nilairapor->id)->first();
                    } else {
                        $deskripsipts[$key] = FALSE;
                    }
                } else {
                    $deskripsipts = FALSE;
                }
            }
        }

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }

        return view('penilaian.deskripsipts', compact('rpd', 'siswa', 'deskripsipts', 'kelas', 'semester', 'countrapor', 'validasi'));
    }

    public function descpts()
    {
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 3], ['employee_id', auth()->user()->pegawai->id]])->orderBy('created_at', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        return view('penilaian.descpts', compact('rpd'));
    }

    public function simpanDeskripsi(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
        ]);

        //Jenis predikat deskripsi untuk ekstra
        $rpd_type_id = 3;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = PredikatDeskripsi::create([
            'description' => $request->deskripsi,
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaian/descpts')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaian/descpts')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusDeskripsi(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaian/descpts')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaian/descpts')->with(['error' => 'Data gagal dihapus']);
        }
    }

    //Ubah Predikat Deskripsi
    public function ubahDeskripsi(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->description = $request->deskripsi;

        if ($query->save()) {
            return redirect('/kependidikan/penilaian/descpts')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaian/descpts')->with(['error' => 'Data gagal diubah']);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $smt_aktif = session('semester_aktif');
        $iserror = FALSE;

        $siswa_id = $request->siswa_id;
        $deskripsi = $request->deskripsi;

        foreach ($siswa_id as $key => $siswas_id) {
            $siswa = Siswa::where('id', $siswas_id)->first();
            $rpd = PredikatDeskripsi::where('id', $deskripsi[$key])->first();
            $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['class_id', $siswa->class_id], ['semester_id', $smt_aktif]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['class_id', $siswa->class_id], ['semester_id', $smt_aktif]])->first();
                
                $namawali = auth()->user()->pegawai->name;
                $nilairapor->hr_name = $namawali;
                $countpts = RaporPts::where('score_id', $nilairapor->id)->count();
                if ($nilairapor->save() && $countpts > 0) {
                    $deskripsipts = RaporPts::where('score_id', $nilairapor->id)->first();
                    $deskripsipts->rpd_id = $deskripsi[$key] ? $deskripsi[$key] : null;
                    $deskripsipts->description = $rpd ? $rpd->description : null;
                    
                    if ($deskripsipts->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    $deskripsipts = RaporPts::create([
                        'score_id' => $nilairapor->id,
                        'rpd_id' => $deskripsi[$key] ? $deskripsi[$key] : null,
                        'description' => $rpd ? $rpd->description : null
                    ]);
                    if ($deskripsipts->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            } else {
                $namawali = auth()->user()->pegawai->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $nilairapor = NilaiRapor::create([
                    'student_id' => $siswas_id,
                    'semester_id' => $smt_aktif,
                    'class_id' => $siswa->class_id,
                    'report_status_id' => 0,
                    'acc_id' => 0,
                    'unit_id' => $siswa->unit_id,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek
                ]);
                $nilairapor->save();
                $deskripsipts = RaporPts::create([
                    'score_id' => $nilairapor->id,
                    'rpd_id' => $deskripsi[$key] ? $deskripsi[$key] : null,
                    'description' => $rpd ? $rpd->description : null
                ]);
                if ($deskripsipts->save()) {
                    $iserror = FALSE;
                } else {
                    $iserror = TRUE;
                    break;
                }
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaian/deskripsipts')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaian/deskripsipts')->with(['error' => 'Data gagal disimpan']);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function cetakpts(Request $request)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::aktif()->first();
        $kelas = $tahunsekarang->kelas()->where('teacher_id', $employee_id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::find($smt_aktif);
        $siswa = $kelas->siswa()->select('id','student_id','unit_id')->get()->sortBy(function($query){return $query->identitas->student_name;});
        if ($siswa->isEmpty()) {
            $nilairapor = FALSE;
        } else {
            foreach ($siswa as $key => $siswas) {
                $datarapor = $siswas->nilaiRapor()->where('semester_id', $semester->id)->first();
                if ($datarapor) {
                    $nilairapor[$key] = $datarapor;
                } else {
                    $nilairapor[$key] = FALSE;
                }
            }
        }
        return view('penilaian.walas.pts_index', compact('siswa', 'semester', 'kelas', 'nilairapor'));
    }

    /**
     * Print cover the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cover(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;
            if($unit) {
                $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_pts_id' => 1])->first();

                if($rapor){
                    return view('penilaian.pts_cover', compact('siswa', 'unit', 'semester'));
                }
                else{
                    Session::flash('danger', 'Nilai laporan tengah semester Ananda '.$siswa->student_name.' belum divalidasi');
                }
            }
        }

        return redirect()->route('pts.cetak');
    }

    /**
     * Print the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporan(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;

            if($unit) {
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                // Components
                $subject_ids = $rapor->pengetahuan()->pluck('id');
                $nilai_harian = NilaiPengetahuanDetail::whereIn('score_knowledge_id', $subject_ids)->whereNotNull('score')->get();

                $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;

                $kelompok = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where(function ($query) use ($siswa) {
                    $query->where(function ($query) {
                        $query->whereDoesntHave('jurusan');
                    })->orWhere(function ($query) use ($siswa) {
                        $query->where('major_id', $siswa->kelas->major_id);
                    });
                })->get();
                
                $matapelajarans = MataPelajaran::whereIn('group_subject_id', $kelompok->pluck('id'));
                if($unit->name == 'SD'){
                    $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                        $q->where('level_id',$rapor->kelas->level_id);
                    });
                }

                $mata_pelajaran = $matapelajarans->count();

                $mulok = $matapelajarans->mulok()->count() > 0 ? 1 : 0;

                $total_rows = $kelompok->count() + $mata_pelajaran + $mulok;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    return view('penilaian.pts_laporan', compact('pts_date', 'siswa', 'unit', 'semester', 'rapor', 'nilai_harian', 'kelompok', 'total_rows', 'digital'));
                }
                else{
                    Session::flash('danger', 'Nilai laporan tengah semester Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pts.cetak');
    }

    public function laporantk(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;

            if($unit) {
                // Components
                $aspek = AspekPerkembangan::aktif()->get();

                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    return view('penilaian.pts_laporan_tk', compact('pts_date', 'siswa', 'unit', 'semester', 'rapor', 'aspek', 'digital'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pts.cetak');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

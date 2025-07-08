<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Models\Penilaian\PredikatDeskripsi;
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


class NilaiSikapController extends Controller
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

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 1], ['employee_id', $employee_id]])->orderBy('predicate', 'ASC')->get();

        // Tambahan type RPD 7 & 8
        $rpd_spiritual =  PredikatDeskripsi::where([['rpd_type_id', 7], ['employee_id', $employee_id]])->orderBy('predicate', 'ASC')->get();;
        $rpd_sosial =  PredikatDeskripsi::where([['rpd_type_id', 8], ['employee_id', $employee_id]])->orderBy('predicate', 'ASC')->get();;

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        }

        $i = 0;
        $nilaispiritual = NULL;
        $nilaisosial = NULL;
        $countrapor = $validasi = 0;
        if ($siswa) {
            foreach ($siswa as $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['class_id', $class_id], ['semester_id', $smt_aktif]])->first();
                    $nilaisikap = NilaiSikap::where([['score_id', $nilairapor->id]])->count();
                    if ($nilaisikap > 0) {
                        $nilaispiritual[$i] = NilaiSikap::where([['score_id', $nilairapor->id], ['ras_type_id', 1]])->first();
                        $nilaisosial[$i] = NilaiSikap::where([['score_id', $nilairapor->id], ['ras_type_id', 2]])->first();
                    } else {
                        $nilaispiritual[$i] = NULL;
                        $nilaisosial[$i] = NULL;
                    }
                } else {
                    $nilaispiritual[$i] = NULL;
                    $nilaisosial[$i] = NULL;
                }
                $i++;
            }
            $validasi = NilaiRapor::where([['report_status_id', 0], ['class_id', $class_id], ['semester_id', $smt_aktif]])->count();
        }

        return view('penilaian.nilaisikap', compact('rpd', 'siswa', 'nilaispiritual', 'nilaisosial', 'semester', 'kelas', 'countrapor', 'validasi','rpd_spiritual', 'rpd_sosial'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function simpan(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();
        $kelas = Kelas::where('teacher_id', $employee_id)->where('academic_year_id', $tahunsekarang->id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $spiritual = $request->spiritual;
        $sosial = $request->sosial;

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();

        if ($siswa) {
            $x = 0;
            foreach ($siswa as $siswa) {
                $nilairapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $siswa->class_id], ['semester_id', $smt_aktif]])->count();
                if ($nilairapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $siswa->class_id], ['semester_id', $smt_aktif]])->first();
                    $namawali = auth()->user()->pegawai->name;
                    $nilairapor->hr_name = $namawali;
                    $nilaisikap = NilaiSikap::where([['score_id', $nilairapor->id]])->count();
                    if ($nilairapor->save() && $nilaisikap > 0) {
                        $predikatspiritual = $spiritual[$x] ? PredikatDeskripsi::where([['id', $spiritual[$x]], ['employee_id', $employee_id]])->first() : null;
                        $predikatsosial = $sosial[$x] ? PredikatDeskripsi::where([['id', $sosial[$x]], ['employee_id', $employee_id]])->first() : null;
                        
                        $nilaispiritual = NilaiSikap::where([['score_id', $nilairapor->id], ['ras_type_id', 1]])->first();
                        if($nilaispiritual){
                            if($predikatspiritual){
                                $nilaispiritual->rpd_id = $spiritual[$x];
                                $nilaispiritual->predicate = $predikatspiritual->predicate;
                                $nilaispiritual->description = $predikatspiritual->description;
                            }
                            else{
                                $nilaispiritual->rpd_id = NULL;
                                $nilaispiritual->predicate = NULL;
                                $nilaispiritual->description = NULL;
                            }
                            $nilaispiritual->save();

                            $save_spiritual = TRUE;
                        }
                        else{
                            if($predikatspiritual){
                                $nilaispiritual = NilaiSikap::create([
                                    'score_id' => $nilairapor->id,
                                    'ras_type_id' => 1,
                                    'rpd_id' => $predikatspiritual->id,
                                    'predicate' => $predikatspiritual->predicate,
                                    'description' => $predikatspiritual->description
                                ]);

                                $save_spiritual = TRUE;
                            }
                        }
                        
                        $nilaisosial = NilaiSikap::where([['score_id', $nilairapor->id], ['ras_type_id', 2]])->first();
                        if($nilaisosial){
                            if($predikatsosial){
                                $nilaisosial->rpd_id = $sosial[$x];
                                $nilaisosial->predicate = $predikatsosial->predicate;
                                $nilaisosial->description = $predikatsosial->description;
                            }
                            else{
                                $nilaisosial->rpd_id = NULL;
                                $nilaisosial->predicate = NULL;
                                $nilaisosial->description = NULL;
                            }
                            $nilaisosial->save();

                            $save_sosial = TRUE;
                        }
                        else{
                            if($predikatsosial){
                                $nilaisosial = NilaiSikap::create([
                                    'score_id' => $nilairapor->id,
                                    'ras_type_id' => 2,
                                    'rpd_id' => $predikatsosial->id,
                                    'predicate' => $predikatsosial->predicate,
                                    'description' => $predikatsosial->description
                                ]);

                                $save_sosial = TRUE;
                            }
                        }

                        if ($save_spiritual || $save_sosial) {
                            $suksesinput[] = TRUE;
                        } else {
                            $suksesinput[] = FALSE;
                        }
                    } else {
                        $predikatspiritual = PredikatDeskripsi::where([['id', $spiritual[$x]], ['employee_id', $employee_id]])->first();
                        $predikatsosial = PredikatDeskripsi::where([['id', $sosial[$x]], ['employee_id', $employee_id]])->first();
                        if($predikatspiritual){
                            // dd('spiritual :'.$spiritual[$x],'id rapor : '.$nilairapor->id,'siswa :'.$siswa);
                            $nilaispiritual = NilaiSikap::create([
                                'score_id' => $nilairapor->id,
                                'ras_type_id' => 1,
                                'rpd_id' => $predikatspiritual->id,
                                'predicate' => $predikatspiritual->predicate,
                                'description' => $predikatspiritual->description
                            ]);
                        }else{
                            $nilaispiritual = NilaiSikap::create([
                                'score_id' => $nilairapor->id,
                                'ras_type_id' => 1,
                                'rpd_id' => null,
                                'predicate' => null,
                                'description' => null
                            ]);
                        }
                        if($predikatsosial){
                            // dd('sosial :'.$sosial[$x],'id rapor : '.$nilairapor->id,'siswa :'.$siswa);
                            $nilaisosial = NilaiSikap::create([
                                'score_id' => $nilairapor->id,
                                'ras_type_id' => 2,
                                'rpd_id' => $predikatsosial->id,
                                'predicate' => $predikatsosial->predicate,
                                'description' => $predikatsosial->description
                            ]);
                        }else{
                            $nilaisosial = NilaiSikap::create([
                                'score_id' => $nilairapor->id,
                                'ras_type_id' => 2,
                                'rpd_id' => null,
                                'predicate' => null,
                                'description' => null
                            ]);
                        }
                        if ($nilaispiritual->save() && $nilaisosial->save()) {
                            $suksesinput[] = TRUE;
                        } else {
                            $suksesinput[] = FALSE;
                        }
                    }
                } else {
                    $namawali = auth()->user()->pegawai->name;
                    $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                    $namakepsek = $kepsek->name;
                    $nilairapor = NilaiRapor::create([
                        'student_id' => $siswa->id,
                        'semester_id' => $smt_aktif,
                        'class_id' => $siswa->class_id,
                        'report_status_id' => 0,
                        'acc_id' => 0,
                        'unit_id' => $siswa->unit_id,
                        'hr_name' => $namawali,
                        'hm_name' => $namakepsek
                    ]);
                    $nilairapor->save();
                    $nilaisikap = NilaiSikap::where([['score_id', $nilairapor->id]])->count();
                    if ($nilaisikap > 0) {
                        $predikatspiritual = $spiritual[$x] ? PredikatDeskripsi::where([['id', $spiritual[$x]], ['employee_id', $employee_id]])->first() : null;
                        $predikatsosial = $sosial[$x] ? PredikatDeskripsi::where([['id', $sosial[$x]], ['employee_id', $employee_id]])->first() : null;
                        
                        $nilaispiritual = NilaiSikap::where([['score_id', $nilairapor->id], ['ras_type_id', 1]])->first();
                        if($nilaispiritual){
                            if($predikatspiritual){
                                $nilaispiritual->rpd_id = $spiritual[$x];
                                $nilaispiritual->predicate = $predikatspiritual->predicate;
                                $nilaispiritual->description = $predikatspiritual->description;
                            }
                            else{
                                $nilaispiritual->rpd_id = NULL;
                                $nilaispiritual->predicate = NULL;
                                $nilaispiritual->description = NULL;
                            }
                            $nilaispiritual->save();

                            $save_spiritual = TRUE;
                        }
                        else{
                            if($predikatspiritual){
                                $nilaispiritual = NilaiSikap::create([
                                    'score_id' => $nilairapor->id,
                                    'ras_type_id' => 1,
                                    'rpd_id' => $predikatspiritual->id,
                                    'predicate' => $predikatspiritual->predicate,
                                    'description' => $predikatspiritual->description
                                ]);

                                $save_spiritual = TRUE;
                            }
                        }
                        
                        $nilaisosial = NilaiSikap::where([['score_id', $nilairapor->id], ['ras_type_id', 2]])->first();
                        if($nilaisosial){
                            if($predikatsosial){
                                $nilaisosial->rpd_id = $sosial[$x];
                                $nilaisosial->predicate = $predikatsosial->predicate;
                                $nilaisosial->description = $predikatsosial->description;
                            }
                            else{
                                $nilaisosial->rpd_id = NULL;
                                $nilaisosial->predicate = NULL;
                                $nilaisosial->description = NULL;
                            }
                            $nilaisosial->save();

                            $save_sosial = TRUE;
                        }
                        else{
                            if($predikatsosial){
                                $nilaisosial = NilaiSikap::create([
                                    'score_id' => $nilairapor->id,
                                    'ras_type_id' => 2,
                                    'rpd_id' => $predikatsosial->id,
                                    'predicate' => $predikatsosial->predicate,
                                    'description' => $predikatsosial->description
                                ]);

                                $save_sosial = TRUE;
                            }
                        }

                        if ($save_spiritual || $save_sosial) {
                            $suksesinput[] = TRUE;
                        } else {
                            $suksesinput[] = FALSE;
                        }
                    }
                    else {
                        $predikatspiritual = $spiritual[$x] ? PredikatDeskripsi::where([['id', $spiritual[$x]], ['employee_id', $employee_id]])->first() : null;
                        if($predikatspiritual){
                            $nilaispiritual = NilaiSikap::create([
                                'score_id' => $nilairapor->id,
                                'ras_type_id' => 1,
                                'rpd_id' => $predikatspiritual->id,
                                'predicate' => $predikatspiritual->predicate,
                                'description' => $predikatspiritual->description
                            ]);
                            
                            $save_spiritual = TRUE;
                        }
                        
                        $predikatsosial = $sosial[$x] ? PredikatDeskripsi::where([['id', $sosial[$x]], ['employee_id', $employee_id]])->first() : null;
                        if($predikatsosial){
                            $nilaisosial = NilaiSikap::create([
                                'score_id' => $nilairapor->id,
                                'ras_type_id' => 2,
                                'rpd_id' => $predikatsosial->id,
                                'predicate' => $predikatsosial->predicate,
                                'description' => $predikatsosial->description
                            ]);
                            
                            $save_sosial = TRUE;
                        }
                        if (($predikatspiritual && $save_spiritual) || ($predikatsosial && $save_sosial)) {
                            $suksesinput[] = TRUE;
                        } else {
                            $suksesinput[] = FALSE;
                        }
                    }
                }
                $x++;
            }
            $iserror = FALSE;
            foreach ($suksesinput as $suksesinput) {
                if ($suksesinput == FALSE) {
                    $iserror = TRUE;
                    break;
                }
            }

            if ($iserror == FALSE) {
                return redirect('/kependidikan/penilaian/nilaisikap')->with(['sukses' => 'Data berhasil disimpan']);
            } else {
                return redirect('/kependidikan/penilaian/nilaisikap')->with(['error' => 'Data gagal disimpan']);
            }
        }

        $query = PredikatDeskripsi::create([
            'predicate' => $request->predikat,
            'description' => preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi)),
            'employee_id' => $employee_id,
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaian/predikatsikap')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaian/predikatsikap')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function predikatsikap()
    {

        $employee_id = auth()->user()->pegawai->id;
        // $rpd = PredikatDeskripsi::where([['rpd_type_id', 1], ['employee_id', $employee_id]])->orderBy('predicate', 'ASC')->get();
        $rpd = PredikatDeskripsi::where('employee_id', $employee_id)->whereIn('rpd_type_id', array(1,7,8))->orderBy('rpd_type_id', 'ASC')->orderBy('predicate', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }

        return view('penilaian.predikatsikap', compact('rpd'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanPredikat(Request $request)
    {
        // $request->validate([
        //     'predikat' => 'required',
        //     'deskripsi' => 'required',
        // ]);

        // Karna pake modal, validatenya ga muncul, jdnya tk bikin required manual aja
        if(!$request->predikat || !$request->sikap || !$request->deskripsi){
            return redirect()->back()->with(['error' => 'Data gagal ditambahkan, form belum terisi semua']);
        }

        // cek isinya sesuai dengan yg ada di form
        if( !in_array($request->sikap, array('7','8')) || !in_array($request->predikat, array('A','B','C','D')) ){
            return redirect()->back()->with(['error' => 'Data gagal ditambahkan, form belum terisi semua']);
        }

        //Jenis predikat deskripsi untuk nilai sikap // ku ganti sikapnya
        $rpd_type_id = $request->sikap;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = PredikatDeskripsi::create([
            'predicate' => $request->predikat,
            'description' => preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi)),
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect()->back()->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect()->back()->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function ubahPredikat(Request $request)
    {
        // Karna pake modal, validatenya ga muncul, jdnya tk bikin required manual aja
        if(!$request->predikat || !$request->sikap || !$request->deskripsi){
            return redirect()->back()->with(['error' => 'Data gagal diubah, form belum terisi semua']);
        }

        // cek isinya sesuai dengan yg ada di form
        if( !in_array($request->sikap, array('7','8')) || !in_array($request->predikat, array('A','B','C','D')) ){
            return redirect()->back()->with(['error' => 'Data gagal diubah, form belum terisi semua']);
        }

        $query = PredikatDeskripsi::where('id', $request->id)->first();
        $query->predicate = $request->predikat;
        $query->rpd_type_id = $request->sikap;
        $query->description = preg_replace('/\s+/', ' ',str_replace(array("\r\n", "\r", "\n"), ' ', $request->deskripsi));

        if ($query->save()) {
            return redirect('/kependidikan/penilaian/predikatsikap')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaian/predikatsikap')->with(['error' => 'Data gagal diubah']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusPredikat(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaian/predikatsikap')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaian/predikatsikap')->with(['error' => 'Data gagal dihapus']);
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

    public function nilaipts()
    {
        $employee_id = auth()->user()->pegawai->id;
        $skbm = Skbm::aktif()->where('unit_id', auth()->user()->pegawai->unit_id)->first();
        $mapelskbm = $skbm->detail->where('employee_id', $employee_id)->pluck('subject_id');
        $semester_id = session('semester_aktif');
        if (empty($mapelskbm)) {
            $mapel = NULL;
        } else {
            $mapel = MataPelajaran::whereIn('id', $mapelskbm)->where('subject_name', 'not like', "Qur'an")->get();
        }
        $semester = Semester::where('id', $semester_id)->first();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();

        return view('penilaian.nilaisikappts', compact('mapel', 'semester', 'level'));
    }

    public function getkelas(Request $request)
    {
        $jadwal = JadwalPelajaran::where([['level_id', $request->level_id], ['teacher_id', auth()->user()->pegawai->id], ['subject_id', $request->mapel_id], ['semester_id', session('semester_aktif')]])->get();
        if ($jadwal->isEmpty()) {
            $kelas = NULL;
        } else {
            foreach ($jadwal as $jadwals) {
                $idkelas[] = $jadwals->class_id;
            }
        }
        if (isset($idkelas)) {
            $kelas = Kelas::whereIn('id', $idkelas)->get();
        } else {
            $kelas = Kelas::where('id', 0)->get();
        }
        $view = view('penilaian.getlevel', compact('kelas', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswa(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $class_id = $request->class_id;
        $level_id = $request->level_id;
        $subject_id = $request->mapel_id;
        $semester_id = session('semester_aktif');
        $countrapor = $validasi = 0;

        $siswa = Siswa::where([['class_id', $class_id]])->with('identitas:id,student_name')-> get()->sortBy('identitas.student_name')->values();
        if ($siswa->isEmpty()) {
            $siswa = FALSE;
        } else {
            $validasi = NilaiRapor::where([['report_status_id', 0], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            foreach ($siswa as $key => $siswas) {
                $countrapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
                if ($countrapor > 0) {
                    $nilairapor = NilaiRapor::where([['student_id', $siswas->id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();
                    $countsikap = NilaiSikapPts::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                    if ($countsikap > 0) {
                        $nilaisikap[$key] = NilaiSikapPts::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                    } else {
                        $nilaisikap[$key] = FALSE;
                    }
                } else {
                    $nilaisikap[$key] = FALSE;
                }
            }
        }

        $view = view('penilaian.getsiswanilaisikap', compact('siswa', 'nilaisikap', 'countrapor', 'validasi'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function simpannilaipts(Request $request)
    {
        $employee_id = auth()->user()->pegawai->id;
        $semester_id = session('semester_aktif');
        $subject_id = $request->mapel_id;
        $class_id = $request->class_id;
        $siswa_id = $request->siswa_id;
        $sikap = $request->sikap;
        $iserror = FALSE;

        foreach ($siswa_id as $key => $siswas_id) {
            $countrapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->count();
            if ($countrapor > 0) {
                $nilairapor = NilaiRapor::where([['student_id', $siswas_id], ['semester_id', $semester_id], ['class_id', $class_id]])->first();

                $siswas = Siswa::where('id', $siswas_id)->first();
                $kelas = Kelas::where('id', $siswas->class_id)->first();
                $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                $namawali = $wali->name;
                $nilairapor->hr_name = $namawali;

                $countsikap = NilaiSikapPts::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->count();
                if ($nilairapor->save() && $countsikap == 0) {
                    $nilaisikap = NilaiSikapPts::create([
                        'score_id' => $nilairapor->id,
                        'subject_id' => $subject_id,
                        'predicate' => $sikap[$key]
                    ]);
                    if ($nilaisikap->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                } else {
                    $nilaisikap = NilaiSikapPts::where([['score_id', $nilairapor->id], ['subject_id', $subject_id]])->first();
                    $nilaisikap->predicate = $sikap[$key];
                    if ($nilaisikap->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            } else {
                $siswas = Siswa::where('id', $siswas_id)->first();
                $kelas = Kelas::where('id', $siswas->class_id)->first();
                $wali = Pegawai::where('id', $kelas->teacher_id)->first();
                $namawali = $wali->name;
                $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
                $namakepsek = $kepsek->name;
                $nilairapor = NilaiRapor::create([
                    'student_id' => $siswas_id,
                    'semester_id' => $semester_id,
                    'class_id' => $siswas->class_id,
                    'report_status_id' => 0,
                    'acc_id' => 0,
                    'unit_id' => $siswas->unit_id,
                    'hr_name' => $namawali,
                    'hm_name' => $namakepsek
                ]);
                if ($nilairapor->save()) {
                    $nilaisikap = NilaiSikapPts::create([
                        'score_id' => $nilairapor->id,
                        'subject_id' => $subject_id,
                        'predicate' => $sikap[$key]
                    ]);
                    if ($nilaisikap->save()) {
                        $iserror = FALSE;
                    } else {
                        $iserror = TRUE;
                        break;
                    }
                }
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaianmapel/nilaisikap')->with(['sukses' => 'Data berhasil disimpan']);
        } else {
            return redirect('/kependidikan/penilaianmapel/nilaisikap')->with(['error' => 'Data gagal disimpan']);
        }
    }
}

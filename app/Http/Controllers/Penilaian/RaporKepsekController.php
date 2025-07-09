<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\LoginUser;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\Kehadiran;
use App\Models\Penilaian\NilaiPengetahuanDetail;
use Illuminate\Http\Request;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PasTK;
use App\Models\Penilaian\PtsTK;
use App\Models\Penilaian\RaporPas;
use App\Models\Penilaian\RaporPts;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\SertifIklas;
use App\Models\Penilaian\TanggalRapor;
use App\Models\Penilaian\TilawahType;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

use Session;

class RaporKepsekController extends Controller
{
    public function pts()
    {
        $semesteraktif = Semester::where('is_active', 1)->first();
        $semester = Semester::all();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();
        return view('penilaian.ptskepsek', compact('level', 'semester', 'semesteraktif'));
    }

    public function pas()
    {
        $semesteraktif = Semester::where('is_active', 1)->first();
        $semester = Semester::all();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();
        return view('penilaian.paskepsek', compact('level', 'semester', 'semesteraktif'));
    }

    public function getkelas(Request $request)
    {
        $semester = Semester::where('id',$request->semester_id)->first();
        $kelas = Kelas::where(['academic_year_id'=> $semester->academic_year_id,'level_id' => $request->level_id])->get();
        $view = view('penilaian.getlevel', compact('kelas', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswapts(Request $request)
    {
        $siswa = NilaiRapor::where([['semester_id', $request->semester_id], ['class_id', $request->class_id]])->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();
        $semester = Semester::where('id', $request->semester_id)->first();
        $view = view('penilaian.getsiswavalidasipts', compact('siswa', 'semester'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function validasipts(Request $request)
    {
        $class_id = $request->class_id;
        $raporkelas = NilaiRapor::where([['class_id', $class_id], ['semester_id', session('semester_aktif')]])->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();
        foreach ($raporkelas as $rapor) {
            if ($rapor->unit_id == 1) {
                $pts = PtsTK::where('score_id', $rapor->id)->first();
            } else {
                $pts = RaporPts::where('score_id', $rapor->id)->first();
            }
            $kehadiran = Kehadiran::where('score_id', $rapor->id)->first();
            $pts->absent = $kehadiran->absent;
            $pts->sick = $kehadiran->sick;
            $pts->leave = $kehadiran->leave;
            $rapor->report_status_pts_id = 1;
            $rapor->acc_id = auth()->user()->pegawai->id;
            if ($pts->save() && $rapor->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
                break;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function validasisiswapts($id)
    {
        $siswa = Siswa::where('id', $id)->first();
        $class_id = $siswa->class_id;

        $rapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $class_id], ['semester_id', session('semester_aktif')]])->first();
        $rapor->report_status_pts_id = 1;
        $rapor->acc_id = auth()->user()->pegawai->id;
        if ($rapor->unit_id == 1) {
            $pts = PtsTK::where('score_id', $rapor->id)->first();
        } else {
            $pts = RaporPts::where('score_id', $rapor->id)->first();
        }
        $kehadiran = Kehadiran::where('score_id', $rapor->id)->first();
        //return $rapor.' - '.$pts;
        $pts->absent = $kehadiran->absent;
        $pts->sick = $kehadiran->sick;
        $pts->leave = $kehadiran->leave;

        if ($pts->save() && $rapor->save()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function validasipas(Request $request)
    {
        $class_id = $request->class_id;

        $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
        $namakepsek = $kepsek->name;

        $smt_aktif = Semester::where('id', session('semester_aktif'))->first();

        $raporkelas = NilaiRapor::where([['class_id', $class_id], ['semester_id', session('semester_aktif')]])->with(['siswa' => function ($q){$q->select('id','student_id','unit_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name')->values();

        foreach ($raporkelas as $rapor) {
            $rapor->report_status_id = 1;
            $rapor->acc_id = auth()->user()->pegawai->id;

            if($rapor->pas && $rapor->pas->conclusion == 'lulus'){
                $sertifIklas = SertifIklas::where(['student_id' => $rapor->siswa->id,'academic_year_id' => $smt_aktif->academic_year_id,'unit_id' => $rapor->siswa->unit_id]);

                if($sertifIklas->count() < 1){
                    SertifIklas::create([
                        'student_id' => $rapor->siswa->id,
                        'academic_year_id' => $smt_aktif->academic_year_id,
                        'unit_id' => $rapor->siswa->unit_id,
                        'hm_name' => $namakepsek
                    ]);
                }
                else{
                    $sertifIklas = $sertifIklas->first();
                    $sertifIklas->academic_year_id = $smt_aktif->academic_year_id;
                    $sertifIklas->hm_name = $namakepsek;
                    $sertifIklas->save();
                }
            }

            if ($rapor->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
                break;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function validasisiswapas($id)
    {
        $siswa = Siswa::where('id', $id)->first();
        $class_id = $siswa->class_id;

        $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
        $namakepsek = $kepsek->name;

        $smt_aktif = Semester::where('id', session('semester_aktif'))->first();

        $rapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $class_id], ['semester_id', session('semester_aktif')]])->first();

        $rapor->report_status_id = 1;
        $rapor->acc_id = auth()->user()->pegawai->id;

        if($rapor->pas && $rapor->pas->conclusion == 'lulus'){
            $sertifIklas = SertifIklas::where(['student_id' => $siswa->id,'academic_year_id' => $smt_aktif->academic_year_id,'unit_id' => $siswa->unit_id]);

            if($sertifIklas->count() < 1){
                SertifIklas::create([
                    'student_id' => $siswa->id,
                    'academic_year_id' => $smt_aktif->academic_year_id,
                    'unit_id' => $siswa->unit_id,
                    'hm_name' => $namakepsek
                ]);
            }
            else{
                $sertifIklas = $sertifIklas->first();
                $sertifIklas->academic_year_id = $smt_aktif->academic_year_id;
                $sertifIklas->hm_name = $namakepsek;
                $sertifIklas->save();
            }
        }

        if ($rapor->save()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function getsiswapas(Request $request)
    {
        $siswa = NilaiRapor::where([['semester_id', $request->semester_id], ['class_id', $request->class_id]])->with(['siswa' => function ($q){$q->select('id','student_id','level_id','class_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();
        $semester = Semester::where('id', $request->semester_id)->first();
        $view = view('penilaian.getsiswavalidasipas', compact('siswa', 'semester'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    /**
     * Print cover the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cover(Request $request, $tahun, $semester, $kelas, $id)
    {
        $role = $request->user()->role->name;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        $history = $id ? $kelas->riwayat()->select('student_id','unit_id','class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first() : null;
                        if($history){
                            $siswa = $history->siswa;
                            $unit = $history->unit;

                            $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();

                            $riwayatKelas = $history->kelas;

                            if($rapor){
                                return view('penilaian.pas_cover', compact('siswa', 'unit', 'semester','riwayatKelas'));
                            }
                            else{
                                Session::flash('danger', 'Nilai rapor Ananda '.$siswa->student_name.' belum divalidasi');
                                return redirect()->route('paskepsek');
                            }
                        }
                        else{
                            //return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                            return redirect()->route('paskepsek');
                        }
                    }
                    else{
                        //return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                        return redirect()->route('paskepsek');
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('paskepsek');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('kependidikan.kelas');
            }
        }
    }

    public function lihatnilaipas(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $tilawah = TilawahType::whereNotNull('tilawah_ep')->get();
                $targetTahfidz = $unit->targetTahfidz()->where(['level_id' => $request->level_id, 'semester_id' => $semester->id])->pluck('target');
                $hafalan = HafalanType::whereIn('mem_type',['hadits','doa'])->get();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $nilairapor = NilaiRapor::where([['student_id', $id], ['semester_id', $request->semester]])->first();

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if(isset($request->major_id)){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $request->major_id)->get();
                    $kelompok_master = $kelompok_umum->take(2);
                    $kelompok_lain = $kelompok_umum->skip(2);
                    $kelompok = $kelompok_master->concat($kelompok_peminatan)->concat($kelompok_lain);
                }
                else $kelompok = $kelompok_umum;
                $pas_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 2]])->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                // Baru
                $kelas = $rapor->kelas;
                $competencies = $descs = $nilai = $capaian = $mergedRows = $iklas = null;
                if($semester->tahunAjaran->academic_year_start >= 2022){
                    // IKLaS
                    $competencies = $unit->kompetensiKategoriIklas()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($competencies && count($competencies) > 0){
                        $catActive = $parentCompetence = null;
                        foreach($competencies as $c){
                            $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                            $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;

                            $deskripsiKompetensi = DeskripsiIklas::where([
                                'class_id' => $kelas->id,
                                'iklas_curriculum_id' => $c->id,
                            ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'unit_id' => $unit->id,
                                ]);
                            })->first();

                            $mergedRows['iklas'][$c->competence_id] = 1;
                            if($deskripsiKompetensi){
                                $descs['iklas'][$c->competence_id] = $deskripsiKompetensi;
                                if($deskripsiKompetensi->is_merged == 0) $parentCompetence = $c->competence_id;
                                if($catActive == $c->category_id && $deskripsiKompetensi->is_merged == 1){
                                    $mergedRows['iklas'][$parentCompetence]++;
                                }
                            }

                            if($catActive != $c->category_id) $iklas['rows'][$c->category_id] = 0;
                            $indicators = IndikatorKurikulumIklas::where([
                                'level_id' => $kelas->level_id,
                                'iklas_curriculum_id' => $c->id,
                            ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'unit_id' => $unit->id,
                                ]);
                            })->get();
                            $iklas['indikator'][$c->id] = null;
                            if($indicators && count($indicators) > 0){
                                $iklas['indikator'][$c->id] = $indicators;
                                $iklas['rows'][$c->category_id] += count($indicators);
                            }
                            else{
                                $iklas['rows'][$c->category_id]++;
                            }

                            if($catActive != $c->category_id) $catActive = $c->category_id;
                        }
                    }
                }
                else{
                    $iklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();
                }

                // Khataman
                $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                    if($capaian['khataman']['type']->type_id == 1){
                        $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                    }
                    elseif($capaian['khataman']['type']->type_id == 2){
                        $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                    }
                }
                $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                foreach($kategoriList['khataman'] as $kategori){
                    $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                    })->first();
                    $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                }

                // Hafalan Qur'an
                $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                    $q->where('rpd_type','Hafalan');
                })->first();
                $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                // Hafalan Hadits & Doa
                $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                    foreach($kategoriList['hafalan'] as $kategori){
                        $kategori = $kategori->mem_type;
                        $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('mem_type',ucwords($kategori));
                        })->get();
                        $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                        })->first();
                        $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                    }
                }
                
                if($semester->tahunAjaran->academic_year_start < 2021){
                    $template = 'old.pas_laporan_lt_2021';
                }elseif($semester->tahunAjaran->academic_year_start < 2022){
                    $template = 'old.pas_laporan_lt_2022';
                }else{
                    $template = 'pas_laporan';
                }
                
                return view('penilaian.'.$template, compact('siswa', 'unit', 'semester', 'iklas', 'tilawah', 'targetTahfidz', 'hafalan', 'rapor', 'nilairapor', 'kelompok', 'pas_date', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pas');
    }

    public function lihatnilaipastk(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $aspek = AspekPerkembangan::aktif()->get();

                $pas_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 2]])->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                // Baru
                $kelas = $rapor->kelas;
                $descs = $nilai = $capaian = $mergedRows = $iklas = null;
                // IKLaS
                $competencies = $unit->kompetensiKategoriIklas()->where([
                    'semester_id' => $semester->id
                ])->orderBy('sort_order')->get();

                if($competencies && count($competencies) > 0){
                    $catActive = $parentCompetence = null;
                    foreach($competencies as $c){
                        $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                        $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;

                        $deskripsiKompetensi = DeskripsiIklas::where([
                            'class_id' => $kelas->id,
                            'iklas_curriculum_id' => $c->id,
                        ])->whereHas('kurikulum',function($q)use($semester,$unit){
                            $q->where([
                                'semester_id' => $semester->id,
                                'unit_id' => $unit->id,
                            ]);
                        })->first();

                        $mergedRows['iklas'][$c->competence_id] = 1;
                        if($deskripsiKompetensi){
                            $descs['iklas'][$c->competence_id] = $deskripsiKompetensi;
                            if($deskripsiKompetensi->is_merged == 0) $parentCompetence = $c->competence_id;
                            if($catActive == $c->category_id && $deskripsiKompetensi->is_merged == 1){
                                $mergedRows['iklas'][$parentCompetence]++;
                            }
                        }

                        if($catActive != $c->category_id) $iklas['rows'][$c->category_id] = 0;
                        $indicators = IndikatorKurikulumIklas::where([
                            'level_id' => $kelas->level_id,
                            'iklas_curriculum_id' => $c->id,
                        ])->whereHas('kurikulum',function($q)use($semester,$unit){
                            $q->where([
                                'semester_id' => $semester->id,
                                'unit_id' => $unit->id,
                            ]);
                        })->get();
                        $iklas['indikator'][$c->id] = null;
                        if($indicators && count($indicators) > 0){
                            $iklas['indikator'][$c->id] = $indicators;
                            $iklas['rows'][$c->category_id] += count($indicators);
                        }
                        else{
                            $iklas['rows'][$c->category_id]++;
                        }

                        if($catActive != $c->category_id) $catActive = $c->category_id;
                    }
                }

                // Khataman
                $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                    if($capaian['khataman']['type']->type_id == 1){
                        $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                    }
                    elseif($capaian['khataman']['type']->type_id == 2){
                        $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                    }
                }
                $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                foreach($kategoriList['khataman'] as $kategori){
                    $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                    })->first();
                    $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                }

                // Hafalan Qur'an
                $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                    $q->where('rpd_type','Hafalan');
                })->first();
                $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                // Hafalan Hadits & Doa
                $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                    foreach($kategoriList['hafalan'] as $kategori){
                        $kategori = $kategori->mem_type;
                        $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('mem_type',ucwords($kategori));
                        })->get();
                        $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                        })->first();
                        $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                    }
                }
                
                if($semester->tahunAjaran->academic_year_start < 2022){
                    $template = 'old.pas_laporan_tk_lt_2022';
                }else{
                    $template = 'pas_laporan_tk';
                }

                return view('penilaian.'.$template, compact('pas_date', 'siswa', 'unit', 'semester', 'aspek', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pas');
    }

    /**
     * Print the last page of specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function akhir(Request $request, $tahun, $semester, $kelas, $id)
    {
        $role = $request->user()->role->name;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
                if($kelas){            
                    $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        $history = $id ? $kelas->riwayat()->select('student_id','unit_id','class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first() : null;
                        if($history){
                            $siswa = $history->siswa;
                            $unit = $history->unit;

                            $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();

                            if($rapor){
                                return view('penilaian.pas_akhir', compact('siswa', 'unit'));
                            }
                            else{
                                Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum divalidasi');
                                return redirect()->route('paskepsek');
                            }
                        }
                        else{
                            //return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                            return redirect()->route('paskepsek');
                        }
                    }
                    else{
                        //return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                        return redirect()->route('paskepsek');
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('paskepsek');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('kependidikan.kelas');
            }
        }
    }

    public function lihatnilaipts(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $subject_ids = $rapor->pengetahuan()->pluck('id');
                $nilai_harian = NilaiPengetahuanDetail::whereIn('score_knowledge_id', $subject_ids)->whereNotNull('score')->get();

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if(isset($request->major_id)){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $request->major_id)->get();
                    $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                }
                else $kelompok = $kelompok_umum;
                $pts_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 1]])->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;

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

                // Baru
                $nilai = $descs = null;
                // IKLaS
                $competencies = $unit->kompetensiKategoriIklas()->where([
                    'semester_id' => $semester->id
                ])->orderBy('sort_order')->get();

                if($competencies && count($competencies) > 0){
                    foreach($competencies as $c){
                        $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                        $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;
                    }
                }

                // Khataman
                $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                    if($capaian['khataman']['type']->type_id == 1){
                        $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                    }
                    elseif($capaian['khataman']['type']->type_id == 2){
                        $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                    }
                }
                $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                foreach($kategoriList['khataman'] as $kategori){
                    $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                    })->first();
                    $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                }

                // Hafalan Qur'an
                $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                    $q->where('rpd_type','Hafalan');
                })->first();
                $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                // Hafalan Hadits & Doa
                $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                    foreach($kategoriList['hafalan'] as $kategori){
                        $kategori = $kategori->mem_type;
                        $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('mem_type',ucwords($kategori));
                        })->get();
                        $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                        })->first();
                        $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                    }
                }

                return view('penilaian.pts_lihatnilai', compact('siswa', 'unit', 'semester', 'rapor', 'nilai_harian', 'kelompok', 'total_rows', 'pts_date', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pts');
    }

    public function lihatnilaiptstk(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $pts_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 1]])->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;
                $aspek = AspekPerkembangan::aktif()->get();

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                return view('penilaian.pts_laporan_tk', compact('pts_date', 'siswa', 'unit', 'semester', 'rapor', 'aspek', 'digital'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pts');
    }

    public function naikkelas(Request $request)
    {
        $class_id = $request->class_id;
        $siswa = Siswa::where([['class_id', $class_id]])->get();
        foreach ($siswa as $siswas) {
            $nilairapor = NilaiRapor::where([['class_id', $class_id], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')]])->first();
            if ($request->user()->pegawai->unit->name == 'TK'){
                if ($nilairapor->pas_tk->conclusion == "naik") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = $siswas->level_id + 1;
                } elseif ($nilairapor->pas_tk->conclusion == "lulus") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = 0;
                    $siswas->is_lulus = 1;
                }
                else {
                    $siswas->class_id = $class_id;
                }
            }
            else {
                if ($nilairapor->pas->conclusion == "naik") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = $siswas->level_id + 1;
                } elseif ($nilairapor->pas->conclusion == "lulus") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = 0;
                    $siswas->is_lulus = 1;
                }
                else {
                    $siswas->class_id = $class_id;
                }
            }
            if ($siswas->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }
        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['sukses' => 'Data siswa berhasil diperbarui']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['error' => 'Data siswa gagal diperbarui']);
        }
    }


    public function aspekperkembangan()
    {
        $aspek = AspekPerkembangan::where('is_deleted', 0)->orderBy('dev_aspect', 'ASC')->get();

        if ($aspek->isEmpty()) {
            $aspek = FALSE;
        }
        return view('penilaian.aspekperkembangan', compact('aspek'));
    }

    public function tambahaspek(Request $request)
    {
        $request->validate([
            'aspek' => 'required',
        ]);

        $query = AspekPerkembangan::create([
            'dev_aspect' => $request->aspek,
            'is_deleted' => 0
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function hapusaspek(Request $request)
    {

        $query = AspekPerkembangan::where('id', $request->id)->first();
        $query->is_deleted = 1;
        if ($query->save()) {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['error' => 'Data gagal dihapus']);
        }
    }

    public function ubahaspek(Request $request)
    {
        $query = AspekPerkembangan::where('id', $request->id)->first();

        $query->dev_aspect = $request->aspek;

        if ($query->save()) {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['error' => 'Data gagal diubah']);
        }
    }

    public function tanggalrapor()
    {
        $unit = auth()->user()->pegawai->unit_id;
        $rapor = TanggalRapor::where([['semester_id', session('semester_aktif')], ['unit_id', $unit]])->get();
        $tgl_lts = NULL;
        $tgl_rapor = NULL;
        if ($rapor->isEmpty()) {
            $rapor = FALSE;
        } else {
            foreach ($rapor as $rapors) {
                if ($rapors->date_type == 1) {
                    $tgl_lts = $rapors->report_date;
                } elseif ($rapors->date_type == 2) {
                    $tgl_rapor = $rapors->report_date;
                }
            }
        }
        return view('penilaian.tanggalrapor', compact('tgl_lts', 'tgl_rapor'));
    }

    public function password()
    {
        return view('penilaian.passwordverif');
    }

    public function simpanpassword(Request $request)
    {
        $pw_verif = md5($request->new_pass);

        $passcheck = Hash::check($request->pw_akun, $request->user()->password);
        if ($passcheck) {
            $pegawai = Pegawai::where('id', auth()->user()->pegawai->id)->first();
            $pegawai->verification_password = $pw_verif;
            if ($pegawai->save()) {
                return redirect('/kependidikan/penilaiankepsek/passwordverifikasi')->with(['sukses' => 'Data berhasil disimpan']);
            } else {
                return redirect('/kependidikan/penilaiankepsek/passwordverifikasi')->with(['error' => 'Data gagal disimpan']);
            }
        } else {
            return redirect('/kependidikan/penilaiankepsek/passwordverifikasi')->with(['error' => 'Password Akun yang Anda masukkan tidak sesuai!']);
        }
    }

    public function simpantanggal(Request $request)
    {
        $unit = auth()->user()->pegawai->unit_id;
        $idkelas = Kelas::where('unit_id', $unit)->pluck('id');
        $semester_id = session('semester_aktif');
        $tgl_lts = $request->tanggal_lts;
        $tgl_rapor = $request->tanggal_rapor;

        $pts = TanggalRapor::where([
            'semester_id' => $semester_id,
            'unit_id' => $unit,
            'date_type' => 1
        ])->first();

        if(!$pts){
            $pts = new TanggalRapor();
            $pts->semester_id = $semester_id;
            $pts->unit_id = $unit;
            $pts->report_date = $tgl_lts;
            $pts->date_type = 1;
            $pts->save();
            $pts->fresh();
        }
        $pts->report_date = $tgl_lts;
        $pts->save();

        $rapor = TanggalRapor::where([
            'semester_id' => $semester_id,
            'unit_id' => $unit,
            'date_type' => 2
        ])->first();

        if(!$rapor){
            $rapor = new TanggalRapor();
            $rapor->semester_id = $semester_id;
            $rapor->unit_id = $unit;
            $rapor->report_date = $tgl_rapor;
            $rapor->date_type = 2;
            $rapor->save();
            $rapor->fresh();
        }
        $rapor->report_date = $tgl_rapor;
        $rapor->save();

        return redirect('/kependidikan/penilaiankepsek/tanggalrapor')->with(['sukses' => 'Data berhasil disimpan']);
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Level;
use App\Models\LoginUser;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\Kehadiran;
use App\Models\Penilaian\NilaiPengetahuanDetail;
use Illuminate\Http\Request;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PasTK;
use App\Models\Penilaian\PtsTK;
use App\Models\Penilaian\RaporPas;
use App\Models\Penilaian\RaporPts;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\SertifIklas;
use App\Models\Penilaian\TanggalRapor;
use App\Models\Penilaian\TilawahType;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

use Session;

class RaporKepsekController extends Controller
{
    public function pts()
    {
        $semesteraktif = Semester::where('is_active', 1)->first();
        $semester = Semester::all();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();
        return view('penilaian.ptskepsek', compact('level', 'semester', 'semesteraktif'));
    }

    public function pas()
    {
        $semesteraktif = Semester::where('is_active', 1)->first();
        $semester = Semester::all();
        $unit = auth()->user()->pegawai->unit->id;
        $level = Level::where('unit_id', $unit)->get();
        return view('penilaian.paskepsek', compact('level', 'semester', 'semesteraktif'));
    }

    public function getkelas(Request $request)
    {
        $semester = Semester::where('id',$request->semester_id)->first();
        $kelas = Kelas::where(['academic_year_id'=> $semester->academic_year_id,'level_id' => $request->level_id])->get();
        $view = view('penilaian.getlevel', compact('kelas', 'request'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function getsiswapts(Request $request)
    {
        $siswa = NilaiRapor::where([['semester_id', $request->semester_id], ['class_id', $request->class_id]])->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();
        $semester = Semester::where('id', $request->semester_id)->first();
        $view = view('penilaian.getsiswavalidasipts', compact('siswa', 'semester'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    public function validasipts(Request $request)
    {
        $class_id = $request->class_id;
        $raporkelas = NilaiRapor::where([['class_id', $class_id], ['semester_id', session('semester_aktif')]])->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();
        foreach ($raporkelas as $rapor) {
            if ($rapor->unit_id == 1) {
                $pts = PtsTK::where('score_id', $rapor->id)->first();
            } else {
                $pts = RaporPts::where('score_id', $rapor->id)->first();
            }
            $kehadiran = Kehadiran::where('score_id', $rapor->id)->first();
            $pts->absent = $kehadiran->absent;
            $pts->sick = $kehadiran->sick;
            $pts->leave = $kehadiran->leave;
            $rapor->report_status_pts_id = 1;
            $rapor->acc_id = auth()->user()->pegawai->id;
            if ($pts->save() && $rapor->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
                break;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function validasisiswapts($id)
    {
        $siswa = Siswa::where('id', $id)->first();
        $class_id = $siswa->class_id;

        $rapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $class_id], ['semester_id', session('semester_aktif')]])->first();
        $rapor->report_status_pts_id = 1;
        $rapor->acc_id = auth()->user()->pegawai->id;
        if ($rapor->unit_id == 1) {
            $pts = PtsTK::where('score_id', $rapor->id)->first();
        } else {
            $pts = RaporPts::where('score_id', $rapor->id)->first();
        }
        $kehadiran = Kehadiran::where('score_id', $rapor->id)->first();
        //return $rapor.' - '.$pts;
        $pts->absent = $kehadiran->absent;
        $pts->sick = $kehadiran->sick;
        $pts->leave = $kehadiran->leave;

        if ($pts->save() && $rapor->save()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pts')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function validasipas(Request $request)
    {
        $class_id = $request->class_id;

        $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
        $namakepsek = $kepsek->name;

        $smt_aktif = Semester::where('id', session('semester_aktif'))->first();

        $raporkelas = NilaiRapor::where([['class_id', $class_id], ['semester_id', session('semester_aktif')]])->with(['siswa' => function ($q){$q->select('id','student_id','unit_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name')->values();

        foreach ($raporkelas as $rapor) {
            $rapor->report_status_id = 1;
            $rapor->acc_id = auth()->user()->pegawai->id;

            if($rapor->pas && $rapor->pas->conclusion == 'lulus'){
                $sertifIklas = SertifIklas::where(['student_id' => $rapor->siswa->id,'academic_year_id' => $smt_aktif->academic_year_id,'unit_id' => $rapor->siswa->unit_id]);

                if($sertifIklas->count() < 1){
                    SertifIklas::create([
                        'student_id' => $rapor->siswa->id,
                        'academic_year_id' => $smt_aktif->academic_year_id,
                        'unit_id' => $rapor->siswa->unit_id,
                        'hm_name' => $namakepsek
                    ]);
                }
                else{
                    $sertifIklas = $sertifIklas->first();
                    $sertifIklas->academic_year_id = $smt_aktif->academic_year_id;
                    $sertifIklas->hm_name = $namakepsek;
                    $sertifIklas->save();
                }
            }

            if ($rapor->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
                break;
            }
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function validasisiswapas($id)
    {
        $siswa = Siswa::where('id', $id)->first();
        $class_id = $siswa->class_id;

        $kepsek = Pegawai::where([['position_id', 1], ['unit_id', auth()->user()->pegawai->unit_id]])->first();
        $namakepsek = $kepsek->name;

        $smt_aktif = Semester::where('id', session('semester_aktif'))->first();

        $rapor = NilaiRapor::where([['student_id', $siswa->id], ['class_id', $class_id], ['semester_id', session('semester_aktif')]])->first();

        $rapor->report_status_id = 1;
        $rapor->acc_id = auth()->user()->pegawai->id;

        if($rapor->pas && $rapor->pas->conclusion == 'lulus'){
            $sertifIklas = SertifIklas::where(['student_id' => $siswa->id,'academic_year_id' => $smt_aktif->academic_year_id,'unit_id' => $siswa->unit_id]);

            if($sertifIklas->count() < 1){
                SertifIklas::create([
                    'student_id' => $siswa->id,
                    'academic_year_id' => $smt_aktif->academic_year_id,
                    'unit_id' => $siswa->unit_id,
                    'hm_name' => $namakepsek
                ]);
            }
            else{
                $sertifIklas = $sertifIklas->first();
                $sertifIklas->academic_year_id = $smt_aktif->academic_year_id;
                $sertifIklas->hm_name = $namakepsek;
                $sertifIklas->save();
            }
        }

        if ($rapor->save()) {
            $iserror = FALSE;
        } else {
            $iserror = TRUE;
        }

        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['sukses' => 'Nilai berhasil divalidasi']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['error' => 'Nilai gagal divalidasi']);
        }
    }

    public function getsiswapas(Request $request)
    {
        $siswa = NilaiRapor::where([['semester_id', $request->semester_id], ['class_id', $request->class_id]])->with(['siswa' => function ($q){$q->select('id','student_id','level_id','class_id')->with('identitas:id,student_name');}])-> get()->sortBy('siswa.identitas.student_name')->values();
        $semester = Semester::where('id', $request->semester_id)->first();
        $view = view('penilaian.getsiswavalidasipas', compact('siswa', 'semester'))->render();
        return response()->json(array('success' => true, 'html' => $view));
    }

    /**
     * Print cover the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cover(Request $request, $tahun, $semester, $kelas, $id)
    {
        $role = $request->user()->role->name;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        $history = $id ? $kelas->riwayat()->select('student_id','unit_id','class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first() : null;
                        if($history){
                            $siswa = $history->siswa;
                            $unit = $history->unit;

                            $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();

                            $riwayatKelas = $history->kelas;

                            if($rapor){
                                return view('penilaian.pas_cover', compact('siswa', 'unit', 'semester','riwayatKelas'));
                            }
                            else{
                                Session::flash('danger', 'Nilai rapor Ananda '.$siswa->student_name.' belum divalidasi');
                                return redirect()->route('paskepsek');
                            }
                        }
                        else{
                            //return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                            return redirect()->route('paskepsek');
                        }
                    }
                    else{
                        //return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                        return redirect()->route('paskepsek');
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('paskepsek');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('kependidikan.kelas');
            }
        }
    }

    public function lihatnilaipas(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $tilawah = TilawahType::whereNotNull('tilawah_ep')->get();
                $targetTahfidz = $unit->targetTahfidz()->where(['level_id' => $request->level_id, 'semester_id' => $semester->id])->pluck('target');
                $hafalan = HafalanType::whereIn('mem_type',['hadits','doa'])->get();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $nilairapor = NilaiRapor::where([['student_id', $id], ['semester_id', $request->semester]])->first();

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if(isset($request->major_id)){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $request->major_id)->get();
                    $kelompok_master = $kelompok_umum->take(2);
                    $kelompok_lain = $kelompok_umum->skip(2);
                    $kelompok = $kelompok_master->concat($kelompok_peminatan)->concat($kelompok_lain);
                }
                else $kelompok = $kelompok_umum;
                $pas_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 2]])->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                // Baru
                $kelas = $rapor->kelas;
                $competencies = $descs = $nilai = $capaian = $mergedRows = $iklas = null;
                if($semester->tahunAjaran->academic_year_start >= 2022){
                    // IKLaS
                    $competencies = $unit->kompetensiKategoriIklas()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($competencies && count($competencies) > 0){
                        $catActive = $parentCompetence = null;
                        foreach($competencies as $c){
                            $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                            $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;

                            $deskripsiKompetensi = DeskripsiIklas::where([
                                'class_id' => $kelas->id,
                                'iklas_curriculum_id' => $c->id,
                            ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'unit_id' => $unit->id,
                                ]);
                            })->first();

                            $mergedRows['iklas'][$c->competence_id] = 1;
                            if($deskripsiKompetensi){
                                $descs['iklas'][$c->competence_id] = $deskripsiKompetensi;
                                if($deskripsiKompetensi->is_merged == 0) $parentCompetence = $c->competence_id;
                                if($catActive == $c->category_id && $deskripsiKompetensi->is_merged == 1){
                                    $mergedRows['iklas'][$parentCompetence]++;
                                }
                            }

                            if($catActive != $c->category_id) $iklas['rows'][$c->category_id] = 0;
                            $indicators = IndikatorKurikulumIklas::where([
                                'level_id' => $kelas->level_id,
                                'iklas_curriculum_id' => $c->id,
                            ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'unit_id' => $unit->id,
                                ]);
                            })->get();
                            $iklas['indikator'][$c->id] = null;
                            if($indicators && count($indicators) > 0){
                                $iklas['indikator'][$c->id] = $indicators;
                                $iklas['rows'][$c->category_id] += count($indicators);
                            }
                            else{
                                $iklas['rows'][$c->category_id]++;
                            }

                            if($catActive != $c->category_id) $catActive = $c->category_id;
                        }
                    }
                }
                else{
                    $iklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();
                }

                // Khataman
                $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                    if($capaian['khataman']['type']->type_id == 1){
                        $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                    }
                    elseif($capaian['khataman']['type']->type_id == 2){
                        $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                    }
                }
                $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                foreach($kategoriList['khataman'] as $kategori){
                    $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                    })->first();
                    $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                }

                // Hafalan Qur'an
                $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                    $q->where('rpd_type','Hafalan');
                })->first();
                $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                // Hafalan Hadits & Doa
                $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                    foreach($kategoriList['hafalan'] as $kategori){
                        $kategori = $kategori->mem_type;
                        $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('mem_type',ucwords($kategori));
                        })->get();
                        $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                        })->first();
                        $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                    }
                }
                
                if($semester->tahunAjaran->academic_year_start < 2021){
                    $template = 'old.pas_laporan_lt_2021';
                }elseif($semester->tahunAjaran->academic_year_start < 2022){
                    $template = 'old.pas_laporan_lt_2022';
                }else{
                    $template = 'pas_laporan';
                }
                
                return view('penilaian.'.$template, compact('siswa', 'unit', 'semester', 'iklas', 'tilawah', 'targetTahfidz', 'hafalan', 'rapor', 'nilairapor', 'kelompok', 'pas_date', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pas');
    }

    public function lihatnilaipastk(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $aspek = AspekPerkembangan::aktif()->get();

                $pas_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 2]])->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                // Baru
                $kelas = $rapor->kelas;
                $descs = $nilai = $capaian = $mergedRows = $iklas = null;
                // IKLaS
                $competencies = $unit->kompetensiKategoriIklas()->where([
                    'semester_id' => $semester->id
                ])->orderBy('sort_order')->get();

                if($competencies && count($competencies) > 0){
                    $catActive = $parentCompetence = null;
                    foreach($competencies as $c){
                        $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                        $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;

                        $deskripsiKompetensi = DeskripsiIklas::where([
                            'class_id' => $kelas->id,
                            'iklas_curriculum_id' => $c->id,
                        ])->whereHas('kurikulum',function($q)use($semester,$unit){
                            $q->where([
                                'semester_id' => $semester->id,
                                'unit_id' => $unit->id,
                            ]);
                        })->first();

                        $mergedRows['iklas'][$c->competence_id] = 1;
                        if($deskripsiKompetensi){
                            $descs['iklas'][$c->competence_id] = $deskripsiKompetensi;
                            if($deskripsiKompetensi->is_merged == 0) $parentCompetence = $c->competence_id;
                            if($catActive == $c->category_id && $deskripsiKompetensi->is_merged == 1){
                                $mergedRows['iklas'][$parentCompetence]++;
                            }
                        }

                        if($catActive != $c->category_id) $iklas['rows'][$c->category_id] = 0;
                        $indicators = IndikatorKurikulumIklas::where([
                            'level_id' => $kelas->level_id,
                            'iklas_curriculum_id' => $c->id,
                        ])->whereHas('kurikulum',function($q)use($semester,$unit){
                            $q->where([
                                'semester_id' => $semester->id,
                                'unit_id' => $unit->id,
                            ]);
                        })->get();
                        $iklas['indikator'][$c->id] = null;
                        if($indicators && count($indicators) > 0){
                            $iklas['indikator'][$c->id] = $indicators;
                            $iklas['rows'][$c->category_id] += count($indicators);
                        }
                        else{
                            $iklas['rows'][$c->category_id]++;
                        }

                        if($catActive != $c->category_id) $catActive = $c->category_id;
                    }
                }

                // Khataman
                $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                    if($capaian['khataman']['type']->type_id == 1){
                        $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                    }
                    elseif($capaian['khataman']['type']->type_id == 2){
                        $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                    }
                }
                $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                foreach($kategoriList['khataman'] as $kategori){
                    $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                    })->first();
                    $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                }

                // Hafalan Qur'an
                $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                    $q->where('rpd_type','Hafalan');
                })->first();
                $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                // Hafalan Hadits & Doa
                $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                    foreach($kategoriList['hafalan'] as $kategori){
                        $kategori = $kategori->mem_type;
                        $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('mem_type',ucwords($kategori));
                        })->get();
                        $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                        })->first();
                        $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                    }
                }
                
                if($semester->tahunAjaran->academic_year_start < 2022){
                    $template = 'old.pas_laporan_tk_lt_2022';
                }else{
                    $template = 'pas_laporan_tk';
                }

                return view('penilaian.'.$template, compact('pas_date', 'siswa', 'unit', 'semester', 'aspek', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pas');
    }

    /**
     * Print the last page of specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function akhir(Request $request, $tahun, $semester, $kelas, $id)
    {
        $role = $request->user()->role->name;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
                if($kelas){            
                    $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        $history = $id ? $kelas->riwayat()->select('student_id','unit_id','class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first() : null;
                        if($history){
                            $siswa = $history->siswa;
                            $unit = $history->unit;

                            $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();

                            if($rapor){
                                return view('penilaian.pas_akhir', compact('siswa', 'unit'));
                            }
                            else{
                                Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum divalidasi');
                                return redirect()->route('paskepsek');
                            }
                        }
                        else{
                            //return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                            return redirect()->route('paskepsek');
                        }
                    }
                    else{
                        //return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                        return redirect()->route('paskepsek');
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('paskepsek');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('kependidikan.kelas');
            }
        }
    }

    public function lihatnilaipts(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $subject_ids = $rapor->pengetahuan()->pluck('id');
                $nilai_harian = NilaiPengetahuanDetail::whereIn('score_knowledge_id', $subject_ids)->whereNotNull('score')->get();

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if(isset($request->major_id)){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $request->major_id)->get();
                    $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                }
                else $kelompok = $kelompok_umum;
                $pts_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 1]])->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;

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

                // Baru
                $nilai = $descs = null;
                // IKLaS
                $competencies = $unit->kompetensiKategoriIklas()->where([
                    'semester_id' => $semester->id
                ])->orderBy('sort_order')->get();

                if($competencies && count($competencies) > 0){
                    foreach($competencies as $c){
                        $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                        $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;
                    }
                }

                // Khataman
                $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                    if($capaian['khataman']['type']->type_id == 1){
                        $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                    }
                    elseif($capaian['khataman']['type']->type_id == 2){
                        $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                    }
                }
                $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                foreach($kategoriList['khataman'] as $kategori){
                    $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                        $q->where('rpd_type',ucwords($kategori).' Tilawah');
                    })->first();
                    $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                }

                // Hafalan Qur'an
                $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                    $q->where('rpd_type','Hafalan');
                })->first();
                $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                // Hafalan Hadits & Doa
                $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                    foreach($kategoriList['hafalan'] as $kategori){
                        $kategori = $kategori->mem_type;
                        $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('mem_type',ucwords($kategori));
                        })->get();
                        $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                            $q->where('rpd_type','Hafalan '.ucwords($kategori));
                        })->first();
                        $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                    }
                }

                return view('penilaian.pts_lihatnilai', compact('siswa', 'unit', 'semester', 'rapor', 'nilai_harian', 'kelompok', 'total_rows', 'pts_date', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pts');
    }

    public function lihatnilaiptstk(Request $request)
    {
        $id = $request->id;
        $siswa = $id ? Siswa::find($id) : null;
        if ($siswa) {
            $unit = Unit::find($siswa->unit->id);

            if ($unit) {
                $semester = Semester::where('id', $request->semester)->first();
                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $pts_date = TanggalRapor::where([['semester_id', $semester->id], ['unit_id', $unit->id], ['date_type', 1]])->first();
                $pts_date = $pts_date ? $pts_date->report_date : null;
                $aspek = AspekPerkembangan::aktif()->get();

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                return view('penilaian.pts_laporan_tk', compact('pts_date', 'siswa', 'unit', 'semester', 'rapor', 'aspek', 'digital'));
            }
        }

        return redirect('/kependidikan/penilaiankepsek/pts');
    }

    public function naikkelas(Request $request)
    {
        $class_id = $request->class_id;
        $siswa = Siswa::where([['class_id', $class_id]])->get();
        foreach ($siswa as $siswas) {
            $nilairapor = NilaiRapor::where([['class_id', $class_id], ['student_id', $siswas->id], ['semester_id', session('semester_aktif')]])->first();
            if ($request->user()->pegawai->unit->name == 'TK'){
                if ($nilairapor->pas_tk->conclusion == "naik") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = $siswas->level_id + 1;
                } elseif ($nilairapor->pas_tk->conclusion == "lulus") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = 0;
                    $siswas->is_lulus = 1;
                }
                else {
                    $siswas->class_id = $class_id;
                }
            }
            else {
                if ($nilairapor->pas->conclusion == "naik") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = $siswas->level_id + 1;
                } elseif ($nilairapor->pas->conclusion == "lulus") {
                    $siswas->class_id = NULL;
                    $siswas->level_id = 0;
                    $siswas->is_lulus = 1;
                }
                else {
                    $siswas->class_id = $class_id;
                }
            }
            if ($siswas->save()) {
                $iserror = FALSE;
            } else {
                $iserror = TRUE;
            }
        }
        if ($iserror == FALSE) {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['sukses' => 'Data siswa berhasil diperbarui']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/pas')->with(['error' => 'Data siswa gagal diperbarui']);
        }
    }


    public function aspekperkembangan()
    {
        $aspek = AspekPerkembangan::where('is_deleted', 0)->orderBy('dev_aspect', 'ASC')->get();

        if ($aspek->isEmpty()) {
            $aspek = FALSE;
        }
        return view('penilaian.aspekperkembangan', compact('aspek'));
    }

    public function tambahaspek(Request $request)
    {
        $request->validate([
            'aspek' => 'required',
        ]);

        $query = AspekPerkembangan::create([
            'dev_aspect' => $request->aspek,
            'is_deleted' => 0
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    public function hapusaspek(Request $request)
    {

        $query = AspekPerkembangan::where('id', $request->id)->first();
        $query->is_deleted = 1;
        if ($query->save()) {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['error' => 'Data gagal dihapus']);
        }
    }

    public function ubahaspek(Request $request)
    {
        $query = AspekPerkembangan::where('id', $request->id)->first();

        $query->dev_aspect = $request->aspek;

        if ($query->save()) {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaiankepsek/tk/aspekperkembangan')->with(['error' => 'Data gagal diubah']);
        }
    }

    public function tanggalrapor()
    {
        $unit = auth()->user()->pegawai->unit_id;
        $rapor = TanggalRapor::where([['semester_id', session('semester_aktif')], ['unit_id', $unit]])->get();
        $tgl_lts = NULL;
        $tgl_rapor = NULL;
        if ($rapor->isEmpty()) {
            $rapor = FALSE;
        } else {
            foreach ($rapor as $rapors) {
                if ($rapors->date_type == 1) {
                    $tgl_lts = $rapors->report_date;
                } elseif ($rapors->date_type == 2) {
                    $tgl_rapor = $rapors->report_date;
                }
            }
        }
        return view('penilaian.tanggalrapor', compact('tgl_lts', 'tgl_rapor'));
    }

    public function password()
    {
        return view('penilaian.passwordverif');
    }

    public function simpanpassword(Request $request)
    {
        $pw_verif = md5($request->new_pass);

        $passcheck = Hash::check($request->pw_akun, $request->user()->password);
        if ($passcheck) {
            $pegawai = Pegawai::where('id', auth()->user()->pegawai->id)->first();
            $pegawai->verification_password = $pw_verif;
            if ($pegawai->save()) {
                return redirect('/kependidikan/penilaiankepsek/passwordverifikasi')->with(['sukses' => 'Data berhasil disimpan']);
            } else {
                return redirect('/kependidikan/penilaiankepsek/passwordverifikasi')->with(['error' => 'Data gagal disimpan']);
            }
        } else {
            return redirect('/kependidikan/penilaiankepsek/passwordverifikasi')->with(['error' => 'Password Akun yang Anda masukkan tidak sesuai!']);
        }
    }

    public function simpantanggal(Request $request)
    {
        $unit = auth()->user()->pegawai->unit_id;
        $idkelas = Kelas::where('unit_id', $unit)->pluck('id');
        $semester_id = session('semester_aktif');
        $tgl_lts = $request->tanggal_lts;
        $tgl_rapor = $request->tanggal_rapor;

        $pts = TanggalRapor::where([
            'semester_id' => $semester_id,
            'unit_id' => $unit,
            'date_type' => 1
        ])->first();

        if(!$pts){
            $pts = new TanggalRapor();
            $pts->semester_id = $semester_id;
            $pts->unit_id = $unit;
            $pts->report_date = $tgl_lts;
            $pts->date_type = 1;
            $pts->save();
            $pts->fresh();
        }
        $pts->report_date = $tgl_lts;
        $pts->save();

        $rapor = TanggalRapor::where([
            'semester_id' => $semester_id,
            'unit_id' => $unit,
            'date_type' => 2
        ])->first();

        if(!$rapor){
            $rapor = new TanggalRapor();
            $rapor->semester_id = $semester_id;
            $rapor->unit_id = $unit;
            $rapor->report_date = $tgl_rapor;
            $rapor->date_type = 2;
            $rapor->save();
            $rapor->fresh();
        }
        $rapor->report_date = $tgl_rapor;
        $rapor->save();

        return redirect('/kependidikan/penilaiankepsek/tanggalrapor')->with(['sukses' => 'Data berhasil disimpan']);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

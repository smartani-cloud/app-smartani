<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\Semester;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RaporPas;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\TanggalRapor;
use App\Models\Penilaian\TilawahType;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use function PHPUnit\Framework\isEmpty;

class PasController extends Controller
{
    public function cetakpas(Request $request)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::aktif()->first();
        $kelas = $tahunsekarang->kelas()->where('teacher_id', $employee_id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::find($smt_aktif);
        $siswa = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->has('siswa')->with('siswa:id,student_id,unit_id')->get()->pluck('siswa')->unique()->sortBy(function($query){return $query->identitas->student_name;});
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

        return view('penilaian.walas.pas_index', compact('siswa', 'semester', 'kelas', 'nilairapor'));
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
                $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();

                if($rapor){
                    return view('penilaian.pas_cover', compact('siswa', 'unit', 'semester','riwayatKelas'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum divalidasi');
                }
            }
        }

        return redirect()->route('pas.cetak');
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
                // Components
                $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;

                $tilawah = TilawahType::whereNotNull('tilawah_ep')->get();
                $targetTahfidz = $unit->targetTahfidz()->where(['level_id' => $riwayatKelas->level_id, 'semester_id' => $semester->id])->pluck('target');
                $hafalan = HafalanType::all();

                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $pas_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pas()->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if($riwayatKelas->major_id){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $siswa->kelas->major_id)->get();
                    $kelompok_master = $kelompok_umum->take(2);
                    $kelompok_lain = $kelompok_umum->skip(2);
                    $kelompok = $kelompok_master->concat($kelompok_peminatan)->concat($kelompok_lain);
                }
                else $kelompok = $kelompok_umum;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    // Baru
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

                    return view('penilaian.pas_laporan', compact('pas_date', 'siswa', 'unit', 'semester', 'tilawah', 'targetTahfidz', 'hafalan',  'kelompok', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pas.cetak');
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

                $pas_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pas()->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    // Baru
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

                    return view('penilaian.pas_laporan_tk', compact('pas_date', 'siswa', 'unit', 'semester', 'rapor', 'aspek', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pas.cetak');
    }

    /**
     * Print the last page of specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function akhir(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;
            $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();
            if($rapor){
                return view('penilaian.pas_akhir', compact('siswa', 'unit'));
            }
            else{
                Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum divalidasi');
            }
        }

        return redirect()->route('pas.cetak');
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\Semester;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\RaporPas;
use App\Models\Penilaian\RefIklas;
use App\Models\Penilaian\TanggalRapor;
use App\Models\Penilaian\TilawahType;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Siswa\Siswa;
use App\Models\Unit;

use function PHPUnit\Framework\isEmpty;

class PasController extends Controller
{
    public function cetakpas(Request $request)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $tahunsekarang = TahunAjaran::aktif()->first();
        $kelas = $tahunsekarang->kelas()->where('teacher_id', $employee_id)->first();
        $class_id = $kelas->id;
        $smt_aktif = session('semester_aktif');
        $semester = Semester::find($smt_aktif);
        $siswa = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->has('siswa')->with('siswa:id,student_id,unit_id')->get()->pluck('siswa')->unique()->sortBy(function($query){return $query->identitas->student_name;});
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

        return view('penilaian.walas.pas_index', compact('siswa', 'semester', 'kelas', 'nilairapor'));
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
                $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();

                if($rapor){
                    return view('penilaian.pas_cover', compact('siswa', 'unit', 'semester','riwayatKelas'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum divalidasi');
                }
            }
        }

        return redirect()->route('pas.cetak');
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
                // Components
                $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;

                $tilawah = TilawahType::whereNotNull('tilawah_ep')->get();
                $targetTahfidz = $unit->targetTahfidz()->where(['level_id' => $riwayatKelas->level_id, 'semester_id' => $semester->id])->pluck('target');
                $hafalan = HafalanType::all();

                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                $pas_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pas()->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                if($riwayatKelas->major_id){
                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $siswa->kelas->major_id)->get();
                    $kelompok_master = $kelompok_umum->take(2);
                    $kelompok_lain = $kelompok_umum->skip(2);
                    $kelompok = $kelompok_master->concat($kelompok_peminatan)->concat($kelompok_lain);
                }
                else $kelompok = $kelompok_umum;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    // Baru
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

                    return view('penilaian.pas_laporan', compact('pas_date', 'siswa', 'unit', 'semester', 'tilawah', 'targetTahfidz', 'hafalan',  'kelompok', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pas.cetak');
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

                $pas_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pas()->first();
                $pas_date = $pas_date ? $pas_date->report_date : null;

                // Digital Signature
                $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                if($rapor){
                    // Baru
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

                    return view('penilaian.pas_laporan_tk', compact('pas_date', 'siswa', 'unit', 'semester', 'rapor', 'aspek', 'digital','competencies','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas'));
                }
                else{
                    Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum ada');
                }
            }
        }

        return redirect()->route('pas.cetak');
    }

    /**
     * Print the last page of specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function akhir(Request $request, $id = null)
    {
        $role = $request->user()->role->name;

        $employee_id = $request->user()->pegawai->id;
        $semester = Semester::aktif()->first();
        $kelas = $semester->tahunAjaran->kelas()->where('teacher_id', $employee_id)->first();
        $siswa = $id ? $kelas->riwayat()->select('student_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->has('siswa')->first()->siswa : null;
        if($siswa) {
            $riwayatKelas = $kelas->riwayat()->select('class_id')->where(['student_id'=>$id,'semester_id'=>$semester->id])->first()->kelas;
            $unit = $riwayatKelas ? $riwayatKelas->unit : null;
            $rapor = $siswa->nilaiRapor()->where(['semester_id' => $semester->id, 'report_status_id' => 1])->first();
            if($rapor){
                return view('penilaian.pas_akhir', compact('siswa', 'unit'));
            }
            else{
                Session::flash('danger', 'Nilai rapor Ananda '.$siswa->identitas->student_name.' belum divalidasi');
            }
        }

        return redirect()->route('pas.cetak');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\RefIklas;
use App\Models\Unit;

use Illuminate\Http\Request;

class IkuEdukasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null)
    {
        $role = $request->user()->role->name;

        $isWali = $request->user()->pegawai->kelas()->first();

        if($request->user()->pegawai->unit_id == 1 || (!in_array($role,['kepsek','wakasek','pembinayys','ketuayys','direktur','etl','etm']) && !$isWali)){
            return redirect()->route('kependidikan.index');
        }

        $kelasList = $mataPelajaran = null;
        
        $semesterList = Semester::all();

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas();
                if(in_array($role,['pembinayys','ketuayys','direktur','etl','etm'])){
                    $kelasList = $kelasList->where('unit_id','!=',1);
                }
                else{
                    $kelasList = $kelasList->where('unit_id',$request->user()->pegawai->unit_id);
                }
                $kelasList = $kelasList->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
                if($kelas){
                    if(in_array($role,['pembinayys','ketuayys','direktur','etl','etm'])){
                        $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
                    }
                    else{
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    }
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        $unit = $kelas->unit()->select('id','name')->first();

                        $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                        if($kelas->major_id){
                            $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                            $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                        }
                        else $kelompok = $kelompok_umum;

                        $mapelFiltered = MataPelajaran::select('id','subject_acronym')->whereIn('group_subject_id', $kelompok->pluck('id'));

                        if($unit->name == 'SD'){
                            $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                                $q->where('level_id',$kelas->level_id);
                            });
                        }

                        if($semester->is_active == 0){
                            $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                $q->where('semester_id',$semester->id);
                            });
                        }

                        $mapel = clone $mapelFiltered;
                        $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                        if($mapel->count() > 0){
                            $mataPelajaran = $mapel->get();
                        }

                        $mapelMulok = clone $mapelFiltered;
                        $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                        if($mapelMulok->count() > 0){
                            $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                        }
                    }
                    else{
                        return redirect()->route('penilaian.ikuEdukasi.kelas',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
                else{
                    if($role == 'guru'){
                        $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                        if($kelas){
                            return redirect()->route('penilaian.ikuEdukasi.kelas',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                        }
                        else return redirect()->route('kependidikan.kelas');
                    }
                }
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas();
            if(in_array($role,['pembinayys','ketuayys','direktur','etl','etm'])){
                $kelasList = $kelasList->where('unit_id','!=',1);
            }
            else{
                $kelasList = $kelasList->where('unit_id',$request->user()->pegawai->unit_id);
            }
            $kelasList = $kelasList->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('penilaian.ikuEdukasi.kelas',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('kependidikan.kelas');
            }
        }

        return view('penilaian.iku_edukasi_index', compact('semesterList', 'semester', 'kelasList', 'kelas', 'mataPelajaran'));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chart(Request $request, $ledger = 'rapor',  $unit = null, $tahun = null, $semester = null)
    {
        $role = $request->user()->role->name;

        $ledgerList = collect([
            [
                'name' => 'Rapor',
                'link' => 'rapor'
            ],
            [
                'name' => 'IKLaS',
                'link' => 'iklas'
            ],
            [
                'name' => 'USP',
                'link' => 'usp'
            ],
        ]);
        
        $unitList = $semesterList = $kelasList = $mataPelajaran = null;
        
        if($ledger == 'iklas'){
            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 5) ? $request->score : null;
        }
        else{
            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 100) ? $request->score : null;
        }

        if($ledger && $ledgerList->where('link',$ledger)->count() > 0){
            if($role == 'kepsek'){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else $unitList = Unit::sekolah()->where('name','!=','TK')->get();

            if($unit){
                $unit = Unit::sekolah()->where('name','!=','TK')->where('name',$unit)->first();
                
                if($unit){
                    $semesterList = Semester::all();

                    if($tahun){
                        $tahun = str_replace("-","/",$tahun);
                        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                    }

                    if($tahun){
                        if($ledger == 'usp'){
                            $semester = Semester::where(['semester_id' => $tahun->academic_year.'-'.$semester, 'semester' => 'Genap'])->first();
                        }
                        else{
                            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                        }
                        if($semester){
                            if(isset($score)){
                                if(in_array($ledger,['rapor','usp'])){
                                    $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unit->id);
    
                                    if($ledger == 'usp'){
                                        $semesterList = $semesterList->where('semester','Genap')->all();
                                        $kelasList = $kelasList->whereHas('level',function($q){
                                            $q->whereIn('level',['6','9','12']);
                                        });
                                    }
    
                                    $kelasList = $kelasList->get();
    
                                    $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                                    if($unit->name == "SMA"){
                                        $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->has('jurusan')->get();
                                        $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                                    }
                                    else $kelompok = $kelompok_umum;
    
                                    $mapelFiltered = MataPelajaran::select(['id','subject_name','subject_acronym','group_subject_id'])->whereIn('group_subject_id', $kelompok->pluck('id'));
    
                                    if($semester->is_active == 0){
                                        $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                            $q->where('semester_id',$semester->id);
                                        });
                                    }
    
                                    $mapel = clone $mapelFiltered;
                                    $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');
    
                                    if($mapel->count() > 0){
                                        $mataPelajaran = $mapel->get();
                                    }
    
                                    $mapelMulok = clone $mapelFiltered;
                                    $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');
    
                                    if($mapelMulok->count() > 0){
                                        $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                                    }
    
                                    // Counting scores
                                    $classes = $datasets = null;
                                    foreach($kelasList as $k){
                                        foreach($mataPelajaran as $m){
                                            $totalKelas = $totalSiswa = 0;
                                            $checked = true;
                                            
                                            if($unit->name == 'SD'){
                                                if($m->mapelKelas()->where('level_id',$k->level_id)->count() > 0){
                                                    $checked = true;
                                                }
                                                else $checked = false;
                                            }
    
                                            if($unit->name == 'SMA'){
                                                if($m->kmps && ($m->kmps->major_id && ($m->kmps->major_id != $k->major_id))){
                                                    $checked = false;
                                                }
                                            }
    
                                            $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                            foreach($riyawatKelas as $r){
                                                $siswa = $r->siswa()->select('id')->first();
                                                if($ledger == 'rapor'){
                                                    $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                                                    
                                                    $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                                    $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                                    if($score_knowledge != '-'){
                                                        if($score_knowledge >= $score) $totalKelas++;
                                                    }
                                                    $totalSiswa++;
                                                }
                                                elseif($ledger == 'usp'){
                                                    $usp = $siswa->usp()->where('semester_id', $semester->id)->where('subject_id',$m->id)->first();
    
                                                    $score_usp = $usp ? number_format((float)$usp->score, 0, ',', '') : '-';
    
                                                    if($score_usp != '-'){
                                                        if($score_usp >= $score) $totalKelas++;
                                                    }
                                                    $totalSiswa++;
                                                }
                                            }
                                            $kelas = collect([
                                                [
                                                    'id' => $k->id,
                                                    'subject_id' => $m->id,
                                                    'total' => $totalKelas,
                                                    'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0,
                                                    'checked' => $checked
                                                ]
                                            ]);
                                            if($classes){
                                                $classes = $classes->concat($kelas);
                                            }
                                            else{
                                                $classes = $kelas;
                                            }
                                        }
                                    }
                                    
                                    $matapelajarans = null;
                                    foreach($kelompok as $kel){
                                        if($kel->matapelajarans()->count()){
                                            $mapel = $kel->matapelajarans()->select('id','subject_name','subject_acronym')->whereNull('is_mulok')->orderBy('subject_number')->get();
                                            $mulok = $kel->matapelajarans()->select('id','subject_name','subject_acronym')->mulok()->orderBy('subject_number');
                                            if($mulok->count() > 0){
                                                $mapel = $mapel->concat($mulok->get());
                                            }
                                            if(!$matapelajarans){
                                                $matapelajarans = collect($mapel);
                                            }
                                            else{
                                                $matapelajarans = $matapelajarans->concat(collect($mapel));
                                            }
                                        }
                                    }
                                    
                                    $num = 12;
                                    foreach($kelasList as $k){
                                        $dataArr = null;
                                        foreach($matapelajarans as $m){
                                            $percentage = $classes->where('id',$k->id)->where('subject_id',$m->id)->first();
                                            $percentage = $percentage ? $percentage['percentage'] : 0;
                                            if(!$dataArr)
                                                $dataArr = array();
                                            $dataArr[] = $percentage;
                                        }
                                        if(!$datasets){
                                            $datasets = collect([
                                                [   
                                                    'label' => $k->levelName,
                                                    'backgroundColor' => $this->getColor($num),
                                                    'data' => $dataArr
                                                ]
                                            ]);
                                        }
                                        else{
                                            $dataset = collect([
                                                [   
                                                    'label' => $k->levelName,
                                                    'backgroundColor' => $this->getColor($num),
                                                    'data' => $dataArr
                                                ]
                                            ]);
                                            $datasets = $datasets->concat($dataset);
                                        }
                                        $num++;
                                    }
                                    
                                    return view('penilaian.iku_edukasi_grafik_rapor', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score', 'kelasList', 'kelompok', 'mataPelajaran', 'matapelajarans', 'classes', 'datasets'));
                                }
                                elseif($ledger == 'iklas'){
                                    $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unit->id)->get();
    
                                    $refIklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();
    
                                    // Counting scores
                                    $classes = null;
                                    foreach($kelasList as $k){
                                        foreach($refIklas as $i){
                                            $totalKelas = $totalSiswa = 0;
                                            $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                            foreach($riyawatKelas as $r){
                                                $siswa = $r->siswa()->select('id')->first();
                                                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
    
                                                $raporIklas = $rapor ? $rapor->iklas : null;
    
                                                $score_iklas = $raporIklas ? $raporIklas->detail()->where('iklas_ref_id',$i->id)->first() : null;
    
                                                if($score_iklas){
                                                    if($score_iklas->predicate >= $score) $totalKelas++;
                                                }
                                                $totalSiswa++;
                                            }
                                            $kelas = collect([
                                                [
                                                    'id' => $k->id,
                                                    'iklas_ref_id' => $i->id,
                                                    'total' => $totalKelas,
                                                    'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0
                                                ]
                                            ]);
                                            if($classes){
                                                $classes = $classes->concat($kelas);
                                            }
                                            else{
                                                $classes = $kelas;
                                            }
                                        }
                                    }
                                    
                                    return view('penilaian.iku_edukasi_persen_iklas', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score', 'kelasList', 'refIklas', 'classes'));
                                }
                            }
                        }
                        else{
                            return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name]);
                        }
                    }
                    else{
                        $semester = Semester::aktif()->first();
                        return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name, 'tahun' => $semester->semesterLink]);
                    }
                }
                else{
                    if($role == 'kepsek'){
                        $unit = $request->user()->pegawai->unit;
                        return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name]);
                    }
                    else return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger]);
                }
            }
            else{
                if($role == 'kepsek'){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name]);
                }
            }
        }
        else{
            return redirect()->route('penilaian.ikuEdukasi.persen');
        }

        return view('penilaian.iku_edukasi_persen_index', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unit(Request $request, $ledger = 'rapor',  $unit = null, $tahun = null, $semester = null)
    {
        $role = $request->user()->role->name;

        $ledgerList = collect([
            [
                'name' => 'Rapor',
                'link' => 'rapor'
            ],
            [
                'name' => 'IKLaS',
                'link' => 'iklas'
            ],
            [
                'name' => 'USP',
                'link' => 'usp'
            ],
        ]);

        $unitList = $semesterList = $kelasList = $mataPelajaran = null;

        if($ledger && $ledgerList->where('link',$ledger)->count() > 0){
            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else $unitList = Unit::sekolah()->where('name','!=','TK')->get();

            if($unit){
                $unit = Unit::sekolah()->where('name','!=','TK')->where('name',$unit)->first();
                
                if($unit){
                    $semesterList = Semester::all();

                    if($tahun){
                        $tahun = str_replace("-","/",$tahun);
                        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                    }

                    if($tahun){
                        if($ledger == 'usp'){
                            $semester = Semester::where(['semester_id' => $tahun->academic_year.'-'.$semester, 'semester' => 'Genap'])->first();
                        }
                        else{
                            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                        }
                        if($semester){
                            if(in_array($ledger,['rapor','usp'])){
                                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unit->id);

                                if($ledger == 'usp'){
                                    $semesterList = $semesterList->where('semester','Genap')->all();
                                    $kelasList = $kelasList->whereHas('level',function($q){
                                        $q->whereIn('level',['6','9','12']);
                                    });
                                }

                                $kelasList = $kelasList->get();

                                $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                                if($unit->name == "SMA"){
                                    $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->has('jurusan')->get();
                                    $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                                }
                                else $kelompok = $kelompok_umum;

                                $mapelFiltered = MataPelajaran::select(['id','subject_name','group_subject_id'])->whereIn('group_subject_id', $kelompok->pluck('id'));

                                if($semester->is_active == 0){
                                    $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                        $q->where('semester_id',$semester->id);
                                    });
                                }

                                $mapel = clone $mapelFiltered;
                                $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                                if($mapel->count() > 0){
                                    $mataPelajaran = $mapel->get();
                                }

                                $mapelMulok = clone $mapelFiltered;
                                $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                                if($mapelMulok->count() > 0){
                                    $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                                }

                                // Counting scores
                                $classes = null;
                                foreach($kelasList as $k){
                                    foreach($mataPelajaran as $m){
                                        $totalKelas = $totalSiswa = 0;
                                        $checked = true;
                                        
                                        if($unit->name == 'SD'){
                                            if($m->mapelKelas()->where('level_id',$k->level_id)->count() > 0){
                                                $checked = true;
                                            }
                                            else $checked = false;
                                        }

                                        if($unit->name == 'SMA'){
                                            if($m->kmps && ($m->kmps->major_id && ($m->kmps->major_id != $k->major_id))){
                                                $checked = false;
                                            }
                                        }

                                        $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                        foreach($riyawatKelas as $r){
                                            $siswa = $r->siswa()->select('id')->first();
                                            if($ledger == 'rapor'){
                                                $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                                                
                                                $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                                $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                                if($score_knowledge != '-'){
                                                    $totalKelas += $score_knowledge;
                                                }
                                                $totalSiswa++;
                                            }
                                            elseif($ledger == 'usp'){
                                                $usp = $siswa->usp()->where('semester_id', $semester->id)->where('subject_id',$m->id)->first();

                                                $score_usp = $usp ? number_format((float)$usp->score, 0, ',', '') : '-';

                                                if($score_usp != '-'){
                                                    $totalKelas += $score_usp;
                                                }
                                                $totalSiswa++;
                                            }
                                        }
                                        $kelas = collect([
                                            [
                                                'id' => $k->id,
                                                'subject_id' => $m->id,
                                                'total' => $totalKelas,
                                                'avg' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)($totalKelas/$totalSiswa), 0, ',', '') : 0,
                                                'checked' => $checked
                                            ]
                                        ]);
                                        if($classes){
                                            $classes = $classes->concat($kelas);
                                        }
                                        else{
                                            $classes = $kelas;
                                        }
                                    }
                                }

                                return view('penilaian.iku_edukasi_unit_rapor', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'kelasList', 'kelompok', 'mataPelajaran', 'classes'));
                            }
                            elseif($ledger == 'iklas'){
                                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unit->id)->get();

                                $refIklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();

                                // Counting scores
                                $classes = null;
                                foreach($kelasList as $k){
                                    foreach($refIklas as $i){
                                        $totalKelas = $totalSiswa = 0;
                                        $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                        foreach($riyawatKelas as $r){
                                            $siswa = $r->siswa()->select('id')->first();
                                            $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                                            $raporIklas = $rapor ? $rapor->iklas : null;

                                            $scrore_iklas = $raporIklas ? $raporIklas->detail()->where('iklas_ref_id',$i->id)->first() : null;

                                            if($scrore_iklas){
                                                $totalKelas += $scrore_iklas->predicate;
                                            }
                                            $totalSiswa++;
                                        }
                                        $kelas = collect([
                                            [
                                                'id' => $k->id,
                                                'iklas_ref_id' => $i->id,
                                                'total' => $totalKelas,
                                                'avg' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)($totalKelas/$totalSiswa), 0, ',', '') : 0
                                            ]
                                        ]);
                                        if($classes){
                                            $classes = $classes->concat($kelas);
                                        }
                                        else{
                                            $classes = $kelas;
                                        }
                                    }
                                }
                                
                                return view('penilaian.iku_edukasi_unit_iklas', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'kelasList', 'refIklas', 'classes'));
                            }
                        }
                        else{
                            return redirect()->route('penilaian.ikuEdukasi.unit',['ledger' => $ledger, 'unit' => $unit->name]);
                        }
                    }
                    else{
                        $semester = Semester::aktif()->first();
                        return redirect()->route('penilaian.ikuEdukasi.unit',['ledger' => $ledger, 'unit' => $unit->name, 'tahun' => $semester->semesterLink]);
                    }
                }
                else{
                    if(in_array($role,['kepsek','wakasek'])){
                        $unit = $request->user()->pegawai->unit;
                        return redirect()->route('penilaian.ikuEdukasi.unit',['ledger' => $ledger, 'unit' => $unit->name]);
                    }
                    else return redirect()->route('penilaian.ikuEdukasi.unit',['ledger' => $ledger]);
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route('penilaian.ikuEdukasi.unit',['ledger' => $ledger, 'unit' => $unit->name]);
                }
            }
        }
        else{
            return redirect()->route('penilaian.ikuEdukasi.unit');
        }

        return view('penilaian.iku_edukasi_unit_index', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function persen(Request $request, $ledger = 'rapor', $unit = null, $tahun = null, $semester = null)
    {
        $role = $request->user()->role->name;

        $ledgerList = collect([
            [
                'name' => 'Rapor',
                'link' => 'rapor'
            ],
            [
                'name' => 'IKLaS',
                'link' => 'iklas'
            ],
            [
                'name' => 'USP',
                'link' => 'usp'
            ],
        ]);
        
        $unitList = $semesterList = $kelasList = $mataPelajaran = null;
        
        if($ledger == 'iklas'){
            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 5) ? $request->score : null;
        }
        else{
            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 100) ? $request->score : null;
        }

        if($ledger && $ledgerList->where('link',$ledger)->count() > 0){
            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else $unitList = Unit::sekolah()->where('name','!=','TK')->get();

            if($unit){
                $unit = Unit::sekolah()->where('name','!=','TK')->where('name',$unit)->first();
                
                if($unit){
                    $semesterList = Semester::all();
                    if($ledger == 'usp'){
                        $semesterList = Semester::where('semester','Genap')->get();
                    }

                    if($tahun){
                        $tahun = str_replace("-","/",$tahun);
                        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                    }

                    if($tahun){
                        if($ledger == 'usp'){
                            $semester = Semester::where(['semester_id' => $tahun->academic_year.'-'.$semester, 'semester' => 'Genap'])->first();
                        }
                        else{
                            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                        }
                        if($semester){
                            if(isset($score)){
                                ini_set('max_execution_time', 0);
                                if(in_array($ledger,['rapor','usp'])){
                                    $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unit->id);
    
                                    if($ledger == 'usp'){
                                        $kelasList = $kelasList->whereHas('level',function($q){
                                            $q->whereIn('level',['6','9','12']);
                                        });
                                    }
    
                                    $kelasList = $kelasList->get();
    
                                    $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                                    if($unit->name == "SMA"){
                                        $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->has('jurusan')->get();
                                        $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                                    }
                                    else $kelompok = $kelompok_umum;
    
                                    $mapelFiltered = MataPelajaran::select(['id','subject_name','group_subject_id'])->whereIn('group_subject_id', $kelompok->pluck('id'));
    
                                    if($semester->is_active == 0){
                                        $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                            $q->where('semester_id',$semester->id);
                                        });
                                    }
    
                                    $mapel = clone $mapelFiltered;
                                    $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');
    
                                    if($mapel->count() > 0){
                                        $mataPelajaran = $mapel->get();
                                    }
    
                                    $mapelMulok = clone $mapelFiltered;
                                    $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');
    
                                    if($mapelMulok->count() > 0){
                                        $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                                    }

                                    $classes = null;
                                    if($mataPelajaran){
                                        // Counting scores
                                        foreach($kelasList as $k){
                                            foreach($mataPelajaran as $m){
                                                $totalKelas = $totalSiswa = 0;
                                                $checked = true;
                                                
                                                if($unit->name == 'SD'){
                                                    if($m->mapelKelas()->where('level_id',$k->level_id)->count() > 0){
                                                        $checked = true;
                                                    }
                                                    else $checked = false;
                                                }
        
                                                if($unit->name == 'SMA'){
                                                    if($m->kmps && ($m->kmps->major_id && ($m->kmps->major_id != $k->major_id))){
                                                        $checked = false;
                                                    }
                                                }
        
                                                $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                                foreach($riyawatKelas as $r){
                                                    $siswa = $r->siswa()->select('id')->first();
                                                    if($ledger == 'rapor'){
                                                        $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                                                        
                                                        $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                                        $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                                        if($score_knowledge != '-'){
                                                            if($score_knowledge >= $score) $totalKelas++;
                                                        }
                                                        $totalSiswa++;
                                                    }
                                                    elseif($ledger == 'usp'){
                                                        $usp = $siswa->usp()->where('semester_id', $semester->id)->where('subject_id',$m->id)->first();
        
                                                        $score_usp = $usp ? number_format((float)$usp->score, 0, ',', '') : '-';
        
                                                        if($score_usp != '-'){
                                                            if($score_usp >= $score) $totalKelas++;
                                                        }
                                                        $totalSiswa++;
                                                    }
                                                }
                                                $kelas = collect([
                                                    [
                                                        'id' => $k->id,
                                                        'subject_id' => $m->id,
                                                        'total' => $totalKelas,
                                                        'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0,
                                                        'checked' => $checked
                                                    ]
                                                ]);
                                                if($classes){
                                                    $classes = $classes->concat($kelas);
                                                }
                                                else{
                                                    $classes = $kelas;
                                                }
                                            }
                                        }

                                    }
        
                                        return view('penilaian.iku_edukasi_persen_rapor', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score', 'kelasList', 'kelompok', 'mataPelajaran', 'classes'));
                                }
                                elseif($ledger == 'iklas'){
                                    $kelasList = $semester->tahunAjaran->kelas()->select('id','level_id','class_name_id')->where('unit_id',$unit->id)->get();
    
                                    $refIklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();
    
                                    // // Counting scores
                                    // $classes = null;
                                    // foreach($kelasList as $k){
                                    //     foreach($refIklas as $i){
                                    //         $totalKelas = $totalSiswa = 0;
                                    //         $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                    //         foreach($riyawatKelas as $r){
                                    //             $siswa = $r->siswa()->select('id')->first();
                                    //             $rapor = $siswa->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
    
                                    //             $raporIklas = $rapor ? $rapor->iklas : null;
    
                                    //             $score_iklas = $raporIklas ? $raporIklas->detail()->select('predicate')->where('iklas_ref_id',$i->id)->first() : null;
    
                                    //             if($score_iklas){
                                    //                 if($score_iklas->predicate >= $score) $totalKelas++;
                                    //             }
                                    //             $totalSiswa++;
                                    //         }
                                    //         $kelas = collect([
                                    //             [
                                    //                 'id' => $k->id,
                                    //                 'iklas_ref_id' => $i->id,
                                    //                 'total' => $totalKelas,
                                    //                 'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0
                                    //             ]
                                    //         ]);
                                    //         if($classes){
                                    //             $classes = $classes->concat($kelas);
                                    //         }
                                    //         else{
                                    //             $classes = $kelas;
                                    //         }
                                    //     }
                                    // }
                                    
                                    //return view('penilaian.iku_edukasi_persen_iklas', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score', 'kelasList', 'refIklas', 'classes'));
                                    return view('penilaian.iku_edukasi_persen_iklas', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score', 'kelasList', 'refIklas'));
                                }
                            }
                        }
                        else{
                            return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name]);
                        }
                    }
                    else{
                        $semester = Semester::aktif()->first();
                        if($ledger == 'usp'){
                            $semester = Semester::where(['academic_year_id' => $semester->academic_year_id, 'semester' => 'Genap'])->first();
                        }
                        return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name, 'tahun' => $semester->semesterLink]);
                    }
                }
                else{
                    if(in_array($role,['kepsek','wakasek'])){
                        $unit = $request->user()->pegawai->unit;
                        return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name]);
                    }
                    else return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger]);
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route('penilaian.ikuEdukasi.persen',['ledger' => $ledger, 'unit' => $unit->name]);
                }
            }
        }
        else{
            return redirect()->route('penilaian.ikuEdukasi.persen');
        }

        return view('penilaian.iku_edukasi_persen_index', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPersen(Request $request, $ledger, $unit, $tahun, $semester)
    {
        $role = $request->user()->role->name;
        
        $unitList = $semesterList = $kelasList = $mataPelajaran = null;
        
        if($ledger == 'iklas'){
            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 5) ? $request->score : null;
        }
        else{
            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 100) ? $request->score : null;
        }

        if(in_array($ledger,['rapor','iklas','usp'])){
            if(in_array($role,['kepsek','wakasek']))
                $unit = $request->user()->pegawai->unit;
            else
                $unit = Unit::select('id','name')->sekolah()->where('name','!=','TK')->where('name',$unit)->first();

            if($unit){
                $semesterList = Semester::select('semester')->get()->unique();

                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if($tahun){
                    if($ledger == 'usp'){
                        $semester = Semester::select('id','academic_year_id','is_active')->where(['semester_id' => $tahun->academic_year.'-'.$semester, 'semester' => 'Genap'])->first();
                    }
                    else{
                        $semester = Semester::select('id','academic_year_id','is_active')->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                    }
                }
                else $semester = Semester::select('id','academic_year_id','is_active')->aktif()->first();

                if(isset($score)){
                    ini_set('max_execution_time', 0);
                    if(in_array($ledger,['rapor','usp'])){
                        $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unit->id);

                        if($ledger == 'usp'){
                            $semesterList = $semesterList->where('semester','Genap')->all();
                            $kelasList = $kelasList->whereHas('level',function($q){
                                $q->whereIn('level',['6','9','12']);
                            });
                        }

                        $kelasList = $kelasList->get();

                        $kelompok_umum = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                        if($unit->name == "SMA"){
                            $kelompok_peminatan = $unit->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->has('jurusan')->get();
                            $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                        }
                        else $kelompok = $kelompok_umum;

                        $mapelFiltered = MataPelajaran::select(['id','subject_name','group_subject_id'])->whereIn('group_subject_id', $kelompok->pluck('id'));

                        if($semester->is_active == 0){
                            $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                $q->where('semester_id',$semester->id);
                            });
                        }

                        $mapel = clone $mapelFiltered;
                        $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                        if($mapel->count() > 0){
                            $mataPelajaran = $mapel->get();
                        }

                        $mapelMulok = clone $mapelFiltered;
                        $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                        if($mapelMulok->count() > 0){
                            $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                        }

                        // Counting scores
                        $classes = null;
                        foreach($kelasList as $k){
                            foreach($mataPelajaran as $m){
                                $totalKelas = $totalSiswa = 0;
                                $checked = true;
                                
                                if($unit->name == 'SD'){
                                    if($m->mapelKelas()->where('level_id',$k->level_id)->count() > 0){
                                        $checked = true;
                                    }
                                    else $checked = false;
                                }

                                if($unit->name == 'SMA'){
                                    if($m->kmps && ($m->kmps->major_id && ($m->kmps->major_id != $k->major_id))){
                                        $checked = false;
                                    }
                                }

                                $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                foreach($riyawatKelas as $r){
                                    $siswa = $r->siswa()->select('id')->first();
                                    if($ledger == 'rapor'){
                                        $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();
                                        
                                        $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                        $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                        if($score_knowledge != '-'){
                                            if($score_knowledge >= $score) $totalKelas++;
                                        }
                                        $totalSiswa++;
                                    }
                                    elseif($ledger == 'usp'){
                                        $usp = $siswa->usp()->where('semester_id', $semester->id)->where('subject_id',$m->id)->first();

                                        $score_usp = $usp ? number_format((float)$usp->score, 0, ',', '') : '-';

                                        if($score_usp != '-'){
                                            if($score_usp >= $score) $totalKelas++;
                                        }
                                        $totalSiswa++;
                                    }
                                }
                                $kelas = collect([
                                    [
                                        'id' => $k->id,
                                        'subject_id' => $m->id,
                                        'total' => $totalKelas,
                                        'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0,
                                        'checked' => $checked
                                    ]
                                ]);
                                if($classes){
                                    $classes = $classes->concat($kelas);
                                }
                                else{
                                    $classes = $kelas;
                                }
                            }
                        }

                        return view('penilaian.iku_edukasi_persen_rapor', compact('ledgerList', 'ledger', 'unitList', 'unit', 'semesterList', 'semester', 'score', 'kelasList', 'kelompok', 'mataPelajaran', 'classes'));
                    }
                    elseif($ledger == 'iklas'){
                        //$kelasList = $semester->tahunAjaran->kelas()->select('id','level_id','class_name_id')->where('unit_id',$unit->id)->take(1)->get();
                        $k = $semester->tahunAjaran->kelas()->select('id','level_id','class_name_id')->where('unit_id',$unit->id)->where('id',$request->class)->first();

                        if($k){
                            $refIklas = RefIklas::select('id')->orderBy('iklas_cat','asc')->orderBy('iklas_no','asc')->get();

                            // Counting scores
                            $classes = null;
                            //foreach($kelasList->sortBy('levelName') as $k){
                                $totals = array();
                                foreach($refIklas as $i){
                                    $totalKelas = $totalSiswa = 0;
                                    $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                    foreach($riyawatKelas as $r){
                                        $siswa = $r->siswa()->select('id')->first();
                                        $rapor = $siswa->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();

                                        $raporIklas = $rapor ? $rapor->iklas : null;

                                        $score_iklas = $raporIklas ? $raporIklas->detail()->select('predicate')->where('iklas_ref_id',$i->id)->first() : null;

                                        if($score_iklas){
                                            if($score_iklas->predicate >= $score) $totalKelas++;
                                        }
                                    }
                                    $totalSiswa = count($riyawatKelas);
                                    $percentage = $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0;
                                    array_push($totals,$percentage);
                                }
                                $kelas = collect([
                                    [
                                        'id' => $k->id,
                                        'name' => $k->levelName,
                                        'percentages' => $totals
                                    ]
                                ]);
                                if($classes){
                                    $classes = $classes->concat($kelas);
                                }
                                else{
                                    $classes = $kelas;
                                }
                            //}
                            
                            return response()->json(['status' => 'success', 'data' => $classes]);
                        }
                        else return response()->json(['status' => 'error', 'message' => 'Class is not found']);
                    }
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek']))
                    return response()->json(['status' => 'error', 'message' => 'Unit is not found']);
                else
                    return response()->json(['status' => 'error', 'message' => 'Unit is not valid, please select another unit']);
            }
        }
        else return response()->json(['status' => 'error', 'message' => 'Ledger category is not valid, pleaseselect another category']);
    }
    
    /**
     * Get RGB colors.
     */
    function getColor($num) {
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return 'rgb('.
            hexdec(substr($hash, 0, 2)).','. // r
            hexdec(substr($hash, 2, 2)).','. // g
            hexdec(substr($hash, 4, 2)).')'; // b
    }
}

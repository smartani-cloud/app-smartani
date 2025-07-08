<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class DeskripsiIklasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi';
        $this->modul = $modul;
        $this->active = 'Deskripsi IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $siswa = null)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q){
            $q->whereHas('semester',function($q){
                $q->aktif();
            })->whereIn('curriculum_id',[1,2]);
        })->first();

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $kelasList = $riwayatKelas = $competencies = $data = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q){
                $q->where(function($q){
                    $q->where('is_active',1);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q){
                $q->where('is_active',1);
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas();
                if(!in_array($role,$managementRoles)){
                    $kelasList = $kelasList->where('unit_id',auth()->user()->pegawai->unit_id);
                    if($isWali){
                        $kelasList = $kelasList->where([
                            'academic_year_id' => $tahun->id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    }
                }
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                    ])->whereIn('curriculum_id',[1,2]);
                })->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
                    if(!in_array($role,$managementRoles)){
                        $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                        if($isWali){
                            $kelas = $kelas->where([
                                'academic_year_id' => $tahun->id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        }
                    }
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                        ])->whereIn('curriculum_id',[1,2]);
                    })->first();

                    if($kelas){
                        // Inti function
                        $unit = $kelas->unit()->select('id','name')->first();
                        
                        $competencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        if($competencies && count($competencies) > 0){
                            foreach($competencies as $c){
                                $deskripsiKompetensi = DeskripsiIklas::where([
                                    'class_id' => $kelas->id,
                                    'iklas_curriculum_id' => $c->id,
                                ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'unit_id' => $unit->id,
                                    ]);
                                })->first();
                                $data[$c->id] = $deskripsiKompetensi ? $deskripsiKompetensi : null;
                            }
                        }
                    }
                    else{
                        return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
            }
        }
        else{
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','riwayatKelas','siswa','competencies','data'));
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
        //
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $kelas)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q){
            $q->whereHas('semester',function($q){
                $q->aktif();
            })->whereIn('curriculum_id',[1,2]);
        })->first();

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->where(function($q){
                $q->where('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->where('is_active',1);
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->whereIn('curriculum_id',[1,2]);
            })->first();

            if($kelas){
                // Inti Function
                $unit = $kelas->unit()->select('id','name')->first();
                
                $competencies = $unit->kompetensiKategoriIklas()->where([
                    'semester_id' => $semester->id
                ])->orderBy('sort_order')->get();

                if($competencies && count($competencies) > 0){
                    $catActive = null;
                    $savedCount = 0;
                    foreach($competencies as $c){
                        $deskripsiKompetensi = DeskripsiIklas::where([
                            'class_id' => $kelas->id,
                            'iklas_curriculum_id' => $c->id,
                        ])->whereHas('kurikulum',function($q)use($semester,$unit){
                            $q->where([
                                'semester_id' => $semester->id,
                                'unit_id' => $unit->id,
                            ]);
                        })->first();                            
                        if(!$deskripsiKompetensi){
                            $deskripsiKompetensi = new DeskripsiIklas();
                            $deskripsiKompetensi->class_id = $kelas->id;
                            $deskripsiKompetensi->iklas_curriculum_id = $c->id;
                            $deskripsiKompetensi->employee_id = auth()->user()->pegawai->id;
                            $deskripsiKompetensi->desc = null;
                            $deskripsiKompetensi->save();
                            $deskripsiKompetensi->fresh();
                        }
                        if($deskripsiKompetensi){
                            $inputName = 'desc-'.$c->id;
                            if($catActive == $c->category_id){
                                $optionName = 'mergeOpt-'.$c->id;
                                if($request->{$optionName} == 'true'){
                                    $deskripsiKompetensi->is_merged = 1;
                                    $deskripsiKompetensi->desc = null;
                                }
                                else{
                                    $deskripsiKompetensi->is_merged = 0;
                                    $deskripsiKompetensi->desc = $request->{$inputName};
                                }
                            }
                            else{
                                $deskripsiKompetensi->is_merged = 0;
                                $deskripsiKompetensi->desc = $request->{$inputName};
                                $catActive = $c->category_id;
                            }
                            $deskripsiKompetensi->save();
                            $savedCount++;
                        }
                    }
                    if($savedCount == count($competencies)){
                        Session::flash('success', 'Semua perubahan deskripsi kompetensi IKLaS berhasil disimpan');
                    }
                    elseif($savedCount > 0 && ($savedCount < count($competencies))){
                        Session::flash('success', 'Beberapa perubahan deskripsi kompetensi IKLaS berhasil disimpan');
                    }
                    else{
                        Session::flash('danger', 'Tidak dapat menyimpan perubahan deskripsi IKLaS');
                    }
                }
                else{
                    Session::flash('danger', 'Tidak ada data kurikulum IKLaS yang ditemukan');
                }
                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
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

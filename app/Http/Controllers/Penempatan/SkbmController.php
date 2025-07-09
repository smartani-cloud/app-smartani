<?php

namespace App\Http\Controllers\Penempatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jataban;
use App\Models\Penempatan\KategoriJabatan;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Skbm\Skbm;
use App\Models\Skbm\SkbmArsip;
use App\Models\Skbm\SkbmDetail;
use App\Models\Unit;

class SkbmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        if($request->tahunpelajaran){
            $tahunajaran = str_replace("-","/",$request->tahunpelajaran);
            $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        }
        else{
            $aktif = TahunAjaran::where('is_active',1)->latest()->first();
        }
        $tahun = TahunAjaran::orderBy('created_at')->get();

        if(in_array($role,['kepsek','wakasek'])){
            $unit = $request->user()->pegawai->unit;
            $skbm = $unit->skbm()->orderBy('created_at','desc')->get();
        }
        else{
            $unit = Unit::sekolah()->orderBy('created_at')->get();
        }

        if(in_array($role,['kepsek','wakasek'])){
            $folder = $role;
            return view('kepegawaian.'.$folder.'.skbm_index', compact('aktif','tahun','skbm'));
        }
        else {
            return view('kepegawaian.read-only.skbm_index', compact('aktif','tahun','unit'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $tahunpelajaran, $unit)
    {
        $tahunajaran = str_replace("-","/",$tahunpelajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::sekolah()->where('name',$unit)->first();

        if($aktif && $unit){
            if($request->user()->role->name == 'kepsek') //harusnya kepsek
            {
                if($request->user()->pegawai->unit->is($unit))
                {
                    $tahunajaran_aktif = TahunAjaran::where('is_active',1)->latest()->first();
                    $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                    if($skbm || (!$skbm && $aktif->is($tahunajaran_aktif))){
                        $messages = [
                            'position.required' => 'Mohon pilih struktural/guru mapel',
                            'employee.required' => 'Mohon pilih pegawai',
                            'students.numeric' => 'Pastikan jumlah siswa per rombel hanya mengandung angka',
                            'teaching_load.numeric' => 'Pastikan beban jam mengajar hanya mengandung angka',
                            'teaching_decree_date.date' => 'Pastikan format tanggal valid'
                        ];

                        $this->validate($request, [
                            'employee' => 'required',
                            'position' => 'required',
                            'students' => 'numeric',
                            'teaching_load' => 'numeric',
                            'teaching_decree_date' => 'nullable|date'
                        ], $messages);

                        // $position = $unit->jabatan()->whereHas('kategori', function (Builder $query){
                        //     $query->where('name', 'Tenaga Pendidik');
                        // })->where('name','!=','Wali Kelas')->where('tref_position.id',$request->position)->first();
                        $position = $unit->jabatan()->whereHas('kategori', function (Builder $query){
                            $query->where('name', 'Tenaga Pendidik');
                        })->where('tref_position.id',$request->position)->first();
                        $subject = $request->subject ? $unit->mataPelajaran()->where('id', $request->subject)->first() : null;
                        $employee = $unit->pegawais()->whereHas('jabatans', function (Builder $query){
                            $query->whereHas('kategori', function (Builder $query){
                                $query->where('name', 'Tenaga Pendidik');
                            });
                        })->with('pegawai')->get()->pluck('pegawai')->where('active_status_id',1)->where('nip',$request->employee)->first();

                        $nama = $employee ? $employee->name : null;

                        if($position && (($request->subject && $subject) || !$request->subject) && $employee){
                            if(!$skbm){
                                $skbm = new Skbm();
                                $skbm->academic_year_id = $aktif->id;
                                $skbm->unit_id = $unit->id;
                                $skbm->principle_id = $request->user()->pegawai->id;
                                $skbm->status_id = 1;
                                $skbm->save();

                                $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                            }

                            $detail = new SkbmDetail();
                            $detail->position_id = $position->id;
                            $detail->subject_id = $subject ? $subject->id : null;
                            $detail->employee_id = $employee->id;
                            $detail->students = $request->students > 0 ? $request->students : null;
                            $detail->teaching_load = $request->teaching_load > 0 ? $request->teaching_load : null;
                            $detail->teaching_decree_date = $request->teaching_decree_date ? Date::parse($request->teaching_decree_date) : null;
                            $detail->teaching_decree_number = $request->teaching_decree_number ? $request->teaching_decree_number : null;

                            $skbm->detail()->save($detail);

                            Session::flash('success','Data '.$nama.'  berhasil ditambahkan');

                        }

                        else Session::flash('danger','Data '.$nama.'  gagal ditambahkan');

                        return redirect()->route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
                    }
                }
            }
        }
        
        return redirect()->route('skbm.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tahunpelajaran, $unit)
    {
        $role = $request->user()->role->name;

        $tahunajaran = str_replace("-","/",$tahunpelajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::sekolah()->where('name',$unit)->first();

        if($aktif && $unit){
            if($role == 'kepsek'){
                if($request->user()->pegawai->unit->is($unit))
                {
                    $tahunajaran_aktif = TahunAjaran::where('is_active',1)->latest()->first();
                    $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                    if($skbm || (!$skbm && $aktif->is($tahunajaran_aktif))){
                        // $jabatan = $unit->jabatan()->whereHas('kategori', function (Builder $query){
                        //     $query->where('name', 'Tenaga Pendidik');
                        // })->where('name','!=','Wali Kelas')->get();
                        $jabatan = $unit->jabatan()->whereHas('kategori', function (Builder $query){
                            $query->where('name', 'Tenaga Pendidik');
                        })->get();
                        $mapel = $unit->mataPelajaran;
                        $pegawai = $unit->pegawais()->whereHas('jabatans', function (Builder $query){
                            $query->whereHas('kategori', function (Builder $query){
                                $query->where('name', 'Tenaga Pendidik');
                            });
                        })->with('pegawai')->get()->pluck('pegawai')->sortBy('name')->where('active_status_id',1)->all();

                        return view('kepegawaian.kepsek.skbm_tampil', compact('aktif','unit','skbm','jabatan','mapel','pegawai'));
                    }
                }
            }
            else{
                $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();

                if($skbm){
                    $access = FALSE;
                    if($role == 'wakasek'){
                        if($request->user()->pegawai->unit->is($unit)){
                            $access = TRUE;
                        }
                    }
                    else $access = TRUE;
                    
                    if($access)
                        return view('kepegawaian.read-only.skbm_tampil', compact('aktif','unit','skbm'));
                }
            }
        }
        
        return redirect()->route('skbm.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tahunpelajaran, $unit)
    {
        $tahunajaran = str_replace("-","/",$tahunpelajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::sekolah()->where('name',$unit)->first();

        if($aktif && $unit){
            if($request->user()->role->name == 'kepsek') //harusnya kepsek
            {
                if($request->user()->pegawai->unit->is($unit))
                {
                    $tahunajaran_aktif = TahunAjaran::where('is_active',1)->latest()->first();
                    $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                    $detail = $request->id ? SkbmDetail::find($request->id) : null;
                    if($skbm && $detail && $detail->skbm->is($skbm)){
                        return view('kepegawaian.kepsek.skbm_ubah', compact('aktif','unit','detail'));
                    }
                }
            }
        }
        
        return redirect()->route('skbm.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahunpelajaran, $unit)
    {
        $tahunajaran = str_replace("-","/",$tahunpelajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::sekolah()->where('name',$unit)->first();

        if($aktif && $unit){
            if($request->user()->role->name == 'kepsek') //harusnya kepsek
            {
                if($request->user()->pegawai->unit->is($unit))
                {
                    $tahunajaran_aktif = TahunAjaran::where('is_active',1)->latest()->first();
                    $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                    if($skbm || (!$skbm && $aktif->is($tahunajaran_aktif))){
                        $messages = [
                            'students.numeric' => 'Pastikan jumlah siswa per rombel hanya mengandung angka',
                            'teaching_load.numeric' => 'Pastikan beban jam mengajar hanya mengandung angka',
                            'teaching_decree_date.date' => 'Pastikan format tanggal valid'
                        ];

                        $this->validate($request, [
                            'students' => 'numeric',
                            'teaching_load' => 'numeric',
                            'teaching_decree_date' => 'nullable|date'
                        ], $messages);

                        $nama = null;

                        $detail = $request->id ? SkbmDetail::find($request->id) : null;
                        
                        $nama = $detail->pegawai->name;

                        if($skbm && $detail && $detail->skbm->is($skbm)){
                            $nama = $detail->pegawai->name;

                            $detail->students = $request->students > 0 ? $request->students : null;
                            $detail->teaching_load = $request->teaching_load > 0 ? $request->teaching_load : null;
                            $detail->teaching_decree_date = $request->teaching_decree_date ? Date::parse($request->teaching_decree_date) : null;
                            $detail->teaching_decree_number = $request->teaching_decree_number ? $request->teaching_decree_number : null;

                            $detail->save();

                            Session::flash('success','Data '.$nama.' berhasil diubah');
                        }

                        else Session::flash('danger','Data '.$nama.' gagal diubah');

                        return redirect()->route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
                    }
                }
            }
        }
        
        return redirect()->route('skbm.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $tahunpelajaran, $unit, $id)
    {
        $tahunajaran = str_replace("-","/",$tahunpelajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::sekolah()->where('name',$unit)->first();

        if($aktif && $unit){
            if($request->user()->role->name == 'kepsek') //harusnya kepsek
            {
                if($request->user()->pegawai->unit->is($unit))
                {
                    $tahunajaran_aktif = TahunAjaran::where('is_active',1)->latest()->first();
                    $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                    if($skbm || (!$skbm && $aktif->is($tahunajaran_aktif))){
                        $nama = null;
                        
                        $detail = $request->id ? SkbmDetail::find($request->id) : null;
                        
                        $nama = $detail->pegawai->name;

                        if($skbm && $detail && $detail->skbm->is($skbm)){
                            $nama = $detail->pegawai->name;

                            $skbm_id = $detail->skbm->id;

                            $detail->delete();

                            $skbm = Skbm::find($skbm_id);

                            if($skbm->show->count() < 1){
                                $skbm->delete();
                            }

                            Session::flash('success','Data '.$nama.' berhasil dihapus');
                        }

                        else Session::flash('danger','Data '.$nama.' gagal dihapus');

                        return redirect()->route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
                    }
                }
            }
        }
        
        return redirect()->route('skbm.index');
    }

    /**
     * Export the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $tahunpelajaran, $unit)
    {
        $tahunajaran = str_replace("-","/",$tahunpelajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::sekolah()->where('name',$unit)->first();

        if($aktif && $unit){
            if($request->user()->role->name == 'kepsek') //harusnya kepsek
            {
                if($request->user()->pegawai->unit->is($unit))
                {
                    $tahunajaran_aktif = TahunAjaran::where('is_active',1)->latest()->first();
                    $skbm = Skbm::where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at','desc')->first();
                    if($skbm || (!$skbm && $aktif->is($tahunajaran_aktif))){
                        $spreadsheet = new Spreadsheet;

                        $spreadsheet->getProperties()->setCreator('SIT Auliya')
                        ->setLastModifiedBy($request->user()->pegawai->name)
                        ->setTitle("Data Pembagian Tugas Mengajar Guru ".$skbm->unit->name." Tahun Pelajaran ".$skbm->tahunAjaran->academic_year)
                        ->setSubject("SKBM ".$skbm->unit->name." Tahun Pelajaran ".$skbm->tahunAjaran->academic_year)
                        ->setDescription("Rekapitulasi Pembagian Tugas Mengajar Guru ".$skbm->unit->name." Tahun Pelajaran ".$skbm->tahunAjaran->academic_year)
                        ->setKeywords("Data, Tugas, Mengajar, Guru");

                        $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'REKAPITULASI PEMBAGIAN TUGAS MENGAJAR GURU')
                        ->setCellValue('A2', 'TAHUN PELAJARAN '.$skbm->tahunAjaran->academic_year)
                        ->setCellValue('A3', 'UNIT '.$skbm->unit->name)
                        ->setCellValue('A5', 'No')
                        ->setCellValue('B5', 'Struktural/Guru Mapel')
                        ->setCellValue('C5', 'Mata Pelajaran')
                        ->setCellValue('D5', 'Nama')
                        ->setCellValue('E5', 'Jumlah Siswa Per Rombel')
                        ->setCellValue('F5', 'Beban Jam Mengajar')
                        ->setCellValue('G5', 'Tanggal SK Mengajar')
                        ->setCellValue('H5', 'SK Mengajar');

                        $kolom = 6;
                        $no = 1;
                        $max_kolom = count($skbm->show)+$kolom-1;
                        foreach($skbm->show->sortBy('position_id')->all() as $s) {
                            $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('A'.$kolom, $no++)
                            ->setCellValue('B'.$kolom, $s->jabatan->name)
                            ->setCellValue('C'.$kolom, $s->mataPelajaran ? $s->mataPelajaran->subject_name : '')
                            ->setCellValue('D'.$kolom, $s->pegawai->name)
                            ->setCellValue('E'.$kolom, $s->students ? $s->students : 0)
                            ->setCellValue('F'.$kolom, $s->teaching_load ? $s->teaching_load : 0)
                            ->setCellValue('G'.$kolom, $s->teaching_decree_date ? $s->teachingDecreeDateId : '')
                            ->setCellValue('H'.$kolom, $s->teaching_decree_number ? $s->teaching_decree_number : '');

                            $kolom++;
                        }

                        $spreadsheet->getActiveSheet()->setTitle('SKBM '.$skbm->unit->name);

                        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
                        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
                        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
                        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);

                        $styleArray = [
                            'font' => [
                                'size' => 14,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT
                            ]
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray);

                        $styleArray = [
                            'font' => [
                                'size' => 12,
                                'bold' => true
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                            ],
                        ];

                        // Table Head
                        $spreadsheet->getActiveSheet()->getStyle('A5:H5')->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('A6:A'.$max_kolom)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $spreadsheet->getActiveSheet()->getStyle('E6:H'.$max_kolom)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Set the number format mask so that the excel timestamp will be displayed as a human-readable date/time
                        $spreadsheet->getActiveSheet()->getStyle('G6:G'.$max_kolom)->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

                        $styleArray = [
                            'font' => [
                                'size' => 12
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ],
                            ],
                        ];

                        $spreadsheet->getActiveSheet()->getStyle('A6:H'.$max_kolom)->applyFromArray($styleArray);

                        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

                        $headers = [
                            'Cache-Control' => 'max-age=0',
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment;filename="skbm-'.strtolower($unit->name)."-tahun-".$aktif->academicYearLink.'.xlsx"',
                        ];

                        return response()->stream(function()use($writer){
                            $writer->save('php://output');
                        }, 200, $headers);
                    }
                }
            }
        }
        
        return redirect()->route('skbm.index');
    }
}

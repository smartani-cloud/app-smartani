<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Psc\PscGradeSet;
use App\Models\Psc\PscIndicator;
use App\Models\Psc\PscIndicatorGrader;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscRoleMapping;
use App\Models\Psc\PscScore;
use App\Models\Psc\PscScoreIndicator;
use App\Models\Psc\PscScoreIndicatorGrader;
use App\Models\Rekrutmen\PegawaiUnit;
use App\Models\Unit;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanPrestasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $unit = null)
    {
        $role = $request->user()->role->name;

        // Can view?
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',3);

        // Check targets
        if($targetsQuery->count() > 0){
            if($tahun){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('psc.laporan.pegawai.index');
            $tahunPelajaran = TahunAjaran::where('is_active',1)->orHas('nilaiPsc')->orderBy('created_at')->get();

            // Check available units
            $allUnit = null;

            $positions = $targetsQuery->pluck('target_position_id');
            if($role == 'kepsek'){
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request,$tahun){
                    return ($value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0) && (($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school));
                })->all();
            }
            else{
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($tahun){
                    return $value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0;
                })->all();
            }
            $allUnit = collect($allUnit);

            if($unit){
                $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
                if($unitAktif){
                    $nilai = $unitAktif->nilaiPsc()->where([
                        'academic_year_id' => $tahun->id,
                        'acc_status_id' => 1,
                    ])->whereNotNull(['acc_employee_id','acc_time'])->latest()->get();

                    return view('kepegawaian.pa.psc.laporan_detail', compact('tahun','tahunPelajaran','unitAktif','nilai'));
                }
                else return redirect()->route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink]);
            }

            return view('kepegawaian.pa.psc.laporan_index', compact('tahun','tahunPelajaran','allUnit'));
        }

        else return redirect()->route('kepegawaian.index');
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
    public function show(Request $request, $tahun, $unit, $pegawai)
    {
        $role = $request->user()->role->name;

        // Can view?
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',3);

        // Check targets
        if($targetsQuery->count() > 0){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('psc.laporan.pegawai.index');

            // Check available units
            $allUnit = null;

            $positions = $targetsQuery->pluck('target_position_id');
            if($role == 'kepsek'){
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request,$tahun){
                    return ($value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0) && (($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school));
                })->all();
            }
            else{
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($tahun){
                    return $value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0;
                })->all();
            }
            $allUnit = collect($allUnit);

            $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
            if($unitAktif){
                $pegawaiUnits = PegawaiUnit::select('employee_id')->where('unit_id',$unitAktif->id)->whereHas('jabatans',function($q)use($positions){
                    $q->whereIn('position_id',$positions);
                })->pluck('employee_id');

                //return $pegawaiUnits;

                $nilai = $unitAktif->nilaiPsc()->where([
                    'academic_year_id' => $tahun->id,
                    'acc_status_id' => 1
                ])->whereHas('pegawai',function($q)use($pegawai,$pegawaiUnits){
                    $q->where('nip',$pegawai)->whereIn('id',$pegawaiUnits);
                })->whereNotNull(['acc_employee_id','acc_time'])->latest()->first();

                return view('kepegawaian.pa.psc.laporan_show', compact('tahun','unitAktif','nilai'));
            }
            else return redirect()->route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink]);
        }

        else return redirect()->route('kepegawaian.index');
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

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $tahun, $unit){
        $role = $request->user()->role->name;

        // Can view?
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',3);

        // Check targets
        if($targetsQuery->count() > 0){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('psc.laporan.pegawai.index');

            // Check available units
            $allUnit = null;

            $positions = $targetsQuery->pluck('target_position_id');
            if($role == 'kepsek'){
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request,$tahun){
                    return ($value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0) && (($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school));
                })->all();
            }
            else{
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($tahun){
                    return $value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0;
                })->all();
            }
            $allUnit = collect($allUnit);

            $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
            if($unitAktif){
                $nilai = $unitAktif->nilaiPsc()->where([
                    'academic_year_id' => $tahun->id,
                    'acc_status_id' => 1,
                ])->whereNotNull(['acc_employee_id','acc_time'])->latest()->get();

                if($nilai && count($nilai) > 0){
                    $spreadsheet = new Spreadsheet();

                    $spreadsheet->getProperties()->setCreator('SIT Auliya')
                    ->setLastModifiedBy($request->user()->pegawai->name)
                    ->setTitle("Data Penilaian Kinerja Pegawai ".$unitAktif->name." Tahun Pelajaran ".$tahun->academic_year)
                    ->setSubject("Laporan Penilaian Kinerja ".$unitAktif->name." Tahun Pelajaran ".$tahun->academic_year)
                    ->setDescription("Rekapitulasi Penilaian Kinerja Pegawai ".$unitAktif->name." Tahun Pelajaran ".$tahun->academic_year)
                    ->setKeywords("Penilaian, Kinerja, Pegawai, Auliya");

                    $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'REKAPITULASI PENILAIAN KINERJA PEGAWAI SIT AULIYA')
                    ->setCellValue('A2', 'TAHUN PELAJARAN '.$tahun->academic_year)
                    ->setCellValue('A3', 'UNIT '.strtoupper($unitAktif->name))
                    ->setCellValue('A5', 'No')
                    ->setCellValue('B5', 'Nama Pegawai')
                    ->setCellValue('C5', 'NIPY')
                    ->setCellValue('D5', 'Jabatan')
                    ->setCellValue('E5', 'Jumlah Nilai')
                    ->setCellValue('F5', 'Grade');

                    $kolom = 6;
                    $no = 1;
                    $max_kolom = count($nilai)+$kolom-1;
                    foreach($nilai as $n) {
                        $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A'.$kolom, $no++)
                        ->setCellValue('B'.$kolom, $n->pegawai->name)
                        ->setCellValueExplicit('C'.$kolom, strval($n->pegawai->nip), DataType::TYPE_STRING)
                        ->setCellValue('D'.$kolom, $n->jabatan->name)
                        ->setCellValue('E'.$kolom, $n->total_score)
                        ->setCellValue('F'.$kolom, $n->grade_name);

                        $kolom++;
                    }

                    $spreadsheet->getActiveSheet()->setTitle('Penilaian Kinerja');

                    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
                    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                    $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(30);

                    $styleArray = [
                        'font' => [
                            'size' => 14,
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
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
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ],
                    ];

                    // Table Head
                    $spreadsheet->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('A6:F'.$max_kolom)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


                    $spreadsheet->getActiveSheet()->getStyle('B6:B'.$max_kolom)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

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

                    $spreadsheet->getActiveSheet()->getStyle('A6:F'.$max_kolom)->applyFromArray($styleArray);

                    $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

                    $headers = [
                        'Cache-Control' => 'max-age=0',
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment;filename="penilaian_kinerja_'.$unitAktif->name.'_'.$tahun->academicYearLink.'.xlsx"',
                    ];

                    return response()->stream(function()use($writer){
                        $writer->save('php://output');
                    }, 200, $headers);
                }
            }
            else return redirect()->route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink]);
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Download the specific resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $tahun, $unit, $pegawai)
    {
        $role = $request->user()->role->name;

        // Can view?
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',3);

        // Check targets
        if($targetsQuery->count() > 0){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('psc.laporan.pegawai.index');

            // Check available units
            $allUnit = null;

            $positions = $targetsQuery->pluck('target_position_id');
            if($role == 'kepsek'){
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request,$tahun){
                    return ($value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0) && (($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school));
                })->all();
            }
            else{
                $allUnit = JabatanUnit::whereIn('position_id',$positions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($tahun){
                    return $value->nilaiPsc()->where(['academic_year_id' => $tahun->id,'acc_status_id' => 1])->count() > 0;
                })->all();
            }
            $allUnit = collect($allUnit);

            $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
            if($unitAktif){
                $pegawaiUnits = PegawaiUnit::select('employee_id')->where('unit_id',$unitAktif->id)->whereHas('jabatans',function($q)use($positions){
                    $q->whereIn('position_id',$positions);
                })->pluck('employee_id');

                $nilai = $unitAktif->nilaiPsc()->where([
                    'academic_year_id' => $tahun->id,
                    'acc_status_id' => 1
                ])->whereHas('pegawai',function($q)use($pegawai,$pegawaiUnits){
                    $q->where('nip',$pegawai)->whereIn('id',$pegawaiUnits);
                })->whereNotNull(['acc_employee_id','acc_time'])->latest()->first();

                $psc = PscGradeSet::where('status_id','!=',1)->orderBy('created_at')->first();

                if($nilai && $nilai->detail()->count() > 0){
                    $kodeIndikator = $nilai->detail()->pluck('code')->unique()->toArray();
                    natsort($kodeIndikator);
                    // Export Section

                    $spreadsheet = new Spreadsheet();

                    $spreadsheet->getProperties()->setCreator('SIT Auliya')
                    ->setLastModifiedBy($request->user()->pegawai->name)
                    ->setTitle("Laporan Prestasi Kerja ".$nilai->pegawai->nama." Tahun Pelajaran ".$nilai->tahunPelajaran->academic_year)
                    ->setSubject("Laporan Prestasi Kerja ".$nilai->pegawai->nama." Tahun Pelajaran ".$nilai->tahunPelajaran->academic_year)
                    ->setDescription("Rekapitulasi Nilai Prestasi Kerja ".$nilai->pegawai->nama." Tahun Pelajaran ".$nilai->tahunPelajaran->academic_year)
                    ->setKeywords("Laporan, Prestasi, Kerja, Pegawai, Sekolah, SIT, Auliya");

                    $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A5', 'LAPORAN PRESTASI KERJA PEGAWAI')
                    ->setCellValue('A6', 'SEKOLAH ISLAM TERPADU AULIYA')
                    ->setCellValue('A8', '"Dan bagi masing-masing mereka derajat menurut apa yang mereka kerjakan dan agar Allah mencukupkan')
                    ->setCellValue('A9', 'bagi mereka (balasan) pekerjaan-pekerjaan mereka, sedangkan mereka tidak dirugikan (Al-Ahqaaf:19)"')
                    ->setCellValue('A11', 'Nama')
                    ->setCellValue('C11', ':')
                    ->setCellValue('D11', $nilai->employee_name ? $nilai->employee_name : $nilai->pegawai->name)
                    ->setCellValue('A12', 'Tahun Pelajaran')
                    ->setCellValue('C12', ':')
                    ->setCellValue('D12', $nilai->tahunPelajaran->academic_year)
                    ->setCellValue('A13', 'Unit')
                    ->setCellValue('C13', ':')
                    ->setCellValue('D13', $nilai->unit->name)
                    ->setCellValue('A14', 'Jabatan'.($nilai->jabatan->kategoriPenempatan->id == 1 ? ' '.$nilai->jabatan->kategoriPenempatan->name : null))
                    ->setCellValue('C14', ':')
                    ->setCellValue('D14', $nilai->position_name ? $nilai->position_name : $nilai->jabatan->name)
                    ->setCellValue('A16', 'No')
                    ->setCellValue('B16', strtoupper('Aspek dan Indikator Kinerja Utama'))
                    ->setCellValue('E16', strtoupper('Skor'))
                    ->setCellValue('F16', strtoupper('Bobot'))
                    ->setCellValue('G16', strtoupper('Nilai'));

                    $logo = new Drawing;
                    $logo->setName('Logo Auliya');
                    $logo->setDescription('Logo Auliya');
                    $logo->setPath('./img/logo/logo-vertical.png');
                    $logo->setHeight(76);
                    $logo->setOffsetX(255);
                    $logo->setOffsetY(15);
                    $logo->setWorksheet($spreadsheet->getActiveSheet());
                    $logo->setCoordinates('D1');

                    $spreadsheet->getActiveSheet()->mergeCells('A5:G5');
                    $spreadsheet->getActiveSheet()->mergeCells('A6:G6');
                    $spreadsheet->getActiveSheet()->mergeCells('A8:G8');
                    $spreadsheet->getActiveSheet()->mergeCells('A9:G9');
                    $spreadsheet->getActiveSheet()->mergeCells('A11:B11');
                    $spreadsheet->getActiveSheet()->mergeCells('A12:B12');
                    $spreadsheet->getActiveSheet()->mergeCells('A13:B13');
                    $spreadsheet->getActiveSheet()->mergeCells('A14:B14');
                    $spreadsheet->getActiveSheet()->mergeCells('B16:D16');

                    $kolom = $first_kolom = 17;
                    $max_kolom = $nilai->detail()->count()+$kolom-1;

                    foreach($kodeIndikator as $k){
                        $n = $nilai->detail()->where('code',$k)->first();

                        $spreadsheet->getActiveSheet()
                        ->setCellValue('A'.$kolom, $n->code)
                        ->setCellValue('B'.$kolom, $n->indikator->name)
                        ->setCellValue('E'.$kolom, $n->score)
                        ->setCellValue('F'.$kolom, $n->percentage.'%')
                        ->setCellValue('G'.$kolom, $n->total_score);
                        $spreadsheet->getActiveSheet()->mergeCells('B'.$kolom.':D'.$kolom);

                        if($n->indikator->level == 1){
                            $styleArray = [
                                'font' => [
                                    'size' => 14,
                                    'bold' => $n->indikator->level == 1 ?true : false
                                ]
                            ];

                            $spreadsheet->getActiveSheet()->getRowDimension($kolom)->setRowHeight(20);
                        }
                        else{
                            $styleArray = [
                                'font' => [
                                    'size' => 12
                                ]
                            ];
                        }

                        $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':G'.$kolom)->applyFromArray($styleArray);

                        $kolom++;
                    }

                    // Total Score
                    $spreadsheet->getActiveSheet()
                    ->setCellValue('A'.$kolom, strtoupper('Jumlah Nilai'))
                    ->setCellValue('G'.$kolom, ($nilai && $nilai->total_score ? $nilai->total_score : '-'));
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':F'.$kolom);

                    $spreadsheet->getActiveSheet()->getRowDimension($kolom)->setRowHeight(20);

                    $kolom ++;

                    $spreadsheet->getActiveSheet()
                    ->setCellValue('A'.$kolom, strtoupper('Grade'))
                    ->setCellValue('G'.$kolom, ($nilai && $nilai->grade_name ? $nilai->grade_name : '-'));
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$kolom.':F'.$kolom);

                    $spreadsheet->getActiveSheet()->getRowDimension($kolom)->setRowHeight(20);

                    $styleArray = [
                        'font' => [
                            'size' => 12,
                            'bold' => true
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('A'.($kolom-1).':F'.$kolom)->applyFromArray($styleArray);

                    $styleArray = [
                        'font' => [
                            'size' => 14,
                            'bold' => true
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('G'.($kolom-1).':G'.$kolom)->applyFromArray($styleArray);

                    $kolom += 2;

                    $spreadsheet->getActiveSheet()
                    ->setCellValue('A'.$kolom, "Rentang Nilai");

                    $kolom++;

                    $gradeCell = '';

                    if($nilai->gradeRecord && $nilai->gradeRecord->grades){
                        $rentangDecode = json_decode($nilai->gradeRecord->grades, true);

                        foreach($rentangDecode as $r){
                            if(strlen($gradeCell) > 0) $gradeCell .= str_repeat(' ',10);
                            $gradeCell .= $r['name'].' : '.$r['start'].' - '.$r['end'];
                        }
                    }
                    else{
                        // $rentang = $psc->grade()->select('name','start','end')->orderBy('end','desc')->get();

                        // foreach($rentang as $r){
                        //     if(strlen($gradeCell) > 0) $gradeCell .= str_repeat(' ',10);
                        //     $gradeCell .= $r->name.' : '.$r->start.' - '.$r->end;
                        // }
                        $gradeCell = '-';
                    }

                    $spreadsheet->getActiveSheet()
                    ->setCellValue('A'.$kolom, $gradeCell);

                    $kolom += 2;

                    $spreadsheet->getActiveSheet()->setTitle($nilai->pegawai->name);

                    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8);
                    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
                    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(2);
                    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(63);
                    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
                    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
                    $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(25);
                    $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(25);
                    $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
                    $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(30);
                    $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(30);
                    $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
                    $spreadsheet->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
                    $spreadsheet->getActiveSheet()->getRowDimension('16')->setRowHeight(25);

                    $styleArray = [
                        'font' => [
                            'size' => 14,
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleArray);

                    $styleArray = [
                        'font' => [
                            'size' => 14,
                            'italic' => true
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('A8:A9')->applyFromArray($styleArray);

                    $styleArray = [
                        'font' => [
                            'size' => 12
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('A11:A14')->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('D11:D14')->applyFromArray($styleArray);

                    $styleArray = [
                        'font' => [
                            'size' => 12
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('C11:C14')->applyFromArray($styleArray);

                    // Table Head

                    $styleArray = [
                        'font' => [
                            'size' => 14,
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ],
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('A16:G16')->applyFromArray($styleArray);

                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ],
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('A'.$first_kolom.':G'.($max_kolom+2))->applyFromArray($styleArray);

                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('B'.$first_kolom.':D'.($max_kolom))->applyFromArray($styleArray);

                    // Validator Signature Row
                    $spreadsheet->getActiveSheet()
                    ->setCellValue('E'.$kolom, 'Tangerang Selatan,');

                    $kolom++;

                    $spreadsheet->getActiveSheet()
                    ->setCellValue('E'.$kolom, 'Direktur SIT Auliya');

                    $kolom+=3;

                    $spreadsheet->getActiveSheet()->setCellValue('E'.$kolom, 'Dr. Kurniasih Mufidayati, M.Si.');

                    $styleArray = [
                        'font' => [
                            'size' => 12
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $spreadsheet->getActiveSheet()->getStyle('E'.($kolom-4).':E'.$kolom)->applyFromArray($styleArray);

                    $kolom++;

                    $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

                    $headers = [
                        'Cache-Control' => 'max-age=0',
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment;filename="nilai_'.$nilai->pegawai->nip.'_'.$nilai->tahunPelajaran->academicYearLink.'.xlsx"',
                    ];

                    return response()->stream(function()use($writer){
                        $writer->save('php://output');
                    }, 200, $headers);

                    // End of Export Section
                }
            }
            else return redirect()->route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink]);
        }

        else return redirect()->route('kepegawaian.index');
    }
}
 
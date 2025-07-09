<?php

namespace Modules\HR\Http\Controllers\EmployeeManagement\Placement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Penempatan\KategoriPenempatan;
use App\Models\Penempatan\PenempatanPegawai;
use App\Models\Penempatan\PenempatanPegawaiArsip;
use App\Models\Penempatan\PenempatanPegawaiDetail;
use App\Models\Rekrutmen\Pegawai;
use App\Models\StatusAcc;
use App\Models\Unit;

class NonstructuralEmployeePlacement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        if($request->tahunajaran){
            $tahunajaran = str_replace("-","/",$request->tahunajaran);
            $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        }
        else{
            $aktif = TahunAjaran::where('is_active',1)->latest()->first();
        }
        $tahun = TahunAjaran::orderBy('created_at')->get();
        if(in_array($role,['kepsek','wakasek'])){
            $unit = Unit::where('id',$request->user()->pegawai->unit_id);
        }
        else{
            $unit = Unit::orderBy('created_at');
        }

        $unit = $unit->get();

        if(in_array($role,['etl','etm','aspv']))
            $folder = $role;
        else $folder = 'read-only';

        return view('kepegawaian.read-only.penempatan_nonstruktural_index', compact('aktif','tahun','unit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $tahunajaran, $unit)
    {
        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            $messages = [
                'employee.required' => 'Mohon pilih pegawai',
                'position.required' => 'Mohon pilih penempatan',
            ];

            $this->validate($request, [
                'employee' => 'required',
                'position' => 'required',
                'period_start' => 'required|date',
                'period_end' => 'required|date|after:period_start'
            ], $messages);

            $kategori = KategoriPenempatan::where('placement','nonstruktural')->first();
            $penempatan = PenempatanPegawai::where('placement_id',$kategori->id)->where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at')->first();

            $employee = Pegawai::where('nip',$request->employee)->first();
            $position = Jabatan::find($request->position);

            if($employee && $position){

                if(!$penempatan){
                    $penempatan = new PenempatanPegawai();
                    $penempatan->academic_year_id = $aktif->id;
                    $penempatan->unit_id = $unit->id;
                    $penempatan->placement_id = $kategori->id;
                    $penempatan->status_id = 1;
                    $penempatan->save();

                    $penempatan = PenempatanPegawai::where('placement_id',$kategori->id)->where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at')->first();
                }

                $detail = new PenempatanPegawaiDetail();
                $detail->employee_id = $employee->id;
                $detail->position_id = $position->id;
                $detail->period_start = Date::parse($request->period_start);
                $detail->period_end = Date::parse($request->period_end);
                $detail->acc_position_id = $position->acc_position_id;

                $penempatan->detail()->save($detail);

                Session::flash('success','Data berhasil ditambahkan');

            }

            else Session::flash('danger','Data gagal ditambahkan');

            return redirect()->route('nonstruktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
        }
        else{
            return redirect()->route('nonstruktural.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tahunajaran, $unit)
    {
        $role = $request->user()->role->name;

        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        if(in_array($role,['kepsek','wakasek']))
            $unit = Unit::where(['id' => $request->user()->pegawai->unit->id,'name' => $unit])->first();
        else
            $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            $kategori = KategoriPenempatan::where('placement','nonstruktural')->first();
            $penempatan = PenempatanPegawai::where('placement_id',$kategori->id)->where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at')->first();

            if(in_array($role,['etl','etm','aspv']))
                $folder = $role;
            else $folder = 'read-only';

            if($role == 'etm'){
                // $pegawai = $unit->pegawai()->where('active_status_id',1)->whereDoesntHave('penempatan', function (Builder $query) use ($aktif,$unit) {
                //     $query->whereHas('penempatanPegawai', function (Builder $query) use ($aktif,$unit) {
                //         $query->where('academic_year_id', $aktif->id)->where('unit_id',$unit->id);
                //     });
                // })->get();
                $pegawai = Pegawai::whereIn('id',$unit->pegawais()->pluck('employee_id'))->aktif()->get();
                $jabatan = $unit->jabatan()->where('placement_id',$kategori->id)->get();
                return view('kepegawaian.'.$folder.'.penempatan_nonstruktural_tampil', compact('aktif','unit','penempatan','pegawai','jabatan'));
            }
            else{
                return view('kepegawaian.'.$folder.'.penempatan_nonstruktural_tampil', compact('kategori','aktif','unit','penempatan'));
            }
        }
        else{
            return redirect()->route('nonstruktural.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tahunajaran, $unit)
    {
        $role = $request->user()->role->name;
        
        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            $kategori = KategoriPenempatan::where('placement','nonstruktural')->first();
            $detail = PenempatanPegawaiDetail::find($request->id);
            if($detail){
                if($role == 'etm'){
                    $jabatan = $unit->jabatan()->where('placement_id',$kategori->id)->get();
                    return view('kepegawaian.'.$role.'.penempatan_nonstruktural_ubah', compact('aktif','unit','detail','jabatan'));
                }
                elseif($role == 'etl'){
                    $acc = StatusAcc::all();
                    return view('kepegawaian.'.$role.'.penempatan_nonstruktural_ubah', compact('aktif','unit','detail','acc'));
                }
                else{
                    return view('kepegawaian.'.$role.'.penempatan_nonstruktural_ubah', compact('aktif','unit','detail'));
                }
            }
        }
        else{
            return redirect()->route('nonstruktural.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahunajaran, $unit)
    {
        $role = $request->user()->role->name;
        
        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            if($role == 'etm'){
                $messages = [
                    'position.required' => 'Mohon pilih penempatan',
                ];

                $this->validate($request, [
                    'position' => 'required',
                    'period_start' => 'required|date',
                    'period_end' => 'required|date|after:period_start'
                ], $messages);

                $detail = PenempatanPegawaiDetail::find($request->id);
                $position = Jabatan::find($request->position);

                if($detail && $position){
                    $detail->position_id = $position->id;
                    $detail->period_start = Date::parse($request->period_start);
                    $detail->period_end = Date::parse($request->period_end);
                    $detail->acc_position_id = $position->acc_position_id;

                    $detail->save();

                    Session::flash('success','Data berhasil diubah');

                }
                else Session::flash('danger','Data gagal diubah');

                return redirect()->route('nonstruktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
            }
            elseif($role == 'etl'){
                $messages = [
                    'acc_status.required' => 'Mohon tentukan persetujuan'
                ];

                $this->validate($request, [
                    'acc_status' => 'required'
                ], $messages);

                $detail = $request->id ? PenempatanPegawaiDetail::find($request->id) : null;
                $jabatan_id = $request->user()->pegawai->position_id;

                if($detail && $detail->acc_position_id == $jabatan_id){
                //if($detail && $detail->acc_position_id != $jabatan_id){ -- untuk uji
                    $detail->acc_employee_id = $request->user()->pegawai->id;
                    $detail->acc_status_id = $request->acc_status;
                    $detail->acc_time = Date::now('Asia/Jakarta');

                    if($request->acc_status == 1){
                        $pegawai = $detail->pegawai;
                        $pegawai->position_id = $detail->position_id;

                        $user = $pegawai->login;
                        $user->role_id = $detail->jabatan->role->id;
                        $user->save();

                        $pegawai->save();
                    }

                    $detail->save();

                    $detail->fresh();

                    if($request->acc_status == 1 && $pegawai->units()->where('unit_id',$unit->id)->count() > 0){
                        $detailPegawai = $detail->pegawai;

                        $penempatan = PenempatanPegawaiDetail::whereHas('penempatanPegawai', function(Builder $query) use ($detail){
                            $query->where([
                                'academic_year_id' => $detail->penempatanPegawai->academic_year_id,
                                'unit_id' => $detail->penempatanPegawai->unit_id
                            ]);
                        })->where([
                            'employee_id' => $detail->employee_id,
                            'acc_status_id' => 1
                        ])->whereNotNull(['acc_employee_id','acc_time'])->pluck('position_id');

                        if($penempatan->count() > 0){
                            $pegawaiUnit = $detailPegawai->units()->where('unit_id',$unit->id)->first();
                            if($pegawaiUnit){
                                $pegawaiUnit->jabatans()->sync($penempatan);
                            }
                        }
                    }

                    Session::flash('success','Data berhasil diubah');
                }
                else Session::flash('danger','Data gagal diubah');

                return redirect()->route('nonstruktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
            }
            elseif($role == 'aspv'){
                $messages = [
                    'placement_date.required' => 'Mohon tentukan tanggal penetapan',
                    'placement_date.date' => 'Format tanggal penetapan tidak valid',
                ];

                $this->validate($request, [
                    'placement_date' => 'required|date'
                ], $messages);

                $detail = $request->id ? PenempatanPegawaiDetail::find($request->id) : null;

                if($detail){
                    $detail->placement_date = Date::parse($request->placement_date);
                    $detail->save();

                    Session::flash('success','Data berhasil diubah');
                }
                else Session::flash('danger','Data gagal diubah');

                return redirect()->route('nonstruktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
            }
            else{
                //
            }
        }
        else{
            return redirect()->route('nonstruktural.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $tahunajaran, $unit, $id)
    {
        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            if(in_array($request->user()->role->name,['etl','etm'])){
                $detail = PenempatanPegawaiDetail::find($id);
                
                if($detail){
                    $penempatan_id = $detail->penempatanPegawai->id;

                    if($request->user()->role->name == 'etm'){
                        if($detail->acc_status_id != 1){
                            $detail->forceDelete();
                            Session::flash('success','Data berhasil dihapus');
                        }
                        else Session::flash('danger','Data gagal dihapus');
                    }

                    if($request->user()->role->name == 'etl'){
                        if($detail->acc_status_id == 1){
                            $detail->delete();
                            Session::flash('success','Data berhasil dihapus');
                        }
                        else Session::flash('danger','Data gagal dihapus');
                    }

                    $penempatan = PenempatanPegawai::find($penempatan_id);

                    if($penempatan->show->count() < 1){
                        $penempatan->delete();
                    }
                }
                else Session::flash('danger','Data gagal dihapus');

                return redirect()->route('nonstruktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
            }
        }
        else{
            return redirect()->route('nonstruktural.index');
        }
    }

    /**
     * Update the selected resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request, $tahunajaran, $unit)
    {
        $role = $request->user()->role->name;

        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            $messages = [
                'placement_date.required' => 'Mohon tentukan tanggal penetapan',
                'placement_date.date' => 'Format tanggal penetapan tidak valid',
            ];

            $this->validate($request, [
                'placement_date' => 'required|date'
            ], $messages);
            
            $kategori = KategoriPenempatan::where('placement','nonstruktural')->first();
            $penempatan = PenempatanPegawai::where('placement_id',$kategori->id)->where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at')->first();
            $nonstruktural = $penempatan->detail()->whereNotNull([
                'period_start',
                'period_end'
            ])->whereNull([
                'acc_employee_id',
                'acc_status_id',
                'acc_time'
            ])->update([
                'placement_date' => Date::parse($request->placement_date)
            ]);

            if($nonstruktural){
                $count = $penempatan->detail()->whereNotNull(['period_start','period_end'])->whereNull(['acc_employee_id','acc_status_id','acc_time'])->count();
                Session::flash('success', "Tanggal penetapan ".$count." pegawai berhasil diatur.");
            }
            else Session::flash('danger', "Tidak ada data penempatan pegawai yang dapat diatur.");

            return redirect()->route('nonstruktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]);
        }
        
        else return redirect()->route('nonstruktural.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $tahunajaran, $unit)
    {
        $role = $request->user()->role->name;

        $tahunajaran = str_replace("-","/",$tahunajaran);
        $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
        $unit = Unit::where('name',$unit)->first();

        if($aktif && $unit){
            $kategori = KategoriPenempatan::where('placement','nonstruktural')->first();
            $penempatan = PenempatanPegawai::where('placement_id',$kategori->id)->where('academic_year_id', $aktif->id)->where('unit_id',$unit->id)->orderBy('created_at')->first();
            if($penempatan->status->status == 'aktif') $nonstruktural = $penempatan->detail();
            else $nonstruktural = $penempatan->arsip();
            $nonstruktural = $nonstruktural->whereNotNull(['acc_employee_id','acc_status_id','acc_time'])->get();

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('SIT Auliya')
            ->setLastModifiedBy($request->user()->pegawai->name)
            ->setTitle("Data Penempatan ".ucwords($kategori->placement)." Pegawai ".$unit->name." Tahun Pelajaran ".$aktif->academic_year)
            ->setSubject("Penempatan ".ucwords($kategori->placement)." Pegawai ".$unit->name." Tahun Pelajaran ".$aktif->academic_year)
            ->setDescription("Rekapitulasi Data Penempatan ".ucwords($kategori->placement)." Pegawai ".$unit->name." Tahun Pelajaran ".$aktif->academic_year)
            ->setKeywords("Penempatan, ".ucwords($kategori->placement).", Pegawai, Auliya");

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'REKAPITULASI PENEMPATAN '.strtoupper($kategori->placement).' PEGAWAI AULIYA')
            ->setCellValue('A2', 'TAHUN PELAJARAN '.$aktif->academic_year)
            ->setCellValue('A3', 'UNIT '.$unit->name)
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'Nama Pegawai')
            ->setCellValue('C5', 'NIPY')
            ->setCellValue('D5', 'Tempat Lahir')
            ->setCellValue('E5', 'Tanggal Lahir')
            ->setCellValue('F5', 'Penempatan '.ucwords($kategori->placement))
            ->setCellValue('G5', 'Tanggal Awal Masa Penempatan')
            ->setCellValue('H5', 'Tanggal Akhir Masa Penempatan')
            ->setCellValue('I5', 'Tanggal Penetapan');

            $kolom = 6;
            $no = 1;
            $max_kolom = count($nonstruktural)+$kolom-1;
            foreach($nonstruktural as $p) {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$kolom, $no++)
                ->setCellValue('B'.$kolom, $p->pegawai->name)
                ->setCellValueExplicit('C'.$kolom, $p->pegawai->nip ? strval($p->pegawai->nip) : '', DataType::TYPE_STRING)
                ->setCellValue('D'.$kolom, $p->pegawai->birth_place)
                ->setCellValue('E'.$kolom, Date::parse($p->pegawai->birth_date)->format('Y-m-d'))
                ->setCellValue('F'.$kolom, $p->jabatan->name)
                ->setCellValue('G'.$kolom, Date::parse($p->period_start)->format('Y-m-d'))
                ->setCellValue('H'.$kolom, Date::parse($p->period_end)->format('Y-m-d'))
                ->setCellValue('I'.$kolom, Date::parse($p->placement_date)->format('Y-m-d'));

                $kolom++;
            }

            $spreadsheet->getActiveSheet()->setTitle('Penempatan '.ucwords($kategori->placement));

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(35);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(35);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(35);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
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
            $spreadsheet->getActiveSheet()->getStyle('A5:I5')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A6:A'.$max_kolom)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->getStyle('C6:C'.$max_kolom)->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);
            $spreadsheet->getActiveSheet()->getStyle('C6:C'.$max_kolom)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Set the number format mask so that the excel timestamp will be displayed as a human-readable date/time
            $spreadsheet->getActiveSheet()->getStyle('E6:E'.$max_kolom)->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
            $spreadsheet->getActiveSheet()->getStyle('G6:G'.$max_kolom)->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
            $spreadsheet->getActiveSheet()->getStyle('H6:H'.$max_kolom)->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
            $spreadsheet->getActiveSheet()->getStyle('I6:I'.$max_kolom)->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

            $spreadsheet->getActiveSheet()->getStyle('C5:C'.$max_kolom)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E5:E'.$max_kolom)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('G5:I'.$max_kolom)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

            $spreadsheet->getActiveSheet()->getStyle('A6:I'.$max_kolom)->applyFromArray($styleArray);

            $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

            $headers = [
                'Cache-Control' => 'max-age=0',
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="penempatan-'.$kategori->placement.'-'.strtolower($unit->name)."-tahun-".$aktif->academicYearLink.'.xlsx"',
            ];

            return response()->stream(function()use($writer){
                $writer->save('php://output');
            }, 200, $headers);
        }
        else{
            return redirect()->route('nonstruktural.index');
        }
    }
}

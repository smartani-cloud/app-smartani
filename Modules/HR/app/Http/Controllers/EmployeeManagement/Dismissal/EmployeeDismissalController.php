<?php

namespace App\Http\Controllers\Phk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use File;
use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Rekrutmen\EvaluasiPegawai;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\Phk\AlasanPhk;
use App\Models\Phk\Phk;
use App\Models\LoginUser;

class PhkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek'])){
            $phk = Phk::whereHas('pegawai',function($query) use($request){
                $query->whereHas('units',function($query) use($request){
                    $query->where('unit_id',$request->user()->pegawai->unit_id);
                });
            })->whereNotNull(['director_acc_id','director_acc_status_id','director_acc_time']);
        }
        elseif(in_array($role,['fam','am','aspv'])){
            $phk = Phk::whereNotNull(['director_acc_id','director_acc_status_id','director_acc_time']);
        }
        else{
            $phk = new Phk();
        }

        $phk = $phk->orderBy('created_at','desc')->get();

        if(in_array($role,['direktur','etl','etm','faspv']))
            $folder = $role;
        else $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.phk_index', compact('phk'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $pegawai = $request->id ? Pegawai::find($request->id) : null;

        if($pegawai){
            $alasan = AlasanPhk::all();

            return view('kepegawaian.etm.phk_tambah', compact('pegawai','alasan'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'reason.required' => 'Mohon pilih salah satu alasan PHK pegawai',
        ];

        $this->validate($request, [
            'reason' => 'required',
        ], $messages);

        if($request->id){
            $role = $request->user()->role->name;

            $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');

            $pegawai = Pegawai::where('id',$request->id)->where('nip','!=','0')->whereNotIn('id',$pejabat)->first();
        }
        else $pegawai = null;

        if($pegawai){
            if(!$pegawai->phk){
                $phk = new Phk();
                $phk->employee_id = $pegawai->id;
                $phk->dismissal_reason_id = $request->reason;
                $phk->save();

                Session::flash('success','Data pengajuan PHK '. $pegawai->name .' berhasil ditambahkan');
            }
            else{
                Session::flash('danger','Data pengajuan PHK '. $pegawai->name .' sudah ada');
                return redirect()->route('pegawai.index');
            }
        }

        return redirect()->route('phk.index');
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
        $phk = $request->id ? Phk::find($request->id) : null;

        if($phk && !$phk->director_acc_status_id){
            $alasan = AlasanPhk::all();

            return view('kepegawaian.etm.phk_ubah', compact('phk','alasan'));
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
        $messages = [
            'reason.required' => 'Mohon pilih salah satu alasan PHK pegawai',
        ];

        $this->validate($request, [
            'reason' => 'required',
        ], $messages);

        $phk = $request->id ? Phk::find($request->id) : null;

        if($phk){
            $phk->dismissal_reason_id = $request->reason;
            $phk->save();

            Session::flash('success','Data pengajuan PHK '. $phk->pegawai->name .' berhasil diubah');
        }

        return redirect()->route('phk.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nama = null;
        $phk = Phk::find($id);

        if($phk){
            $nama = $phk->pegawai->name;
            if(!$phk->director_acc_status_id){
                $phk->delete();

                Session::flash('success','Pengajuan PHK '. $nama .' berhasil dibatalkan');
            }
        }

        return redirect()->route('phk.index');
    }

    /**
     * Accept the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $id)
    {
        $nama = null;
        $phk = Phk::find($id);

        if($phk){
            $nama = $phk->pegawai->name;
            if(!$phk->director_acc_status_id){
                $login = LoginUser::where('user_id',$phk->pegawai->id)->pegawai()->first();
                if($login) $login->delete();

                $pegawai = Pegawai::find($phk->pegawai->id);
                $pegawai->join_badge_status_id = 2;
                $pegawai->disjoin_date = Date::now('Asia/Jakarta');
                $pegawai->disjoin_badge_status_id = 1;
                $pegawai->active_status_id = 2;

                $pegawai->save();

                $phk->director_acc_id = $request->user()->pegawai->id;
                $phk->director_acc_status_id = 1;
                $phk->director_acc_time = Date::now('Asia/Jakarta');
                $phk->save();

                Session::flash('success','Pengajuan PHK '. $nama .' berhasil disetujui');
                return redirect()->route('phk.index');
            }
        }

        return redirect()->route('phk.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $phk = Phk::whereNotNull(['director_acc_id','director_acc_status_id','director_acc_time'])->orderBy('created_at','desc')->get();

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('SIT Auliya')
        ->setLastModifiedBy($request->user()->pegawai->name)
        ->setTitle("Data Pengajuan PHK Pegawai Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setSubject("Pengajuan PHK Pegawai Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setDescription("Rekapitulasi Data Pengajuan PHK Pegawai Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setKeywords("Pengajuan, PHK, Pegawai, Auliya");

        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', strtoupper('Pengajuan PHK Pegawai'))
        ->setCellValue('A2', strtoupper('SIT Auliya'))
        ->setCellValue('A4', 'No')
        ->setCellValue('B4', 'Nama Pegawai')
        ->setCellValue('C4', 'NIPY')
        ->setCellValue('D4', 'Tempat Lahir')
        ->setCellValue('E4', 'Tanggal Lahir')
        ->setCellValue('F4', 'Unit')
        ->setCellValue('G4', 'Masa Kerja')
        ->setCellValue('H4', 'Tanggal Pengajuan')
        ->setCellValue('I4', 'Alasan PHK')
        ->setCellValue('J4', 'Persetujuan');

        $kolom = 5;
        $no = 1;
        $max_kolom = count($phk)+$kolom-1;
        foreach($phk as $p) {
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$kolom, $no++)
            ->setCellValue('B'.$kolom, $p->pegawai->name)
            ->setCellValueExplicit('C'.$kolom, $p->pegawai->nip ? strval($p->pegawai->nip) : '', DataType::TYPE_STRING)
            ->setCellValue('D'.$kolom, $p->pegawai->birth_place)
            ->setCellValue('E'.$kolom, Date::parse($p->pegawai->birth_date)->format('Y-m-d'))
            ->setCellValue('F'.$kolom, $p->pegawai->units()->count() > 0 ? implode(', ',$p->pegawai->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('name')->toArray()) : '')
            ->setCellValue('G'.$kolom, $p->pegawai->yearsOfService)
            ->setCellValue('H'.$kolom, Date::parse($p->created_at)->format('Y-m-d'))
            ->setCellValue('I'.$kolom, $p->alasan->reason)
            ->setCellValue('J'.$kolom, Date::parse($p->director_acc_time)->format('Y-m-d'));

            $kolom++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Pengajuan PHK Pegawai');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(22);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);

        $styleArray = [
            'font' => [
                'size' => 14,
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ]
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArray);

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
        $spreadsheet->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A5:A'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C5:C'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Set the number format mask so that the excel timestamp will be displayed as a human-readable date/time
        $spreadsheet->getActiveSheet()->getStyle('E5:E'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        $spreadsheet->getActiveSheet()->getStyle('H5:H'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        $spreadsheet->getActiveSheet()->getStyle('J5:J'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

        $spreadsheet->getActiveSheet()->getStyle('I5:I'.$max_kolom)->getAlignment()
        ->setWrapText(true);

        $spreadsheet->getActiveSheet()->getStyle('C5:C'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('E5:H'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('J5:J'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $styleArray = [
            'font' => [
                'size' => 12
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A5:J'.$max_kolom)->applyFromArray($styleArray);

        // $writer = new Xls($spreadsheet);

        // header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="pengajuan-phk-pegawai-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xls"');
        // header('Cache-Control: max-age=0');

        // $writer->save('php://output');

        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

        $headers = [
            'Cache-Control' => 'max-age=0',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="pengajuan-phk-pegawai-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xlsx"',
        ];

        return response()->stream(function()use($writer){
            $writer->save('php://output');
        }, 200, $headers);
    }
}

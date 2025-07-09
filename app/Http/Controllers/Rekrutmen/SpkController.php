<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Rekrutmen\Spk;
use App\Models\Setting;

class SpkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $counter = Setting::where('name','work_agreement_counter')->first();

        if(in_array($role,['pembinayys','ketuayys','am'])){
            $spk = Spk::whereNotNull(['period_start','period_end']);
        }
        else{
            $spk = new Spk();
        }

        $spk = $spk->aktif()->orderBy('created_at','desc')->get();

        if(in_array($role,['etm','am','aspv']))
            $folder = $role;
        else $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.spk_index', compact('counter','spk'));
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
        $spk = $request->id ? Spk::find($request->id) : null;

        if($spk){
            return view('kepegawaian.'.$request->user()->role->name.'.spk_ubah', compact('spk'));
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
        $this->validate($request, [
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start'
        ]);

        $spk = $request->id ? Spk::aktif()->find($request->id) : null;

        if($spk){
            //$spk->reference_number = $request->number;
            if(!$spk->reference_number){
                $counter = Setting::where('name','work_agreement_counter')->first();
                $work_agreement_counter = ($counter->value)+1;
                $month = Date::now('Asia/Jakarta')->format('m');
                $year = Date::now('Asia/Jakarta')->format('y');
                $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
                $returnValue = '';
                while ($month > 0) {
                    foreach ($map as $roman => $int) {
                        if($month >= $int) {
                            $month -= $int;
                            $returnValue .= $roman;
                            break;
                        }
                    }
                }
                $roman_month = $returnValue;
                $number = ($work_agreement_counter > 999) ? $work_agreement_counter : sprintf('%03d',$work_agreement_counter);
                $spk->reference_number = $number.'/SPK-PTT/YYS/'.$roman_month.'/'.$year;
                $counter->value = $work_agreement_counter;
                $counter->save();
            }
            $spk->period_start = Date::parse($request->period_start);
            $spk->period_end = Date::parse($request->period_end);
            $spk->save();

            Session::flash('success','Data SPK '. $spk->pegawai->name .' berhasil diatur');
        }

        return redirect()->route('spk.index');
    }

    /**
     * Update the selected resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        $update = Spk::aktif()->update([
            'period_start' => Date::parse($request->input_period_start),
            'period_end' => Date::parse($request->input_period_end)
        ]);

        if($update){
            $spk = Spk::aktif()->whereNull('reference_number')->orderBy('employee_name')->get();
            foreach($spk as $s){
                $counter = Setting::where('name','work_agreement_counter')->first();
                $work_agreement_counter = ($counter->value)+1;
                $month = Date::now('Asia/Jakarta')->format('m');
                $year = Date::now('Asia/Jakarta')->format('y');
                $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
                $returnValue = '';
                while ($month > 0) {
                    foreach ($map as $roman => $int) {
                        if($month >= $int) {
                            $month -= $int;
                            $returnValue .= $roman;
                            break;
                        }
                    }
                }
                $roman_month = $returnValue;
                $number = ($work_agreement_counter > 999) ? $work_agreement_counter : sprintf('%03d',$work_agreement_counter);
                $s->reference_number = $number.'/SPK-PTT/YYS/'.$roman_month.'/'.$year;
                $s->save();
                $counter->value = $work_agreement_counter;
                $counter->save();
            }

            $count = Spk::aktif()->count();
            Session::flash('success', $count." data SPK berhasil diatur.");
        }

        return redirect()->route('spk.index');
    }

    /**
     * Reset the selected resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $update = Setting::where('name','work_agreement_counter')->update(['value' => 0]);

        if($update){
            Session::flash('success', "Nomor SPK berhasil di-reset.");
        }

        return redirect()->route('spk.index');
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
    public function export(Request $request)
    {
        $spk = Spk::aktif()->whereNotNull(['period_start','period_end'])->orderBy('created_at','desc')->get();

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('SIT Auliya')
        ->setLastModifiedBy($request->user()->pegawai->name)
        ->setTitle("Data Perjanjian Kerja Pegawai Tidak Tetap Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setSubject("Perjanjian Kerja Pegawai Tidak Tetap Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setDescription("Rekapitulasi Data Perjanjian Kerja Pegawai Tidak Tetap Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setKeywords("Perjanjian, Kerja, SPK, Pegawai, PTT, Auliya");

        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'No')
        ->setCellValue('B1', 'Nama Pegawai')
        ->setCellValue('C1', 'Alamat')
        ->setCellValue('D1', 'Unit')
        ->setCellValue('E1', 'Penempatan')
        ->setCellValue('F1', 'Status Kepegawaian')
        ->setCellValue('G1', 'Nomor SPK')
        ->setCellValue('H1', 'Tanggal SPK')
        ->setCellValue('I1', 'Tanggal Awal Masa Kerja')
        ->setCellValue('J1', 'Tanggal Akhir Masa Kerja');

        $kolom = 2;
        $no = 1;
        $max_kolom = count($spk)+1;
        foreach($spk as $s) {
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$kolom, $no++)
            ->setCellValue('B'.$kolom, $s->employee_name)
            ->setCellValue('C'.$kolom, $s->employee_address)
            ->setCellValue('D'.$kolom, $s->pegawai && $s->pegawai->units()->count() > 0 ? implode(', ',$s->pegawai->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('show_name')->toArray()) : '')
            ->setCellValue('E'.$kolom, $s->pegawai && $s->pegawai->units()->count() > 0 ? implode(', ',$s->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '')
            ->setCellValue('F'.$kolom, $s->employee_status)
            ->setCellValue('G'.$kolom, $s->reference_number)
            ->setCellValue('H'.$kolom, Date::parse($s->updated_at)->format('Y-m-d'))
            ->setCellValue('I'.$kolom, Date::parse($s->period_start)->format('Y-m-d'))
            ->setCellValue('J'.$kolom, Date::parse($s->period_end)->format('Y-m-d'));

            $kolom++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Perjanjian Kerja PTT');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(95);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(30);

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

        $styleArray = [
            'font' => [
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
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2:A'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set the number format mask so that the excel timestamp will be displayed as a human-readable date/time
        $spreadsheet->getActiveSheet()->getStyle('H1:H'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        $spreadsheet->getActiveSheet()->getStyle('I1:I'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        $spreadsheet->getActiveSheet()->getStyle('J1:J'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

        $spreadsheet->getActiveSheet()->getStyle('H2:J'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A2:J'.$max_kolom)->applyFromArray($styleArray);

        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

        $headers = [
            'Cache-Control' => 'max-age=0',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="perjanjian-kerja-ptt-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xlsx"',
        ];

        return response()->stream(function()use($writer){
            $writer->save('php://output');
        }, 200, $headers);
    }
}
=======
<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Rekrutmen\Spk;
use App\Models\Setting;

class SpkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $counter = Setting::where('name','work_agreement_counter')->first();

        if(in_array($role,['pembinayys','ketuayys','am'])){
            $spk = Spk::whereNotNull(['period_start','period_end']);
        }
        else{
            $spk = new Spk();
        }

        $spk = $spk->aktif()->orderBy('created_at','desc')->get();

        if(in_array($role,['etm','am','aspv']))
            $folder = $role;
        else $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.spk_index', compact('counter','spk'));
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
        $spk = $request->id ? Spk::find($request->id) : null;

        if($spk){
            return view('kepegawaian.'.$request->user()->role->name.'.spk_ubah', compact('spk'));
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
        $this->validate($request, [
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start'
        ]);

        $spk = $request->id ? Spk::aktif()->find($request->id) : null;

        if($spk){
            //$spk->reference_number = $request->number;
            if(!$spk->reference_number){
                $counter = Setting::where('name','work_agreement_counter')->first();
                $work_agreement_counter = ($counter->value)+1;
                $month = Date::now('Asia/Jakarta')->format('m');
                $year = Date::now('Asia/Jakarta')->format('y');
                $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
                $returnValue = '';
                while ($month > 0) {
                    foreach ($map as $roman => $int) {
                        if($month >= $int) {
                            $month -= $int;
                            $returnValue .= $roman;
                            break;
                        }
                    }
                }
                $roman_month = $returnValue;
                $number = ($work_agreement_counter > 999) ? $work_agreement_counter : sprintf('%03d',$work_agreement_counter);
                $spk->reference_number = $number.'/SPK-PTT/YYS/'.$roman_month.'/'.$year;
                $counter->value = $work_agreement_counter;
                $counter->save();
            }
            $spk->period_start = Date::parse($request->period_start);
            $spk->period_end = Date::parse($request->period_end);
            $spk->save();

            Session::flash('success','Data SPK '. $spk->pegawai->name .' berhasil diatur');
        }

        return redirect()->route('spk.index');
    }

    /**
     * Update the selected resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        $update = Spk::aktif()->update([
            'period_start' => Date::parse($request->input_period_start),
            'period_end' => Date::parse($request->input_period_end)
        ]);

        if($update){
            $spk = Spk::aktif()->whereNull('reference_number')->orderBy('employee_name')->get();
            foreach($spk as $s){
                $counter = Setting::where('name','work_agreement_counter')->first();
                $work_agreement_counter = ($counter->value)+1;
                $month = Date::now('Asia/Jakarta')->format('m');
                $year = Date::now('Asia/Jakarta')->format('y');
                $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
                $returnValue = '';
                while ($month > 0) {
                    foreach ($map as $roman => $int) {
                        if($month >= $int) {
                            $month -= $int;
                            $returnValue .= $roman;
                            break;
                        }
                    }
                }
                $roman_month = $returnValue;
                $number = ($work_agreement_counter > 999) ? $work_agreement_counter : sprintf('%03d',$work_agreement_counter);
                $s->reference_number = $number.'/SPK-PTT/YYS/'.$roman_month.'/'.$year;
                $s->save();
                $counter->value = $work_agreement_counter;
                $counter->save();
            }

            $count = Spk::aktif()->count();
            Session::flash('success', $count." data SPK berhasil diatur.");
        }

        return redirect()->route('spk.index');
    }

    /**
     * Reset the selected resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $update = Setting::where('name','work_agreement_counter')->update(['value' => 0]);

        if($update){
            Session::flash('success', "Nomor SPK berhasil di-reset.");
        }

        return redirect()->route('spk.index');
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
    public function export(Request $request)
    {
        $spk = Spk::aktif()->whereNotNull(['period_start','period_end'])->orderBy('created_at','desc')->get();

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('SIT Auliya')
        ->setLastModifiedBy($request->user()->pegawai->name)
        ->setTitle("Data Perjanjian Kerja Pegawai Tidak Tetap Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setSubject("Perjanjian Kerja Pegawai Tidak Tetap Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setDescription("Rekapitulasi Data Perjanjian Kerja Pegawai Tidak Tetap Auliya ".Date::now('Asia/Jakarta')->format('d.m.Y'))
        ->setKeywords("Perjanjian, Kerja, SPK, Pegawai, PTT, Auliya");

        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'No')
        ->setCellValue('B1', 'Nama Pegawai')
        ->setCellValue('C1', 'Alamat')
        ->setCellValue('D1', 'Unit')
        ->setCellValue('E1', 'Penempatan')
        ->setCellValue('F1', 'Status Kepegawaian')
        ->setCellValue('G1', 'Nomor SPK')
        ->setCellValue('H1', 'Tanggal SPK')
        ->setCellValue('I1', 'Tanggal Awal Masa Kerja')
        ->setCellValue('J1', 'Tanggal Akhir Masa Kerja');

        $kolom = 2;
        $no = 1;
        $max_kolom = count($spk)+1;
        foreach($spk as $s) {
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$kolom, $no++)
            ->setCellValue('B'.$kolom, $s->employee_name)
            ->setCellValue('C'.$kolom, $s->employee_address)
            ->setCellValue('D'.$kolom, $s->pegawai && $s->pegawai->units()->count() > 0 ? implode(', ',$s->pegawai->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('show_name')->toArray()) : '')
            ->setCellValue('E'.$kolom, $s->pegawai && $s->pegawai->units()->count() > 0 ? implode(', ',$s->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '')
            ->setCellValue('F'.$kolom, $s->employee_status)
            ->setCellValue('G'.$kolom, $s->reference_number)
            ->setCellValue('H'.$kolom, Date::parse($s->updated_at)->format('Y-m-d'))
            ->setCellValue('I'.$kolom, Date::parse($s->period_start)->format('Y-m-d'))
            ->setCellValue('J'.$kolom, Date::parse($s->period_end)->format('Y-m-d'));

            $kolom++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Perjanjian Kerja PTT');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(95);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(30);

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

        $styleArray = [
            'font' => [
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
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2:A'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set the number format mask so that the excel timestamp will be displayed as a human-readable date/time
        $spreadsheet->getActiveSheet()->getStyle('H1:H'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        $spreadsheet->getActiveSheet()->getStyle('I1:I'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
        $spreadsheet->getActiveSheet()->getStyle('J1:J'.$max_kolom)->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

        $spreadsheet->getActiveSheet()->getStyle('H2:J'.$max_kolom)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A2:J'.$max_kolom)->applyFromArray($styleArray);

        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

        $headers = [
            'Cache-Control' => 'max-age=0',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="perjanjian-kerja-ptt-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xlsx"',
        ];

        return response()->stream(function()use($writer){
            $writer->save('php://output');
        }, 200, $headers);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

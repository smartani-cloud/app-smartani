<?php

namespace App\Http\Controllers\Psb;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\ListingCandidateStudent;
use App\Http\Services\Psb\ListingDaftarUlang;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Kbm\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bank;
use App\Models\Level;
use App\Models\Psb\RegisterCounter;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\Siswa;
use App\Models\Pembayaran\BmsDeduction;

use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminPsbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        return view('psb.admin.index',compact('levels','level'));
    }

    public function data($link)
    {
        //
        if($link == 'formulir-terisi'){
            $title = 'Formulir Terisi';
            $status_id = 1;
        }
        else if($link == 'saving-seat'){
            $title = 'Biaya Observasi';
            $status_id = 2;
        }
        else if($link == 'wawancara'){
            $title = 'Wawancara';
            $status_id = 3;
        }
        else if($link == 'diterima'){
            $title = 'Diterima';
            $status_id = 4;
        }
        else if($link == 'bayar-daftar-ulang'){
            $title = 'Bayar Daftar Ulang';
            $status_id = 5;
        }
        else if($link == 'dicadangkan'){
            $title = 'Dicadangkan';
            $status_id = 6;
        }
        else if($link == 'batal-daftar-ulang'){
            $title = 'Batal Daftar Ulang';
            $status_id = 7;
        }
        else if($link == 'peresmian-siswa'){
            $title = 'Peresmian Siswa';
            $status_id = 5;
        }
        else{
            return redirect('/kependidikan/psb/formulir-terisi');
        }
 

        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if(auth()->user()->pegawai->unit_id  == 5){
            $calons = CalonSiswa::where('status_id',$status_id)->get();
        }else{
            $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
        }


        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link'));
    }

    public function find($link, Request $request)
    {
        //
        if($link == 'formulir-terisi'){
            $title = 'Formulir Terisi';
            $status_id = 1;
        }
        else if($link == 'saving-seat'){
            $title = 'Biaya Observasi';
            $status_id = 2;
        }
        else if($link == 'wawancara'){
            $title = 'Wawancara';
            $status_id = 3;
        }
        else if($link == 'diterima'){
            $title = 'Diterima';
            $status_id = 4;
        }
        else if($link == 'bayar-daftar-ulang'){
            $title = 'Bayar Daftar Ulang';
            $status_id = 5;
        }
        else if($link == 'dicadangkan'){
            $title = 'Dicadangkan';
            $status_id = 6;
        }
        else if($link == 'batal-daftar-ulang'){
            $title = 'Batal Daftar Ulang';
            $status_id = 7;
        }
        else{
            return redirect('/kependidikan/psb/formulir-terisi');
        }


        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }


        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bayarFormulir(Request $request)
    {
        //
        
    }

    public function wawancaraLink(Request $request)
    {
        //
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Kirim link wawancara gagal');

        $calons->interview_type = $request->tipe_wawancara;
        if($request->tipe_wawancara == 1){
            $calons->link = $request->link;
        }else{
            $calons->link = null;
        }
        $calons->interview_date = $request->interview_date;
        $calons->interview_time = $request->interview_time;
        // $calons->observation_link = $request->observation_link;
        // $calons->observation_date = $request->observation_date;
        // $calons->observation_time = $request->observation_time;

        //$bank = $request->bank ? Bank::find($request->bank) : null;
        //$calons->bank_id = $bank ? $bank->id : null;
        $calons->account_number = $request->account_number;
        //$calons->account_holder = $request->account_holder;
        $calons->save();

        return redirect()->back()->with('success', 'Kirim link wawancara berhasil');

    }

    public function wawancaraDone(Request $request)
    {
        //
        // dd($request);
        $calons = CalonSiswa::find($request->id);
        if(!$calons) return redirect()->back()->with('error', 'Penerimaan gagal');

        $existStatus = $calons->status_id;

        $calons->status_id = 4;
        $calons->acc_employee_id = $request->user()->pegawai->id;
        $calons->acc_time = Date::now('Asia/Jakarta');
        $calons->save();
        
        $calons->fresh();

        RegisterCounterService::addCounter($calons->id,'accepted');

        if($existStatus == 6){
            RegisterCounterService::diffCounter($calons->id,'reserved');
        }

        return redirect()->back()->with('success', 'Penerimaan berhasil');
    }
    
    public function diterima(Request $request)
    {
        //
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Penerimaan siswa gagal');

        $calons->status_id = 4;
        $calons->save();
        
        $calons->fresh();
        
        $counter = RegisterCounter::where('unit_id',$calons->unit_id)->where('academic_year_id',$calons->academic_year_id)->first();

        if($calons->origin_school == 'SIT Auliya'){
            $counter->accepted_intern = $counter->accepted_intern + 1;
            $counter->save();
        }else{
            $counter->accepted_extern = $counter->accepted_extern + 1;
            $counter->save();
        }
        
        return redirect()->back()->with('success', 'Calon siswa berhasil diterima');

    }

    public function dicadangkan(Request $request)
    {
        //
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Pencadangan calon siswa gagal');

        $calons->status_id = 6;
        $calons->save();
        
        $calons->fresh();
        
        RegisterCounterService::addCounter($calons->id,'reserved');

        return redirect()->back()->with('success', 'Calon siswa berhasil dicadangkan');

    }

    public function bayarDaftarUlang(Request $request)
    {
        //

    }

    public function batalDaftarUlang(Request $request)
    {
        //
        $calons = CalonSiswa::find($request->id);
        // dd($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Pembatalan bayar daftar ulang gagal');

        $calons->last_status_id = $calons->status_id;
        $calons->status_id = 7;
        $calons->save();
        
        $calons->fresh();
        
        RegisterCounterService::addCounter($calons->id,'canceled');
        
        return redirect()->back()->with('success', 'Bayar daftar ulang berhasil dibatalkan');

    }

    public function revertBatalDaftarUlang(Request $request)
    {
        //
        $calons = CalonSiswa::find($request->id);
        // dd($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Urungkan pembatalan bayar daftar ulang gagal');

        if($calons->last_status_id){
            $calons->status_id = $calons->last_status_id;
            $calons->last_status_id = null;
        }
        else{
            $calons->status_id = $calons->year_spp && $calons->month_spp ? 5 : 4;
        }
        $calons->save();
        
        $calons->fresh();
        
        RegisterCounterService::diffCounter($calons->id,'canceled');
        
        return redirect()->back()->with('success', 'Pembatalan bayar daftar ulang berhasil diurungkan');

    }

    public function formulirTerisi(Request $request)
    {
        $link = 'formulir-terisi';
        $title = 'Formulir Terisi';
        $status_id = 1;

        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);

        $banks = Bank::select('id','name','short_name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','banks'));
    }

    public function formulirTerisiFind(Request $request)
    {
        $link = 'formulir-terisi';
        $title = 'Formulir Terisi';
        $status_id = 1;
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }


        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link'));
    }
    
    public function savingSeatFind(Request $request)
    {
        $link = 'saving-seat';
        $title = 'Biaya Observasi';
        $status_id = 2;
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }


        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link'));
    }

    public function linkDiterima(Request $request)
    {
        $link = 'diterima';
        $title = 'Diterima';
        $status_id = 4;
        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','deductions'));
    }

    public function linkDiterimaFind(Request $request)
    {
        $link = 'diterima';
        $title = 'Diterima';
        $status_id = 4;
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link','deductions'));
    }
    
    public function linkDicadangkan(Request $request)
    {
        $link = 'dicadangkan';
        $title = 'Dicadangkan';
        $status_id = 6;
        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','deductions'));
    }

    public function linkDicadangkanFind(Request $request)
    {
        $link = 'dicadangkan';
        $title = 'Dicadangkan';
        $status_id = 6;
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link','deductions'));
    }

    public function linkBatalDaftarUlang(Request $request)
    {
        $link = 'batal-daftar-ulang';
        $title = 'Batal Daftar Ulang';
        $status_id = 7;
        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','deductions'));
    }

    public function linkBatalDaftarUlangFind(Request $request)
    {
        $link = 'batal-daftar-ulang';
        $title = 'Batal Daftar Ulang';
        $status_id = 7;
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link','deductions'));
    }

    public function linkPeresmianSiswa(Request $request)
    {
        $link = 'peresmian-siswa';
        $title = 'Peresmian Siswa';
        $status_id = 5;
        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','deductions'));
    }

    public function linkPeresmianSiswaFind(Request $request)
    {
        $link = 'peresmian-siswa';
        $title = 'Peresmian Siswa';
        $status_id = 5;
        $unit = Auth::user()->pegawai->unit_id;
        if($unit == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit)->get();
        }
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->level == 'semua'){
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->get();
            }
        }else{
            $level = $request->level;
            if(auth()->user()->pegawai->unit_id  == 5){
                $calons = CalonSiswa::where('status_id',$status_id)->where('level_id',$request->level)->get();
            }else{
                $calons = CalonSiswa::where('status_id',$status_id)->where('unit_id',$unit_id)->where('level_id',$request->level)->get();
            }
        }
        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('levels','level','title','calons','status_id','link','deductions'));
    }

    public function chart(Request $request)
    {

        $data = array();
        $year_list = TahunAjaran::orderBy('academic_year_start','DESC')->get();
        // dd($year_list);

        if($request->year){
            $year = $request->year;
            $unit_id = $request->unit_id?$request->unit_id:auth()->user()->pegawai->unit_id;
            $type = $request->type;
            $data = $this->getCounter($unit_id,$year,$type);
        }else{
            $year_selected = TahunAjaran::aktif()->first();
            $year = $year_selected->id;
            $type = 1;
            if(auth()->user()->pegawai->unit_id == 5){
                $unit_id = 1;
            }else{
                $unit_id = auth()->user()->pegawai->unit_id;
            }
            $data = $this->getCounter($unit_id,$year,1);
        }
        
        return view('psb.admin.chart',compact('data','year_list','year','unit_id','type'));
    }

    public function getCounter($unit, $year, $asal)
    {
        $data = array();

        $list = RegisterCounter::where('unit_id',$unit)->where('academic_year_id',$year)->first();
        // $list = RegisterCounter::where('unit_id',4)->where('academic_year_id',7)->first();

        if($list){
            // dd($list);
            if($asal == 1){
                $data = [
                    $list->register_intern,
                    $list->saving_seat_intern,
                    $list->interview_intern,
                    $list->accepted_intern,
                    $list->reapply_intern,
                ];
            }else{
                $data = [
                    $list->register_extern,
                    $list->saving_seat_extern,
                    $list->interview_extern,
                    $list->accepted_extern,
                    $list->reapply_extern,
                ];
            }
        }else{
            $data = [
                0,
                0,
                0,
                0,
                0,
            ];
        }
        return $data;
    }

    public function export(Request $request,$status)
    {
        $link = $status;
        if($link == 'formulir-terisi'){
            $title = 'Formulir Terisi';
            $status_id = 1;
        }
        else if($link == 'saving-seat'){
            $title = 'Biaya Observasi';
            $status_id = 2;
        }
        else if($link == 'wawancara'){
            $title = 'Wawancara';
            $status_id = 3;
        }
        else if($link == 'diterima'){
            $title = 'Diterima';
            $status_id = 4;
        }
        else if($link == 'peresmian-siswa'){
            $title = 'Peresmian Siswa';
            $status_id = 5;
        }
        else if($link == 'dicadangkan'){
            $title = 'Dicadangkan';
            $status_id = 6;
        }
        else if($link == 'batal-daftar-ulang'){
            $title = 'Batal Daftar Ulang';
            $status_id = 7;
        }
        else if($link == 'belum-lunas'){
            $title = 'Belum Lunas';
            $status_id = 0;
            $bayar = 'sebagian';
            if(isset($request->bayar) && $request->bayar != 'sebagian'){
                if(in_array($request->bayar,['belum'])) $bayar = $request->bayar;
            }
            if($bayar == 'sebagian'){
                $title .= ' Membayar '.ucwords($bayar);
            }
        }
        else if($link == 'sudah-lunas'){
            $title = 'Sudah Lunas';
            $status_id = 1;
            $bayar = null;
        }
        else{
            return redirect('/kependidikan/psb/formulir-terisi');
        }

        if(in_array($link,['belum-lunas','sudah-lunas'])){
            $siswas = ListingDaftarUlang::list($request->level, $request->year, $status_id, $bayar);
        }
        else{
            $siswas = ListingCandidateStudent::list($request->level, $request->year, $status_id);
        }
        // dd($siswas);

        // init sheet
        $spreadsheet = new Spreadsheet;

        // init column & row
        $column_init = 'A';
        $row_init = 1;
        $sheet = 0;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheet);

        $list_judul = [
            'ID',
            'No Pendaftaran',
            'Program',
            'Tanggal Daftar'
        ];
        if(in_array($status_id,[1,2,3,4])){
            $list_judul = array_merge($list_judul,[
                'Baru/Pindahan'
            ]);
        }
        $list_judul = array_merge($list_judul,[
            'Tahun Pelajaran',
            'Tingkat Kelas',
            'NIPD',
            'NISN',
            'NIK',
            'Nama',
            'Nama Panggilan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Anak Ke',
            'Status Anak',
            'Alamat',
            'No',
            'RT',
            'RW',
            'Wilayah',
            'Nama Ayah',
            'NIK Ayah',
            'HP Ayah',
            'Email Ayah',
            'Pekerjaan Ayah',
            'Jabatan Ayah',
            'Telp Kantor Ayah',
            'Alamat Kantor Ayah',
            'Gaji Ayah',
            'Nama Ibu',
            'NIK Ibu',
            'HP Ibu',
            'HP Email Ibu',
            'Pekerjaan Ibu',
            'Jabatan Ibu',
            'Telp Kantor Ibu',
            'Alamat Kantor Ibu',
            'Gaji Ibu',
            'NIP (Orang tua yang bekerja di Auliya)',
            'Alamat Orang Tua',
            'HP Alternatif',
            'Nama Wali',
            'NIK Wali',
            'HP Wali',
            'HP Email Wali',
            'Pekerjaan Wali',
            'Jabatan Wali',
            'Telp Kantor Wali',
            'Alamat Kantor Wali',
            'Gaji Wali',
            'Alamat Wali',
            'Asal Sekolah',
            'Saudara Kandung',
            'Nama Saudara',
            'Info Dari',
            'Nama',
            'Posisi',
        ]);

        $row = $row_init;
        $column = $column_init;
        foreach($list_judul as $judul){
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $judul);
            $column++;
        }

        foreach($siswas as $siswa){
            if(in_array($link,['belum-lunas','sudah-lunas'])){
                $siswa = $siswa->siswa;
            }
            if(!in_array($link,['belum-lunas','sudah-lunas']) || (in_array($link,['belum-lunas','sudah-lunas']) && $siswa->status_id == 4)){
                $row++;
                $column = $column_init;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->id);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->reg_number);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->unit->name);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, date('d-m-Y', strtotime($siswa->created_at)));

                if(in_array($status_id,[1,2,3,4])){                
                    $column++;
                    $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->statusSiswa ? $siswa->statusSiswa->status : '-');

                    $column++;
                    $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->semester ? $siswa->semester->semester_id : '-');
                
                }
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->level_id?$siswa->level->level:'-');
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nis);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nisn);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValueExplicit($column.$row, $siswa->nik ? strval($siswa->nik) : '', DataType::TYPE_STRING);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_name);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->student_nickname);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->birth_place);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->birth_date);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->gender_id?ucwords($siswa->jeniskelamin->name):'');
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->religion_id?$siswa->agama->name:'-');
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->child_of);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->family_status);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->address);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->address_number);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->rt);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->rw);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->wilayah ? $siswa->wilayah->name.', '.$siswa->wilayah->kecamatanName().', '.$siswa->wilayah->kabupatenName().', '.$siswa->wilayah->provinsiName() : '');
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_name);
                
                $column++;
                $value = strlen($siswa->orangtua->father_nik)>120?decrypt($siswa->orangtua->father_nik):$siswa->orangtua->father_nik;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $value = strlen($siswa->orangtua->father_phone)>120?decrypt($siswa->orangtua->father_phone):$siswa->orangtua->father_phone;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_email);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_job);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_position);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_phone_office);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_job_address);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->father_salary);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_name);
                
                $column++;
                $value = strlen($siswa->orangtua->mother_nik)>120?decrypt($siswa->orangtua->mother_nik):$siswa->orangtua->mother_nik;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $value = strlen($siswa->orangtua->mother_phone)>120?decrypt($siswa->orangtua->mother_phone):$siswa->orangtua->mother_phone;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_email);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_job);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_position);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_phone_office);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_job_address);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->mother_salary);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->employee_id);
                
                $column++;
                $value = strlen($siswa->orangtua->parent_address)>120?decrypt($siswa->orangtua->parent_address):$siswa->orangtua->parent_address;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $value = strlen($siswa->orangtua->parent_phone_number)>120?decrypt($siswa->orangtua->parent_phone_number):$siswa->orangtua->parent_phone_number;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_name);
                
                $column++;
                $value = strlen($siswa->orangtua->guardian_nik)>120?decrypt($siswa->orangtua->guardian_nik):$siswa->orangtua->guardian_nik;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $value = strlen($siswa->orangtua->guardian_phone_number)>120?decrypt($siswa->orangtua->guardian_phone_number):$siswa->orangtua->guardian_phone_number;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $value);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_email);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_job);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_position);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_phone_office);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_job_address);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_salary);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->orangtua->guardian_address);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->origin_school!="Sekolah Islam Terpadu Auliya"?$siswa->origin_school_address:"Sekolah Islam Terpadu Auliya");
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->sibling_name?$siswa->sibling_name:'-');
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, ($siswa->sibling_level_id)?$siswa->levelsaudara->level:'-');
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->info_from);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->info_name);
                
                $column++;
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($column.$row, $siswa->position);
            }
        }
        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

        $headers = [
            'Cache-Control' => 'max-age=0',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="data-calon-siswa'.($title ? '-'.str_replace(" ", "-", strtolower($title)) : null).'-'.Date::now('Asia/Jakarta')->format('Y-m-d').'.xlsx"',
        ];

        return response()->stream(function()use($writer){
            $writer->save('php://output');
        }, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers\Pelatihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use File;
use LaravelPDF as PDF;
use Carbon\Carbon;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pelatihan\Pelatihan;
use App\Models\Penempatan\Jabatan;

class SertifikatPelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $pegawai = $request->user()->pegawai;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        if($request->tahunajaran && $request->tahunajaran != 'semua'){
            $tahunajaran = str_replace("-","/",$request->tahunajaran);
            if($tahunajaran != $aktif->academic_year){
                $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
            }

            if(!$aktif) return redirect()->route('pelatihan.saya.index');
        }
        else $request->tahunajaran = 'semua';

        if($request->tahunajaran == 'semua'){
            $aktif = 'semua';
            $pelatihan = Pelatihan::selesai();
        }
        else
            $pelatihan = $aktif->pelatihan()->selesai();

        $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($pegawai){
            $query->whereHas('jabatan',function($query)use($pegawai){
                $query->where([
                    'unit_id' => $pegawai->unit_id,
                    'position_id' => $pegawai->position_id
                ]);
            });
        })->whereHas('presensi',function($query)use($pegawai){
            $query->where([
                'employee_id' => $pegawai->id,
                'presence_status_id' => 1,
            ]);
        })->whereNotNull([
            'education_acc_id',
            'education_acc_time'
        ])->where('education_acc_status_id',1)->orderBy('date','desc')->get();

        $tahun_tersedia = $pelatihan->pluck('academic_year_id')->unique()->all();

        $tahun = TahunAjaran::whereIn('id',$tahun_tersedia)->orderBy('academic_year')->get();
        
        return view('kepegawaian.read-only.pelatihan_sertifikat_index', compact('aktif','tahun','pelatihan'));
    }

    public function download(Request $request,$id)
    {
        $pegawai = $request->user()->pegawai;

        $pelatihan = Pelatihan::selesai()->whereHas('sasaran',function($query)use($pegawai){
            $query->whereHas('jabatan',function($query)use($pegawai){
                $query->where([
                    'unit_id' => $pegawai->unit_id,
                    'position_id' => $pegawai->position_id
                ]);
            });
        })->whereHas('presensi',function($query)use($pegawai){
            $query->where([
                'employee_id' => $pegawai->id,
                'presence_status_id' => 1
            ]);
        })->whereNotNull([
            'education_acc_id',
            'education_acc_time'
        ])->where([
            'id' => $id,
            'education_acc_status_id' => 1,
        ])->orderBy('date','desc')->first();

        if($pelatihan){

            $pengaturan = collect([
                'margin_nomor' => 66.6,
                'font_size_nomor' => 18,
                'font_size_untuk' => 17,
                'margin_nama' => 5,
                'font_size_nama' => 42,
                'margin_pelatihan' => 7,
                'font_size_pelatihan' => 17,
                'margin_direktur' => 10,
                'margin_nama_direktur' => 23
            ]);

            $filename = 'sertifikat-'.$pelatihan->id.'-'.$pegawai->id;

            $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf::setHeaderCallback(function($pdf){
                // get the current page break margin
                $bMargin = PDF::getBreakMargin();
                // get current auto-page-break mode
                $auto_page_break = PDF::getAutoPageBreak();
                // disable auto-page-break
                PDF::SetAutoPageBreak(false, 0);
                PDF::setCellPaddings(0,0,0,0);
                // set background image
                $img_file = public_path('img/ecertificate/template.png');
                if(!File::exists($img_file)){
                    // check in server
                    $img_file = base_path('../sekolah.digiyok.com/img/ecertificate/template.png');
                }
                PDF::Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
                // restore auto-page-break status
                PDF::SetAutoPageBreak($auto_page_break, $bMargin);
                // set the starting point for the page content
                PDF::setPageMark();
            });

            // set document information
            $pdf::SetCreator(PDF_CREATOR);
            $pdf::SetAuthor($pegawai->name);
            $pdf::SetTitle('Sertifikat Kompetensi - '.$pelatihan->name.' - '.$pegawai->name);
            $pdf::SetSubject('Sertifikat Kompetensi');
            $pdf::SetKeywords('PDF, e-certificate, certificate, '.PDF_CREATOR);

            // set margins
            $pdf::SetMargins(PDF_MARGIN_RIGHT, $pengaturan['margin_nomor'], PDF_MARGIN_RIGHT);
            $pdf::SetHeaderMargin(0);
            $pdf::SetFooterMargin(0);

            // remove default footer
            $pdf::setPrintFooter(false);
            $pdf::SetAutoPageBreak(TRUE, 0);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf::setLanguageArray($l);
            }

            // ---------------------------------------------------------

            $pdf::AddPage();
            $pdf::SetTextColor(0,0,0);
            $pdf::SetFont('helvetica', 'B', $pengaturan['font_size_nomor']);
            $pdf::Write(0, 'NO. '.$pelatihan->number, '', 0, 'C', true, 0, false, false, 0);
            $pdf::SetFont('helvetica', '', $pengaturan['font_size_untuk']);
            $pdf::Write(0, 'Digiyok Sekolah memberikan sertifikat kepada:', '', 0, 'C', true, 0, false, false, 0);
            $pdf::Ln((int) $pengaturan['margin_nama']);
            $pdf::SetFont('raleway', '', $pengaturan['font_size_nama']);
            $pdf::Write(0, $pegawai->name, '', 0, 'C', true, 0, false, false, 0);
            $pdf::Ln((int) $pengaturan['margin_pelatihan']);
            $pdf::SetFont('helvetica', 'B', $pengaturan['font_size_pelatihan']);
            $pdf::Write(0, 'Atas Partisipasinya Sebagai Peserta Pelatihan', '', 0, 'C', true, 0, false, false, 0);
            $pdf::Write(0, '"'.strtoupper($pelatihan->name).'"', '', 0, 'C', true, 0, false, false, 0);
            $pdf::SetFont('helvetica', 'B', $pengaturan['font_size_pelatihan']);
            Carbon::setLocale('id');
            $pdf::Write(0, 'Pada Tanggal '.Carbon::parse($pelatihan->date)->format('j F Y'), '', 0, 'C', true, 0, false, false, 0);
            $pdf::Write(0, 'Dengan Pembicara '.$pelatihan->speaker_name, '', 0, 'C', true, 0, false, false, 0);
            $pdf::Ln((int) $pengaturan['margin_direktur']);
            $pdf::Write(0, 'Digiyok Sekolah,', '', 0, 'C', true, 0, false, false, 0);
            $pdf::Ln((int) $pengaturan['margin_nama_direktur']);
            
            // Direktur
            $jabatan = Jabatan::where('code','19')->first();
            $pejabat = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                $q->aktif();
            })->first()->pegawai;
            
            $pdf::Write(0, $pejabat ? $pejabat->name : 'Dr. Kurniasih Mufidayanti, M.Si.', '', 0, 'C', true, 0, false, false, 0);
            $pdf::Output($filename.'.pdf', 'I');
            
            ob_end_flush();
        }
    }
}

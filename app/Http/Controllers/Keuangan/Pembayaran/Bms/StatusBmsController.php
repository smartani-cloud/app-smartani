<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Mail;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Level;
use App\Models\Unit;

class StatusBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Status BMS';
        $this->route = 'bms.status';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa','alumni'])){
            $siswa = 'siswa';
        }

        $jenis = 'tunai';
        if(isset($request->jenis) && $request->jenis != 'tunai'){
            if(in_array($request->jenis,['berkala'])) $jenis = $request->jenis;
        }

        $register = 'semua';
        if(isset($request->register_filter) & $request->register_filter != 'semua'){
            if(in_array($request->register_filter,['semua','0','1'])) $register = $request->register_filter;
        }

        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';
        if($unit_id == 5){
            $levels = Level::all();
            // bms_paid > 0 = register_paid > 0
            $lists = $siswa == 'calon' ? BmsCalonSiswa::orderBy('unit_id','asc') : BMS::orderBy('unit_id','asc');
        }else{
            $lists = $siswa == 'calon' ? BmsCalonSiswa::where('unit_id',$unit_id)->orderBy('candidate_student_id','asc') : BMS::where('unit_id',$unit_id)->orderBy('student_id','asc');
            $levels = Level::where('unit_id',$unit_id);
        }
        $level = 'semua';
        $tahun_aktif = TahunAjaran::where('is_active',1)->first();

        if($siswa == 'alumni'){
            $lists = $lists->whereHas('siswa',function($q){
                $q->where('is_lulus',1);
            });
        }
        elseif($siswa != 'calon'){
            $lists = $lists->whereHas('siswa',function($q){
                $q->where('is_lulus',0);
            });
        }

        $lists = $jenis == 'berkala' ? $lists->whereHas('tipe',function($q){
            $q->where('bms_type','Berkala 1');
        }) : $lists->where(function($q){
            $q->whereHas('tipe',function($q){
                $q->where('bms_type','Tunai');
            })->orWhereNull('bms_type_id');
        });

        if($siswa == 'calon' && $register != 'semua'){
            $lists = $register == '0' ? $lists->where('register_paid',0) : $lists->where('register_paid','>',0);
        }

        $lists = $lists->get();

        $active = $this->active.($siswa ? (' '.((!isset($jenis) || $jenis != 'berkala') ? 'Tunai' : 'Berkala').' '.ucwords(($siswa == 'alumni' ? 'Siswa ' : null).$siswa.($siswa == 'calon' ? ' Siswa' : null))) : null);
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','lists','levels','level','unit_id','unit','tahun_aktif','siswa','jenis','register'));
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
    
    public function reminderEmailCreate(Request $request)
    {
        $data = null;
        if(isset($request->id)){
            $siswa = 'siswa';
            if(isset($request->siswa) && in_array($request->siswa,['calon','siswa','alumni'])){
                $siswa = $request->siswa;
            }

            if($siswa == 'calon'){
                $data = BmsCalonSiswa::where('bms_paid','>',0)->where('id',$request->id)->whereHas('siswa', function($q){
                    $q->has('virtualAccount');
                });
            }
            else{
                $data = BMS::where('id',$request->id)->whereHas('siswa', function($q){
                    $q->has('virtualAccount');
                });
                if($siswa == 'alumni'){
                    $data = $data->whereHas('siswa', function($q){
                        $q->where('is_lulus',1);
                    });
                }
                else{
                    $data = $data->whereHas('siswa', function($q){
                        $q->where('is_lulus',0);
                    });
                }
            }
            $data = $data->having('bms_remain','>',0)->first();
        }
        
        if($data){
            $identitas = $siswa == 'calon' ? $data->siswa : $data->siswa->identitas;
            if($identitas->orangtua && ($identitas->orangtua->father_email || $identitas->orangtua->mother_email || $identitas->orangtua->guardian_email)){
                $emailCol = collect();
                if($identitas->orangtua->father_email){
                    $emailCol->push('father_email');
                }
                if($identitas->orangtua->mother_email){
                    $emailCol->push('mother_email');
                }
                if($identitas->orangtua->guardian_email){
                    $emailCol->push('guardian_email');
                }
                
                $tahunAktif = TahunAjaran::where('is_active',1)->first();

                return view('keuangan.pembayaran.bms.email', compact('data','emailCol','tahunAktif','siswa'));
            }
            else return "Ups, alamat email orang tua/wali tidak ditemukan.";
        }
        else return "Ups, tidak dapat memuat data";
    }
    
    public function reminderEmail(Request $request,$siswa = null,$id)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa','alumni'])){
            $siswa = 'siswa';
        }

        if($siswa == 'calon'){
            $data = BmsCalonSiswa::where('bms_paid','>',0)->where('id',$id)->whereHas('siswa', function($q){
                $q->has('virtualAccount');
            });
        }
        else{
            $data = BMS::where('id',$id)->whereHas('siswa', function($q){
                $q->has('virtualAccount');
            });
            if($siswa == 'alumni'){
                $data = $data->whereHas('siswa', function($q){
                    $q->where('is_lulus',1);
                });
            }
            else{
                $data = $data->whereHas('siswa', function($q){
                    $q->where('is_lulus',0);
                });
            }
        }
        $data = $data->having('bms_remain','>',0)->first();

        // $data = BmsCalonSiswa::where('bms_type_id',2)->whereHas('siswa', function($q){
        //     $q->has('virtualAccount');
        // })->having('bms_remain','>',0)->first();
        
        if($data){
            $tahunAktif = TahunAjaran::aktif()->first();
            $semesterAktif = $tahunAktif->semester()->select('id','semester_id')->aktif()->first();

            $tagihan = $berkala = null;
            if($data->termin()->count() > 1){
                $relation = $siswa == 'alumni' ? 'siswa' : $siswa;
                foreach($data->termin()->{$relation}()->with('tahunPelajaran:id,academic_year_start')->get()->sortBy('tahunPelajaran.academic_year_start') as $key => $t){
                    if($t->remain > 0 && $t->academic_year_id == $tahunAktif->id && !$berkala){
                        $tagihan = $t->remainWithSeparator;
                        $berkala = $key+1;
                    }
                }
                if(!$tagihan){
                    $tagihan = $data->bmsRemainWithSeparator;
                    //$berkala = $data->termin()->count();
                }
            }

            $financeAdmin = Pegawai::select('name','phone_number','gender_id')->aktif()->whereHas('units',function($query)use($data){
                $query->where('unit_id',$data->unit_id)->whereHas('jabatans',function($query){
                    $query->where('name','Admin Keuangan');
                });
            })->first();
                
            $hm = Pegawai::aktif()->whereHas('units',function($query)use($data){
                $query->where('unit_id',$data->unit_id)->whereHas('jabatans',function($query){
                    $query->where('name','Kepala Sekolah');
                });
            })->first();

            $usePeriodNumber = false;

            $bmsType = "BMS".($data->tipe ? ' '.ucwords($data->tipe->bmsTypeWoNumber) : ($data->termin()->count() > 1 ? " Berkala".($berkala && $usePeriodNumber ? " ".$berkala : null) : " Tunai"));
                
            $unit = $data->siswa->unit;
            $unitPhone = null;
            if($unit->id == 2){
                $phones = $siswa == 'calon' || ($data->siswa->kelas && $data->siswa->kelas->level && in_array($data->siswa->kelas->level->level,['1','2'])) ? explode(';',$unit->phone_unit)[1] : explode(';',$unit->phone_unit)[0];
                $unitPhone = $phones ? implode(", ",explode('-',$phones)) : null;
            }
            else $unitPhone = $unit->phone_unit;
            
            $units = Unit::select('short_desc','address','region_id','phone_unit','email')->sekolah()->get();

            $kelas = ($siswa == 'siswa' && $data->siswa->kelas && $data->siswa->kelas->level ? $data->siswa->kelas->level->level_romawi : '-').($data->siswa->kelas && $data->siswa->kelas->jurusan ? ' '.$data->siswa->kelas->jurusan->major_name.' ' : ' ').($data->siswa->kelas ? $data->siswa->kelas->namakelases->class_name : null);
    
            return view('email.reminder_bms',compact('data','tahunAktif','semesterAktif','bmsType','kelas','financeAdmin','hm','unit','unitPhone','units','siswa'));

            $identitas = $siswa == 'calon' ? $data->siswa : $data->siswa->identitas;
            
            if($identitas->orangtua && ($identitas->orangtua->father_email || $identitas->orangtua->mother_email || $identitas->orangtua->guardian_email)){
                $emailCol = collect();
                if($identitas->orangtua->father_email){
                    $emailCol->push('father_email');
                }
                elseif($identitas->orangtua->mother_email){
                    $emailCol->push('mother_email');
                }
                elseif($identitas->orangtua->guardian_email){
                    $emailCol->push('guardian_email');
                }
                if(!isset($request->email)){
                    $firstEmailAttr = $emailCol->first();
                    $data["email"] = $identitas->orangtua->{$firstEmailAttr};
                }
                elseif(in_array($request->email,$emailCol->toArray())){
                    $data["email"] = $identitas->orangtua->{$request->email};
                }
                else{
                    return redirect()->back()->with(['danger' => 'Email orang tua tidak ditemukan.']);
                }
            
                $data["title"] = "Informasi Tanggungan BMS DIGIYOK";
                // Override Email for Testing
                $data["email"] = 'ihsfwz@information-computer.com';
                //$data["email"] = 'arif@sekolahauliya.sch.id';
                
                //$files = [public_path('../img/boy.png')];
                
                $file = null;
                
                if($request->hasFile('file')){
                    $messages = [
                        'file.file' => 'Mohon pilih berkas lampiran yang valid',
                        'file.mimes' => 'Mohon lampirkan berkas PDF',
                        'file.max' => 'Ukuran berkas maksimal 5 MB',
                    ];
        
                    $this->validate($request, [
                        'file' => 'file|mimes:pdf|max:5000',
                    ],$messages);
                    
                    $unit = str_replace(" ", "_", strtolower($data->unit->name));
        
                    if($request->file('file')->isValid()){
                        // Move file to storage folder
                        $extension = $request->file('file')->getClientOriginalExtension();
                        $fileName = time().'_attachment';
                        $nameExtension = $fileName.'.'.$extension;
                        $filePath = 'temp/'.$unit.'/';
                        $request->file('file')->storeAs($filePath, $nameExtension);
                        
                        $file = $filePath.$nameExtension;
                    }
                }
                
                Mail::send('email.reminder_bms',compact('data','tahunAktif','semesterAktif','bmsType','kelas','financeAdmin','hm','unit','unitPhone','units','siswa'), function($message)use($data, $file) {
                    $message->from($address = 'estatement@digiyok.com', $name = 'Sekolah DIGIYOK e-Statement');
                    $message->to($data["email"]);
                    $message->subject($data["title"]);
         
                    // foreach ($files as $file){
                    //     $message->attach($file);
                    // }
                    
                    if($file){
                        $message->attach(storage_path('app/'.$file));
                    }
                });
                
                // Delete current file in storage folder
                if($file && Storage::exists($file)){
                    Storage::delete($file);
                }
                
                //return 'Sukses';
                
                return redirect()->back()->with('success','Email kepada '.$data['email'].' berhasil dikirim.');
            }
            else return redirect()->back()->with(['danger' => 'Alamat email orang tua/wali tidak ditemukan.']);
        }
        else return redirect()->back()->with(['danger' => 'Data tagihan siswa tidak ditemukan.']);
    }
    
    public function reminderWhatsApp(Request $request,$siswa = null,$id)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa','alumni'])){
            $siswa = 'siswa';
        }

        if($siswa == 'calon'){
            $data = BmsCalonSiswa::where('bms_paid','>',0)->where('id',$id)->whereHas('siswa', function($q){
                $q->has('virtualAccount');
            });
        }
        else{
            $data = BMS::where('id',$id)->whereHas('siswa', function($q){
                $q->has('virtualAccount');
            });
            if($siswa == 'alumni'){
                $data = $data->whereHas('siswa', function($q){
                    $q->where('is_lulus',1);
                });
            }
            else{
                $data = $data->whereHas('siswa', function($q){
                    $q->where('is_lulus',0);
                });
            }
        }
        $data = $data->having('bms_remain','>',0)->first();
        
        if($data){
            $financeAdmin = Pegawai::select('name','phone_number','gender_id')->aktif()->whereHas('units',function($query)use($data){
                $query->where('unit_id',$data->unit_id)->whereHas('jabatans',function($query){
                    $query->where('name','Admin Keuangan');
                });
            })->first();
            
            $hm = Pegawai::aktif()->whereHas('units',function($query)use($data){
                $query->where('unit_id',$data->unit_id)->whereHas('jabatans',function($query){
                    $query->where('name','Kepala Sekolah');
                });
            })->first();
            
            $tahunAktif = TahunAjaran::where('is_active',1)->first();
            $tagihan = $berkala = null;
            if($data->termin()->count() > 1){
                foreach($data->termin as $key => $t){
                    if($t->remain > 0 && $t->academic_year_id == $tahunAktif->id && !$berkala){
                        $tagihan = $t->remainWithSeparator;
                        $berkala = $key+1;
                    }
                }
                if(!$tagihan){
                    $tagihan = $data->bmsRemainWithSeparator;
                    //$berkala = $data->termin()->count();
                }
            }
            
            Date::setLocale('id');
            $tanggalTagihan = Date::now('Asia/Jakarta')->format('j F Y');

            $identitas = $siswa == 'calon' ? $data->siswa : $data->siswa->identitas;
            
            if($identitas->orangtua && ($identitas->orangtua->father_phone || $identitas->orangtua->mother_phone || $identitas->orangtua->guardian_phone_number)){
                $phoneNumber = null;
                if($identitas->orangtua->father_phone && (substr($identitas->orangtua->father_phone, 0, 2) == "62" || substr($identitas->orangtua->father_phone, 0, 1) == "0")){
                    $phoneNumber = $identitas->orangtua->father_phone;
                }
                elseif($identitas->orangtua->mother_phone && (substr($identitas->orangtua->mother_phone, 0, 2) == "62" || substr($identitas->orangtua->mother_phone, 0, 1) == "0")){
                    $phoneNumber = $identitas->orangtua->mother_phone;
                }
                elseif($identitas->orangtua->guardian_phone_number && (substr($identitas->orangtua->guardian_phone_number, 0, 2) == "62" || substr($identitas->orangtua->guardian_phone_number, 0, 1) == "0")){
                    $phoneNumber = $identitas->orangtua->guardian_phone_number;
                }
                
                if(!$phoneNumber) return redirect()->back()->with(['danger' => 'Nomor telepon orang tua/wali siswa tidak valid. Pastikan diawali dengan 0 (nol) atau 62.']);
            
                $phoneNumber = substr($phoneNumber, 0, 2) == "62" ? $phoneNumber : ('62'.substr($phoneNumber, 1));

                $usePeriodNumber = false;

                $bmsType = "BMS".($data->tipe ? ' '.ucwords($data->tipe->bms_type) : ($data->termin()->count() > 1 ? " Berkala".($berkala && $usePeriodNumber ? " ".$berkala : " Tunai") : "null"));

                $unit = $data->siswa->unit;
                $unitPhone = null;
                if($unit->id == 2){
                    $phones = $data->siswa->kelas && $data->siswa->kelas->level && in_array($data->siswa->kelas->level->level,['1','2']) ? explode(';',$unit->phone_unit)[1] : explode(';',$unit->phone_unit)[0];
                    $unitPhone = $phones ? implode(", ",explode('-',$phones)) : null;
                }
                else $unitPhone = $unit->phone_unit;
                    
                $kelas = ($data->siswa->kelas && $data->siswa->kelas->level ? $data->siswa->kelas->level->level_romawi : '-').($data->siswa->kelas && $data->siswa->kelas->jurusan ? ' '.$data->siswa->kelas->jurusan->major_name.' ' : ' ').($data->siswa->kelas ? $data->siswa->kelas->namakelases->class_name : null);
    
                $whatsAppText = rawurlencode("*TAGIHAN ".strtoupper($bmsType)."*

Assalamu’alaikum Ayah Bunda,

Bersama ini kami informasikan Tanggungan ".$bmsType." atas nama *".$identitas->student_name."* ".($siswa == 'siswa' && $kelas && $kelas != '-' ? "(Kelas: ".$kelas.") " : null)."melalui email resmi sekolah estatement@digiyok.com. Mohon Ayah Bunda segera melakukan pembayaran melalui VA BMS ananda di Bank BSI sebagaimana tercantum dalam email Ayah Bunda.

Informasi lebih lanjut dapat menghubungi bagian Administrasi Keuangan ".str_replace("Digiyok","DIGIYOK",$unit->desc)." di nomor ".($unitPhone ? $unitPhone." (via _Telephone_)".($financeAdmin ? " atau " : null) : null).($financeAdmin ? $financeAdmin->phoneNumberWithDashId." (via _WhatsApp_)." : ($unitPhone ? "." : null))."
Wassalamu’alaikum Warahmatullahi Wabarakatuh.

Kepala ".str_replace("Digiyok","DIGIYOK",$unit->desc)."
".($hm ? "*".$hm->name."*" : '...')."

_Notes : Apabila pada saat menerima informasi ini Ayah Bunda sudah membayarkan ".$bmsType." tersebut, kami mengucapkan terima kasih._");
                    
                //return rawurldecode($whatsAppText);
        
                return redirect('https://api.whatsapp.com/send?phone='.$phoneNumber.'&text='.$whatsAppText);
            }
            else return redirect()->back()->with(['danger' => 'Nomor telepon orang tua/wali siswa tidak ditemukan']);
        }
        else return redirect()->back()->with(['danger' => 'Data tidak ditemukan. Gagal mengirim pesan WhatsApp.']);
    }

    public function cetakTagihan($id)
    {
        try {
            $real_id = Crypt::decrypt($id);
        } catch (\Exception $e){
            return redirect()->back()->with('error','Terjadi Kesalahan');
        }
    
        $bms = BMS::find($real_id);
        $calon = $bms ? $bms->siswa : null;
        if($bms->bms_type_id == 1){
            return view('keuangan.pembayaran.bms.surat',compact('calon', 'bms'));
        }else{
            return view('keuangan.pembayaran.bms.surat-berkala',compact('calon', 'bms'));
        }
    }
}

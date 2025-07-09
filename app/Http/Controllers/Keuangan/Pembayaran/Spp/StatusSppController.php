<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Spp\SppSiswaCollection;
use Illuminate\Http\Request;

use NumberHelper;
use Mail;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Storage;

use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\SppBill;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Level;
use App\Models\Unit;

class StatusSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Status SPP';
        $this->route = 'spp.status';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['siswa','alumni'])){
            $siswa = 'siswa';
        }

        $unit_id = auth()->user()->pegawai->unit_id;
        if($unit_id == 5){
            $levels = Level::all();
            $unit_id = 1;
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }
        $level = 'semua';

        $datas = Spp::where('unit_id',$unit_id)->whereHas('siswa', function($q) use ($unit_id){
            $q->where('unit_id',$unit_id);
        });

        if($siswa == 'alumni'){
            $datas = $datas->whereHas('siswa', function($q){
                $q->where('is_lulus',1);
            });
        }
        else{
            $datas = $datas->whereHas('siswa', function($q){
                $q->where('is_lulus',0);
            });
        }

        $datas = $datas->get();

        $active = $this->active.($siswa ? (' '.ucwords(($siswa == 'alumni' ? 'Siswa ' : null).$siswa)) : null);
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','datas','levels','unit_id','level','siswa'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGet(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['siswa','alumni'])){
            $siswa = 'siswa';
        }

        $unit_id = $request->unit_id;
        $level_id = $request->level_id;

        $datas = Spp::when($level_id, function($q, $level_id){
            return $q->whereHas('siswa', function($q) use ($level_id){
                $q->where('level_id',$level_id);
            });
        });

        if($request->user()->pegawai->unit_id == 5){
            $datas = $datas->where('unit_id',$unit_id);
        }else{
            $datas = $datas->where('unit_id',$request->user()->pegawai->unit_id);
        }

        if($siswa == 'alumni'){
            $datas = $datas->whereHas('siswa', function($q){
                $q->where('is_lulus',1);
            });
        }
        else{
            $datas = $datas->whereHas('siswa', function($q){
                $q->where('is_lulus',0);
            });
        }

        $datas = $datas->get();

        $data = new SppSiswaCollection($datas);

        return response()->json([$data]);
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
        $data = $request->id ? Spp::select('*')->selectRaw('total-(paid+deduction) as tunggakan')->where('id',$request->id)->whereHas('siswa', function($q){
            $q->has('virtualAccount');
        })->having('tunggakan','>',0)->first() : null;
                
        $day = Date::now('Asia/Jakarta')->format('d');
        $number = null;
        if($day >= 10) $number =  $day < 25 ? 1 : 2;
        
        if($data && $number){
            $tagihans = SppBill::select('id','spp_id','month','year','spp_nominal','deduction_nominal','spp_paid')->selectRaw('spp_nominal-(deduction_nominal+spp_paid) as tunggakan')->where([
                'unit_id' => $data->unit_id,
                'student_id' => $data->siswa->id
            ])->having('tunggakan','>',0)->orderBy('year','ASC')->orderBy('month','ASC')->get();
            
            if($tagihans && count($tagihans) > 0){
                $date = Date::now('Asia/Jakarta')->format('F Y');
                
                if($data->siswa->identitas->orangtua && ($data->siswa->identitas->orangtua->father_email || $data->siswa->identitas->orangtua->mother_email || $data->siswa->identitas->orangtua->guardian_email)){
                    $emailCol = collect();
                    if($data->siswa->identitas->orangtua->father_email){
                        $emailCol->push('father_email');
                    }
                    if($data->siswa->identitas->orangtua->mother_email){
                        $emailCol->push('mother_email');
                    }
                    if($data->siswa->identitas->orangtua->guardian_email){
                        $emailCol->push('guardian_email');
                    }
                    
                    return view('keuangan.pembayaran.spp.email', compact('data','emailCol','date'));
                }
                else return "Ups, alamat email orang tua/wali tidak ditemukan.";
            }
            else return "Tagihan SPP siswa tidak ditemukan";
        }
        elseif(!$number) return "Ups, hari ini kurang dari tanggal 10. Gagal memuat data.";
        else return "Ups, tidak dapat memuat data";
    }
    
    public function reminderEmail(Request $request,$id)
    {
        $data = Spp::select('*')->selectRaw('total-(paid+deduction) as tunggakan')->where('id',$id)->whereHas('siswa', function($q){
            $q->has('virtualAccount');
        })->having('tunggakan','>',0)->first();
                
        $day = Date::now('Asia/Jakarta')->format('d');
        $number = null;
        if($day >= 10) $number =  $day < 25 ? 1 : 2;
        
        if($data && $number){
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
    
            $tagihans = SppBill::select('id','spp_id','month','year','spp_nominal','deduction_nominal','spp_paid')->selectRaw('spp_nominal-(deduction_nominal+spp_paid) as tunggakan')->where([
                'unit_id' => $data->unit_id,
                'student_id' => $data->siswa->id
            ])->having('tunggakan','>',0)->orderBy('year','ASC')->orderBy('month','ASC')->get();
            
            if($tagihans && count($tagihans) > 0){
                $kelas = ($data->siswa->kelas && $data->siswa->kelas->level ? $data->siswa->kelas->level->level_romawi : '-').($data->siswa->kelas && $data->siswa->kelas->jurusan ? ' '.$data->siswa->kelas->jurusan->major_name.' ' : ' ').($data->siswa->kelas ? $data->siswa->kelas->namakelases->class_name : null);
                
                $lastMonthDate = Date::now('Asia/Jakarta')->subMonthNoOverflow()->format('F Y');
                $date = Date::now('Asia/Jakarta')->format('F Y');
                
                $thisMonthBill = $tagihans->last();
                
                $day = Date::now('Asia/Jakarta')->format('d') < 10 || Date::now('Asia/Jakarta')->format('d') >= 18 ? '10' : '18';
                $month = Date::now('Asia/Jakarta')->format('d') < 18 ? Date::now('Asia/Jakarta')->format('-m-Y') : Date::now('Asia/Jakarta')->addMonth()->format('-m-Y');
                $deadline = $day.$month;
                
                $unit = $data->siswa->unit;
                $unitPhone = null;
                if($unit->id == 2){
                    $phones = in_array($data->siswa->kelas->level->level,['1','2']) ? explode(';',$unit->phone_unit)[1] : explode(';',$unit->phone_unit)[0];
                    $unitPhone = $phones ? implode(", ",explode('-',$phones)) : null;
                }
                else $unitPhone = $unit->phone_unit;
                
                $units = Unit::select('short_desc','address','region_id','phone_unit','email')->sekolah()->get();
        
                //return view('email.reminder_spp',compact('data','number','kelas','lastMonthDate','date','thisMonthBill','deadline','financeAdmin','hm','unit','unitPhone','units'));
                
                if($data->siswa->identitas->orangtua && ($data->siswa->identitas->orangtua->father_email || $data->siswa->identitas->orangtua->mother_email || $data->siswa->identitas->orangtua->guardian_email)){
                    $emailCol = collect();
                    if($data->siswa->identitas->orangtua->father_email){
                        $emailCol->push('father_email');
                    }
                    elseif($data->siswa->identitas->orangtua->mother_email){
                        $emailCol->push('mother_email');
                    }
                    elseif($data->siswa->identitas->orangtua->guardian_email){
                        $emailCol->push('guardian_email');
                    }
                    if(!isset($request->email)){
                        $firstEmailAttr = $emailCol->first();
                        $data["email"] = $data->siswa->identitas->orangtua->{$firstEmailAttr};
                    }
                    elseif(in_array($request->email,$emailCol->toArray())){
                        $data["email"] = $data->siswa->identitas->orangtua->{$request->email};
                    }
                    else{
                        return redirect()->back()->with(['danger' => 'Email orang tua tidak ditemukan.']);
                    }
                
                    $data["title"] = "Informasi Tagihan ".($number ? "#".$number." " : null)."DIGIYOK Bulan ".$date;
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
                        
                        $unitDir = str_replace(" ", "_", strtolower($data->unit->name));
            
                        if($request->file('file')->isValid()){
                            // Move file to storage folder
                            $extension = $request->file('file')->getClientOriginalExtension();
                            $fileName = time().'_attachment';
                            $nameExtension = $fileName.'.'.$extension;
                            $filePath = 'temp/'.$unitDir.'/';
                            $request->file('file')->storeAs($filePath, $nameExtension);
                            
                            $file = $filePath.$nameExtension;
                        }
                    }
                    
                    Mail::send('email.reminder_spp',compact('data','number','kelas','lastMonthDate','date','thisMonthBill','deadline','financeAdmin','hm','unit','unitPhone','units'), function($message)use($data, $file) {
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
            else return redirect()->back()->with(['danger' => 'Tagihan SPP siswa tidak ditemukan']);
        }
        elseif(!$number) return redirect()->back()->with(['danger' => 'Hari ini kurang dari tanggal 10. Gagal mengirim email.']);
        else return redirect()->back()->with(['danger' => 'Data tagihan siswa tidak ditemukan.']);
    }
    
    public function reminderWhatsApp(Request $request,$id)
    {
        $data = Spp::select('*')->selectRaw('total-(paid+deduction) as tunggakan')->where('id',$id)->whereHas('siswa', function($q){
            $q->has('virtualAccount');
        })->having('tunggakan','>',0)->first();
                
        $day = Date::now('Asia/Jakarta')->format('d');
        $number = null;
        if($day >= 10) $number =  $day < 25 ? 1 : 2;
        
        if($data && $number){
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
            
            $date = Date::now('Asia/Jakarta')->format('F Y');
    
            $tagihans = SppBill::select('id','spp_id','month','year','spp_nominal','deduction_nominal','spp_paid')->selectRaw('spp_nominal-(deduction_nominal+spp_paid) as tunggakan')->where([
                'unit_id' => $data->unit_id,
                'student_id' => $data->siswa->id
            ])->having('tunggakan','>',0)->orderBy('year','ASC')->orderBy('month','ASC')->get();
            
            if($tagihans && count($tagihans) > 0){
                $bulanTagihan = $tagihans->first()->monthYearId;
                if(count($tagihans) > 1){
                    $tagihanAwal = $tagihans->first();
                    $tagihanAkhir = $tagihans->last();
                    
                    $bulanTagihan = ($tagihanAwal->year == $tagihanAkhir->year ? $tagihanAwal->monthId : $tagihanAwal->monthYearId).' sampai dengan '.$tagihanAkhir->monthYearId;
                }
                
                if($data->siswa->identitas->orangtua && ($data->siswa->identitas->orangtua->father_phone || $data->siswa->identitas->orangtua->mother_phone || $data->siswa->identitas->orangtua->guardian_phone_number)){
                    $phoneNumber = null;
                    if($data->siswa->identitas->orangtua->father_phone && (substr($data->siswa->identitas->orangtua->father_phone, 0, 2) == "62" || substr($data->siswa->identitas->orangtua->father_phone, 0, 1) == "0")){
                        $phoneNumber = $data->siswa->identitas->orangtua->father_phone;
                    }
                    elseif($data->siswa->identitas->orangtua->mother_phone && (substr($data->siswa->identitas->orangtua->mother_phone, 0, 2) == "62" || substr($data->siswa->identitas->orangtua->mother_phone, 0, 1) == "0")){
                        $phoneNumber = $data->siswa->identitas->orangtua->mother_phone;
                    }
                    elseif($data->siswa->identitas->orangtua->guardian_phone_number && (substr($data->siswa->identitas->orangtua->guardian_phone_number, 0, 2) == "62" || substr($data->siswa->identitas->orangtua->guardian_phone_number, 0, 1) == "0")){
                        $phoneNumber = $data->siswa->identitas->orangtua->guardian_phone_number;
                    }
                    
                    if(!$phoneNumber) return redirect()->back()->with(['danger' => 'Nomor telepon orang tua/wali siswa tidak valid. Pastikan diawali dengan 0 (nol) atau 62.']);
                
                    $phoneNumber = substr($phoneNumber, 0, 2) == "62" ? $phoneNumber : ('62'.substr($phoneNumber, 1));
                    
                    $unit = $data->siswa->unit;
                    $unitPhone = null;
                    if($unit->id == 2){
                        $phones = in_array($data->siswa->kelas->level->level,['1','2']) ? explode(';',$unit->phone_unit)[1] : explode(';',$unit->phone_unit)[0];
                        $unitPhone = $phones ? implode(", ",explode('-',$phones)) : null;
                    }
                    else $unitPhone = $unit->phone_unit;
                    
                    $kelas = ($data->siswa->kelas && $data->siswa->kelas->level ? $data->siswa->kelas->level->level_romawi : '-').($data->siswa->kelas && $data->siswa->kelas->jurusan ? ' '.$data->siswa->kelas->jurusan->major_name.' ' : ' ').($data->siswa->kelas ? $data->siswa->kelas->namakelases->class_name : null);
        
                    $whatsAppText = rawurlencode("*TAGIHAN ".($number ? "#".$number." " : null)."SPP BULAN ".strtoupper($date)."*

Assalamu’alaikum Ayah Bunda,

".($number == 1 ? "Bersama ini kami informasikan Tanggungan SPP per ".$date." atas nama *".$data->siswa->identitas->student_name."* ".($kelas && $kelas != '-' ? "(Kelas: ".$kelas.") " : null)."melalui email resmi sekolah estatement@digiyok.com. Berdasarkan catatan kami, pembayaran Tanggungan SPP ananda belum diterima pada rekening DIGIYOK per tanggal 10 ".$date."." : "Berdasarkan catatan kami, pembayaran Tanggungan SPP sebagaimana Tagihan #1 sebelumnya per ".$date." atas nama *".$data->siswa->identitas->student_name."* ".($kelas && $kelas != '-' ? "(Kelas: ".$kelas.") " : null)."belum diterima pada rekening DIGIYOK per hari ini.")." Mohon Ayah Bunda segera melakukan pembayaran melalui VA SPP ananda di Bank BSI.

Informasi lebih lanjut ".($number == 2 ? "kami sampaikan melalui email estatement@digiyok.com atau " : null)."dapat menghubungi bagian Administrasi Keuangan ".str_replace("Digiyok","DIGIYOK",$unit->desc)." di nomor ".($unitPhone ? $unitPhone." (via _Telephone_)".($financeAdmin ? " atau " : null) : null).($financeAdmin ? $financeAdmin->phoneNumberWithDashId." (via _WhatsApp_)." : ($unitPhone ? "." : null))."
Wassalamu’alaikum Warahmatullahi Wabarakatuh.

Kepala ".str_replace("Digiyok","DIGIYOK",$unit->desc)."
".($hm ? "*".$hm->name."*" : '...')."

_Notes : Apabila pada saat menerima informasi ini Ayah Bunda sudah membayarkan SPP tersebut, kami mengucapkan terima kasih._");
                    
                    //return rawurldecode($whatsAppText);
            
                    return redirect('https://api.whatsapp.com/send?phone='.$phoneNumber.'&text='.$whatsAppText);
                }
                else return redirect()->back()->with(['danger' => 'Nomor telepon orang tua/wali siswa tidak ditemukan']);
            }
            else return redirect()->back()->with(['danger' => 'Tagihan SPP siswa tidak ditemukan']);
        }
        elseif(!$number) return redirect()->back()->with(['danger' => 'Hari ini kurang dari tanggal 10. Gagal mengirim pesan WhatsApp.']);
        else return redirect()->back()->with(['danger' => 'Data tidak ditemukan. Gagal mengirim pesan WhatsApp.']);
    }
}

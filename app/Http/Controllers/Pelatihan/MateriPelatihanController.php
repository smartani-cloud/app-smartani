<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Pelatihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Pelatihan\Pelatihan;
use App\Models\Pelatihan\PresensiPelatihan;
use App\Models\Pelatihan\SasaranPelatihan;
use App\Models\Pelatihan\StatusWajib;
use App\Models\Penempatan\Jabatan;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\Setting;
use App\Models\StatusAcc;
use App\Models\Unit;

class MateriPelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        if($request->tahunajaran){
            $tahunajaran = str_replace("-","/",$request->tahunajaran);
            if($tahunajaran != $aktif->academic_year){
                $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
            }

            if(!$aktif) return redirect()->route('pelatihan.materi.index');
        }

        $tahun = TahunAjaran::orderBy('academic_year')->get();
        $status = 'aktif';

        if(isset($request->status) && $request->status != 'aktif'){
            $status = $request->status;
        }

        if(($aktif->is_active == 1 && $status == 'selesai') || $aktif->is_active == 0){
            $pelatihan = $aktif->pelatihan()->selesai();
            if(in_array($role,['kepsek','wakasek'])){
                $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($request){
                    $query->whereHas('jabatan',function($query)use($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                });
                if($role == 'wakasek'){
                    $pelatihan = $pelatihan->whereNotNull(['education_acc_id','education_acc_time'])->where('education_acc_status_id',1);
                }
            }
            $pelatihan = $pelatihan->orderBy('date','desc')->get();
        }
        else{
            $pelatihan = $aktif->pelatihan()->aktif();
            if(in_array($role,['kepsek','wakasek'])){
                $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($request){
                    $query->whereHas('jabatan',function($query)use($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                });
                if($role == 'wakasek'){
                    $pelatihan = $pelatihan->whereNotNull(['education_acc_id','education_acc_time'])->where('education_acc_status_id',1);
                }
            }
            $pelatihan = $pelatihan->orderBy('created_at','desc')->get();
        }

        if($aktif->is_active == 1 && $status != 'selesai'){
            if($role == 'etm'){
                $unit = Unit::all();
                $pegawai = Pegawai::select('name','id')->whereNotNull(['position_id','unit_id'])->whereHas('units',function($query){
                    $query->has('jabatans');
                })->aktif()->orderBy('name')->get();
                $semester = $aktif->semester()->orderBy('semester_id')->get();
                $jabatan = JabatanUnit::all();
                $status = StatusWajib::all();

                return view('kepegawaian.etm.pelatihan_materi_index', compact('aktif','tahun','pelatihan','jabatan','unit','pegawai','semester','status'));
            }
            else{
                if($role == 'etl') $folder = $role;
                else $folder = 'read-only';

                return view('kepegawaian.'.$folder.'.pelatihan_materi_index', compact('aktif','tahun','pelatihan'));
            }
        }
        
        return view('kepegawaian.read-only.pelatihan_materi_selesai', compact('aktif','tahun','pelatihan'));
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
            'name.required' => 'Mohon tuliskan materi pelatihan',
            'position.required' => 'Mohon tentukan sasaran pelatihan',
            'date.date' => 'Mohon periksa kembali format tanggal',
            'speaker.required_if' => 'Mohon tentukan narasumber pelatihan',
            'speaker_name.required_if' => 'Mohon tentukan narasumber pelatihan',
            'semester.required' => 'Mohon pilih salah satu semester',
            'status.required' => 'Mohon tentukan sifat pelatihan',
            'organizer.required' => 'Mohon tentukan penyelenggara pelatihan'
        ];

        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'date' => 'nullable|date',
            'speaker_category' => 'required',
            'speaker' => 'required_if:speaker_category,1',
            'speaker_name' => 'required_if:speaker_category,2',
            'semester' => 'required',
            'status' => 'required',
            'organizer' => 'required'
        ], $messages);

        $tahunajaran = TahunAjaran::where('academic_year',$request->tahunajaran)->first();
        $semester = $tahunajaran->semester()->where('semester_id',$request->tahunajaran.'-'.$request->semester)->first();
        $status = StatusWajib::find($request->status);

        // $pelatihan = Pelatihan::where(['name' => $request->name, 'academic_year_id' => $tahunajaran->id, 'semester_id' => $semester->id])->first();

        // if(!$pelatihan){
            $pelatihan = new Pelatihan();
            $pelatihan->name = $request->name;
            $pelatihan->desc = isset($request->desc) ? $request->desc : null;
            $pelatihan->date = isset($request->date) ? Date::parse($request->date) : null;
            $pelatihan->place = isset($request->place) ? $request->place : null;
            $pelatihan->speaker_id = isset($request->speaker) && $request->speaker_category == 1 ? $request->speaker : null;
            $pelatihan->speaker_name = isset($request->speaker_name) && $request->speaker_category == 2 ? $request->speaker_name : null;
            $pelatihan->academic_year_id = isset($tahunajaran) ? $tahunajaran->id : null;
            $pelatihan->semester_id = isset($semester) ? $semester->id : null;
            $pelatihan->mandatory_status_id = isset($status) ? $status->id : null;
            $pelatihan->organizer_id = isset($request->organizer) ? $request->organizer : null;
            $pelatihan->active_status_id = 1;
            $pelatihan->save();

            $pelatihan->fresh();

            foreach($request->position as $p){
                $sasaran = new SasaranPelatihan();
                $sasaran->position_id = $p;

                $pelatihan->sasaran()->save($sasaran);
            }

            Session::flash('success','Data materi pelatihan berhasil ditambahkan');
        // }
        // else Session::flash('danger','Data materi pelatihan sudah pernah ditambahkan');

        return redirect()->route('pelatihan.materi.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $role = $request->user()->role->name;

        $pelatihan = Pelatihan::find($id);

        if($pelatihan){
            if($role == 'etm'){
                $etl = PegawaiJabatan::whereHas('jabatan',function($query){
                    $query->where('name','Kepala Divisi Edukasi');
                })->with('pegawaiUnit.pegawai')->get()->pluck('pegawaiUnit.pegawai')->where('active_status_id',1)->sortByDesc('id')->first();
                $peserta = null;
                if($pelatihan->active_status_id == 1){
                    $sasaran = $pelatihan->sasaran()->pluck('position_id');
                    $unit = JabatanUnit::whereIn('id',$sasaran)->pluck('unit_id')->unique();
                    $i = 1;
                    foreach($unit as $u){
                        $posisi = JabatanUnit::whereIn('id',$sasaran)->where('unit_id',$u)->pluck('position_id');
                        $pegawai = Pegawai::aktif()->whereHas('units',function($query)use($u,$posisi){
                            $query->where('unit_id',$u)->whereHas('jabatans',function($query)use($posisi){
                                $query->whereIn('position_id',$posisi);
                            });
                        })->get();
                        if($i == 1)
                            $peserta = $pegawai;
                        else
                            $peserta = $peserta->concat($pegawai);

                        $i++;
                    }

                    $peserta = $peserta->unique()->all();
                }
                elseif($pelatihan->active_status_id == 2){
                    $peserta = $pelatihan->presensi()->whereHas('status')->get();
                    $role = 'read-only';
                }
                
                return view('kepegawaian.'.$role.'.pelatihan_materi_tampil', compact('pelatihan','etl','peserta'));
            }
            elseif(in_array($role,['kepsek','wakasek'])){
                $peserta = $pelatihan->presensi()->whereHas('pegawai',function($query)use($request){
                    $query->whereHas('units',function($query) use($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                })->whereHas('status')->get();
            }
            else{
                $peserta = $pelatihan->presensi()->whereHas('status')->get();
            }

            return view('kepegawaian.read-only.pelatihan_materi_tampil', compact('pelatihan','peserta'));
        }
        
        else return redirect()->route('pelatihan.materi.index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $role = $request->user()->role->name;

        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;

        if($pelatihan){
            if($role == 'etm'){
                $tahunajaran = TahunAjaran::find($pelatihan->academic_year_id);
                $unit = Unit::all();
                $pegawai = Pegawai::select('name','id')->whereNotNull(['position_id','unit_id'])->whereHas('units',function($query){
                    $query->has('jabatans');
                })->aktif()->orderBy('name')->get();
                $sasaran = $pelatihan->sasaran()->pluck('position_id');
                $jabatan = JabatanUnit::all();
                $semester = $tahunajaran->semester()->orderBy('semester_id')->get();
                $status = StatusWajib::all();

                return view('kepegawaian.'.$role.'.pelatihan_materi_ubah', compact('pelatihan',
                    'unit','pegawai','sasaran','jabatan','semester','status'));
            }
            elseif($role == 'etl'){
                if($pelatihan->active_status_id != 2){
                    if($pelatihan->education_acc_status_id != 1){
                        $acc = StatusAcc::all();

                        return view('kepegawaian.'.$role.'.pelatihan_materi_validasi', compact('pelatihan','acc'));
                    }
                    else{
                        $pegawai = Pegawai::select('name','id')->whereNotNull(['position_id','unit_id'])->whereHas('units',function($query){
                            $query->has('jabatans');
                        })->aktif()->orderBy('name')->get();

                        return view('kepegawaian.'.$role.'.pelatihan_materi_ubah', compact('pelatihan','pegawai'));
                    }
                }
                else{
                    return "Ups, tidak dapat memuat data";
                }
            }
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
        $role = $request->user()->role->name;

        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;
        $nama = $pelatihan ? $pelatihan->name : null;

        if($pelatihan){
            if($role == 'etm'){
                if($pelatihan->education_acc_status_id != 1 && $pelatihan->active_status_id != 2){
                    $messages = [
                        'name.required' => 'Mohon tuliskan materi pelatihan',
                        'position.required' => 'Mohon tentukan sasaran pelatihan',
                        'speaker.required_if' => 'Mohon tentukan narasumber pelatihan',
                        'speaker_name.required_if' => 'Mohon tentukan narasumber pelatihan',
                        'semester.required' => 'Mohon pilih salah satu semester',
                        'status.required' => 'Mohon tentukan sifat pelatihan',
                        'organizer.required' => 'Mohon tentukan penyelenggara pelatihan'
                    ];

                    $this->validate($request, [
                        'name' => 'required',
                        'position' => 'required',
                        'date' => 'nullable|date',
                        'speaker_category' => 'required',
                        'speaker' => 'required_if:speaker_category,1',
                        'speaker_name' => 'required_if:speaker_category,2',
                        'semester' => 'required',
                        'status' => 'required',
                        'organizer' => 'required'
                    ], $messages);

                    $tahunajaran = TahunAjaran::find($pelatihan->academic_year_id);
                    $semester = $tahunajaran->semester()->where('semester_id',$tahunajaran->academic_year.'-'.$request->semester)->first();
                    $status = StatusWajib::find($request->status);

                    if($semester && $status){
                        $existing_pelatihan = 0;
                        // if($pelatihan->name != $request->name || $pelatihan->semester_id != $semester->id){
                        //     $existing_pelatihan = Pelatihan::where(['name' => $request->name, 'academic_year_id' => $tahunajaran->id, 'semester_id' => $semester->id])->count();
                        // }

                        if($existing_pelatihan == 0){
                            $pelatihan->name = $request->name;
                            $pelatihan->desc = isset($request->desc) ? $request->desc : null;
                            $pelatihan->date = isset($request->date) ? Date::parse($request->date) : null;
                            $pelatihan->place = isset($request->place) ? $request->place : null;
                            $pelatihan->speaker_id = isset($request->speaker) && $request->speaker_category == 1 ? $request->speaker : null;
                            $pelatihan->speaker_name = isset($request->speaker_name) && $request->speaker_category == 2 ? $request->speaker_name : null;
                            $pelatihan->semester_id = isset($semester) ? $semester->id : null;
                            $pelatihan->mandatory_status_id = isset($status) ? $status->id : null;
                            $pelatihan->organizer_id = isset($request->organizer) ? $request->organizer : null;

                            $sasaran_ids = $pelatihan->sasaran()->pluck('id');

                            $i = 0;
                            $len = count($request->position);

                            if($len > 0){
                                foreach($request->position as $p){
                                    if(count($sasaran_ids) > 0){
                                        if($i < $len){
                                            $sasaran = SasaranPelatihan::find($sasaran_ids->shift());
                                            $sasaran->position_id = $p;
                                            $sasaran->save();
                                        }
                                    }
                                    else{
                                        $sasaran = new SasaranPelatihan();
                                        $sasaran->position_id = $p;

                                        $pelatihan->sasaran()->save($sasaran);
                                    }

                                    $i++;
                                }

                                if(count($sasaran_ids) > 0) SasaranPelatihan::destroy($sasaran_ids);
                            }

                            $pelatihan->save();

                            Session::flash('success','Data materi pelatihan berhasil diubah');
                        }

                        else Session::flash('danger','Data materi pelatihan sudah ada');
                        
                    }

                    else Session::flash('danger','Data materi pelatihan gagal diubah');
                }
                
                elseif($pelatihan->education_acc_status_id == 1 && $pelatihan->active_status_id != 2){
                    $messages = [
                        'date.date' => 'Mohon periksa kembali format tanggal',
                    ];

                    $this->validate($request, [
                        'date' => 'nullable|date'
                    ], $messages);

                    $pelatihan->desc = isset($request->desc) ? $request->desc : null;
                    $pelatihan->date = isset($request->date) ? Date::parse($request->date) : null;
                    $pelatihan->place = isset($request->place) ? $request->place : null;

                    $pelatihan->save();

                    Session::flash('success','Data materi pelatihan berhasil diubah');
                }
            }

            elseif($role == 'etl'){
                if($pelatihan->active_status_id != 2){
                    if($pelatihan->education_acc_status_id != 1){
                        $messages = [
                            'acc_status.required' => 'Mohon tentukan persetujuan'
                        ];

                        $this->validate($request, [
                            'acc_status' => 'required'
                        ], $messages);

                        $pelatihan->education_acc_id = $request->user()->pegawai->id;
                        $pelatihan->education_acc_status_id = $request->acc_status;
                        $pelatihan->education_acc_time = Date::now('Asia/Jakarta');

                        $pelatihan->save();

                        Session::flash('success','Data '.$nama.' berhasil diubah');
                    }
                    else{
                        $messages = [
                            'name.required' => 'Mohon tuliskan materi pelatihan',
                            'speaker.required_if' => 'Mohon tentukan narasumber pelatihan',
                            'speaker_name.required_if' => 'Mohon tentukan narasumber pelatihan'
                        ];

                        $this->validate($request, [
                            'name' => 'required',
                            'speaker_category' => 'required',
                            'speaker' => 'required_if:speaker_category,1',
                            'speaker_name' => 'required_if:speaker_category,2',
                        ], $messages);

                        $pelatihan->name = $request->name;
                        $pelatihan->desc = isset($request->desc) ? $request->desc : null;
                        $pelatihan->speaker_id = isset($request->speaker) && $request->speaker_category == 1 ? $request->speaker : null;
                        $pelatihan->speaker_name = isset($request->speaker_name) && $request->speaker_category == 2 ? $request->speaker_name : null;

                        $pelatihan->save();

                        Session::flash('success','Data '.$nama.' berhasil diubah');
                    }
                }
                else Session::flash('danger','Data '.$nama.' gagal diubah');
            }
        }

        return redirect()->route('pelatihan.materi.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAttribute(Request $request)
    {
        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;

        if(!$request->name || ($request->name && !in_array($request->name,['date','place']))){
            $response = collect([
                'status' => 'danger',
                'message' => 'Perubahan data pelatihan gagal disimpan'
            ]);

            return $response->toJson();
        }

        if($pelatihan && $pelatihan->active_status_id != 2){
            if($request->name == 'date'){
                $pelatihan->date = $request->value ? Date::parse($request->value) : null;
                $pelatihan->save();
            }
            elseif($request->name == 'place'){
                $pelatihan->place = $request->value ? $request->value : null;
                $pelatihan->save();
            }

            $pelatihan->fresh();
            
            $response = collect([
                'status' => 'success',
                'message' => 'Perubahan data pelatihan berhasil disimpan',
                'date' => $pelatihan->date ? Date::parse($pelatihan->date)->format('Y-m-d') : null,
                'date_id' => $pelatihan->date ? $pelatihan->dateFullId : null,
            ]);

            return $response->toJson();
        }
        else{
            $response = collect([
                'status' => 'danger',
                'message' => 'Pelatihan tidak ditemukan'
            ]);

            return $response->toJson();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $role = $request->user()->role->name;
        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;
        $nama = $pelatihan ? $pelatihan->name : null;

        if($pelatihan && (($role == 'etm' && (!$pelatihan->education_acc_status_id || $pelatihan->education_acc_status_id == 2)) || ($role == 'etl' && $pelatihan->education_acc_status_id == 1)) && $pelatihan->active_status_id != 2){
            $pelatihan->presensi()->delete();
            $pelatihan->sasaran()->delete();
            $pelatihan->delete();

            Session::flash('success','Data '.$nama.' berhasil dihapus');
        }

        else Session::flash('danger','Data '.$nama.' gagal dihapus');

        return redirect()->route('pelatihan.materi.index');
    }

    /**
     * End the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function end(Request $request)
    {
        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;
        $nama = $pelatihan ? $pelatihan->name : null;

        if($pelatihan && $pelatihan->education_acc_status_id == 1){
            if($pelatihan->active_status_id != 2){
                $counter = Setting::where('name','training_counter')->first();
                $training_counter = ($counter->value)+1;
                
                $roman_month = $this->romanMonth();
                $year = Date::now('Asia/Jakarta')->format('Y');

                $pelatihan->number = $training_counter.'/YYS/'.$roman_month.'/'.$year;
                $pelatihan->speaker_name = $pelatihan->speaker;
                $pelatihan->active_status_id = 2;

                $pelatihan->presensi()->whereNull('presence_status_id')->delete();

                $pelatihan->save();

                $counter->value = $training_counter;
                $counter->save();

                Session::flash('success','Data '.$nama.' berhasil disimpan');
            }
            else Session::flash('success','Pelatihan '.$nama.' sudah dinyatakan selesai');
        }

        elseif(!$pelatihan->education_acc_status_id || $pelatihan->education_acc_status_id == 2) Session::flash('danger','Data belum dapat disimpan sampai ETL menyetujui pelatihan ini');

        else Session::flash('danger','Data '.$nama.' gagal disimpan');

        return redirect()->route('pelatihan.materi.detail',['id' => $pelatihan->id]);
    }

    /**
     * Generate roman month.
     */
    public function romanMonth(){
        $month = Date::now('Asia/Jakarta')->format('m');

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

        return $returnValue;
    }
=======
<?php

namespace App\Http\Controllers\Pelatihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Pelatihan\Pelatihan;
use App\Models\Pelatihan\PresensiPelatihan;
use App\Models\Pelatihan\SasaranPelatihan;
use App\Models\Pelatihan\StatusWajib;
use App\Models\Penempatan\Jabatan;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\Setting;
use App\Models\StatusAcc;
use App\Models\Unit;

class MateriPelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $aktif = TahunAjaran::where('is_active',1)->latest()->first();

        if($request->tahunajaran){
            $tahunajaran = str_replace("-","/",$request->tahunajaran);
            if($tahunajaran != $aktif->academic_year){
                $aktif = TahunAjaran::where('academic_year',$tahunajaran)->first();
            }

            if(!$aktif) return redirect()->route('pelatihan.materi.index');
        }

        $tahun = TahunAjaran::orderBy('academic_year')->get();
        $status = 'aktif';

        if(isset($request->status) && $request->status != 'aktif'){
            $status = $request->status;
        }

        if(($aktif->is_active == 1 && $status == 'selesai') || $aktif->is_active == 0){
            $pelatihan = $aktif->pelatihan()->selesai();
            if(in_array($role,['kepsek','wakasek'])){
                $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($request){
                    $query->whereHas('jabatan',function($query)use($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                });
                if($role == 'wakasek'){
                    $pelatihan = $pelatihan->whereNotNull(['education_acc_id','education_acc_time'])->where('education_acc_status_id',1);
                }
            }
            $pelatihan = $pelatihan->orderBy('date','desc')->get();
        }
        else{
            $pelatihan = $aktif->pelatihan()->aktif();
            if(in_array($role,['kepsek','wakasek'])){
                $pelatihan = $pelatihan->whereHas('sasaran',function($query)use($request){
                    $query->whereHas('jabatan',function($query)use($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                });
                if($role == 'wakasek'){
                    $pelatihan = $pelatihan->whereNotNull(['education_acc_id','education_acc_time'])->where('education_acc_status_id',1);
                }
            }
            $pelatihan = $pelatihan->orderBy('created_at','desc')->get();
        }

        if($aktif->is_active == 1 && $status != 'selesai'){
            if($role == 'etm'){
                $unit = Unit::all();
                $pegawai = Pegawai::select('name','id')->whereNotNull(['position_id','unit_id'])->whereHas('units',function($query){
                    $query->has('jabatans');
                })->aktif()->orderBy('name')->get();
                $semester = $aktif->semester()->orderBy('semester_id')->get();
                $jabatan = JabatanUnit::all();
                $status = StatusWajib::all();

                return view('kepegawaian.etm.pelatihan_materi_index', compact('aktif','tahun','pelatihan','jabatan','unit','pegawai','semester','status'));
            }
            else{
                if($role == 'etl') $folder = $role;
                else $folder = 'read-only';

                return view('kepegawaian.'.$folder.'.pelatihan_materi_index', compact('aktif','tahun','pelatihan'));
            }
        }
        
        return view('kepegawaian.read-only.pelatihan_materi_selesai', compact('aktif','tahun','pelatihan'));
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
            'name.required' => 'Mohon tuliskan materi pelatihan',
            'position.required' => 'Mohon tentukan sasaran pelatihan',
            'date.date' => 'Mohon periksa kembali format tanggal',
            'speaker.required_if' => 'Mohon tentukan narasumber pelatihan',
            'speaker_name.required_if' => 'Mohon tentukan narasumber pelatihan',
            'semester.required' => 'Mohon pilih salah satu semester',
            'status.required' => 'Mohon tentukan sifat pelatihan',
            'organizer.required' => 'Mohon tentukan penyelenggara pelatihan'
        ];

        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'date' => 'nullable|date',
            'speaker_category' => 'required',
            'speaker' => 'required_if:speaker_category,1',
            'speaker_name' => 'required_if:speaker_category,2',
            'semester' => 'required',
            'status' => 'required',
            'organizer' => 'required'
        ], $messages);

        $tahunajaran = TahunAjaran::where('academic_year',$request->tahunajaran)->first();
        $semester = $tahunajaran->semester()->where('semester_id',$request->tahunajaran.'-'.$request->semester)->first();
        $status = StatusWajib::find($request->status);

        // $pelatihan = Pelatihan::where(['name' => $request->name, 'academic_year_id' => $tahunajaran->id, 'semester_id' => $semester->id])->first();

        // if(!$pelatihan){
            $pelatihan = new Pelatihan();
            $pelatihan->name = $request->name;
            $pelatihan->desc = isset($request->desc) ? $request->desc : null;
            $pelatihan->date = isset($request->date) ? Date::parse($request->date) : null;
            $pelatihan->place = isset($request->place) ? $request->place : null;
            $pelatihan->speaker_id = isset($request->speaker) && $request->speaker_category == 1 ? $request->speaker : null;
            $pelatihan->speaker_name = isset($request->speaker_name) && $request->speaker_category == 2 ? $request->speaker_name : null;
            $pelatihan->academic_year_id = isset($tahunajaran) ? $tahunajaran->id : null;
            $pelatihan->semester_id = isset($semester) ? $semester->id : null;
            $pelatihan->mandatory_status_id = isset($status) ? $status->id : null;
            $pelatihan->organizer_id = isset($request->organizer) ? $request->organizer : null;
            $pelatihan->active_status_id = 1;
            $pelatihan->save();

            $pelatihan->fresh();

            foreach($request->position as $p){
                $sasaran = new SasaranPelatihan();
                $sasaran->position_id = $p;

                $pelatihan->sasaran()->save($sasaran);
            }

            Session::flash('success','Data materi pelatihan berhasil ditambahkan');
        // }
        // else Session::flash('danger','Data materi pelatihan sudah pernah ditambahkan');

        return redirect()->route('pelatihan.materi.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $role = $request->user()->role->name;

        $pelatihan = Pelatihan::find($id);

        if($pelatihan){
            if($role == 'etm'){
                $etl = PegawaiJabatan::whereHas('jabatan',function($query){
                    $query->where('name','Kepala Divisi Edukasi');
                })->with('pegawaiUnit.pegawai')->get()->pluck('pegawaiUnit.pegawai')->where('active_status_id',1)->sortByDesc('id')->first();
                $peserta = null;
                if($pelatihan->active_status_id == 1){
                    $sasaran = $pelatihan->sasaran()->pluck('position_id');
                    $unit = JabatanUnit::whereIn('id',$sasaran)->pluck('unit_id')->unique();
                    $i = 1;
                    foreach($unit as $u){
                        $posisi = JabatanUnit::whereIn('id',$sasaran)->where('unit_id',$u)->pluck('position_id');
                        $pegawai = Pegawai::aktif()->whereHas('units',function($query)use($u,$posisi){
                            $query->where('unit_id',$u)->whereHas('jabatans',function($query)use($posisi){
                                $query->whereIn('position_id',$posisi);
                            });
                        })->get();
                        if($i == 1)
                            $peserta = $pegawai;
                        else
                            $peserta = $peserta->concat($pegawai);

                        $i++;
                    }

                    $peserta = $peserta->unique()->all();
                }
                elseif($pelatihan->active_status_id == 2){
                    $peserta = $pelatihan->presensi()->whereHas('status')->get();
                    $role = 'read-only';
                }
                
                return view('kepegawaian.'.$role.'.pelatihan_materi_tampil', compact('pelatihan','etl','peserta'));
            }
            elseif(in_array($role,['kepsek','wakasek'])){
                $peserta = $pelatihan->presensi()->whereHas('pegawai',function($query)use($request){
                    $query->whereHas('units',function($query) use($request){
                        $query->where('unit_id',$request->user()->pegawai->unit_id);
                    });
                })->whereHas('status')->get();
            }
            else{
                $peserta = $pelatihan->presensi()->whereHas('status')->get();
            }

            return view('kepegawaian.read-only.pelatihan_materi_tampil', compact('pelatihan','peserta'));
        }
        
        else return redirect()->route('pelatihan.materi.index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $role = $request->user()->role->name;

        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;

        if($pelatihan){
            if($role == 'etm'){
                $tahunajaran = TahunAjaran::find($pelatihan->academic_year_id);
                $unit = Unit::all();
                $pegawai = Pegawai::select('name','id')->whereNotNull(['position_id','unit_id'])->whereHas('units',function($query){
                    $query->has('jabatans');
                })->aktif()->orderBy('name')->get();
                $sasaran = $pelatihan->sasaran()->pluck('position_id');
                $jabatan = JabatanUnit::all();
                $semester = $tahunajaran->semester()->orderBy('semester_id')->get();
                $status = StatusWajib::all();

                return view('kepegawaian.'.$role.'.pelatihan_materi_ubah', compact('pelatihan',
                    'unit','pegawai','sasaran','jabatan','semester','status'));
            }
            elseif($role == 'etl'){
                if($pelatihan->active_status_id != 2){
                    if($pelatihan->education_acc_status_id != 1){
                        $acc = StatusAcc::all();

                        return view('kepegawaian.'.$role.'.pelatihan_materi_validasi', compact('pelatihan','acc'));
                    }
                    else{
                        $pegawai = Pegawai::select('name','id')->whereNotNull(['position_id','unit_id'])->whereHas('units',function($query){
                            $query->has('jabatans');
                        })->aktif()->orderBy('name')->get();

                        return view('kepegawaian.'.$role.'.pelatihan_materi_ubah', compact('pelatihan','pegawai'));
                    }
                }
                else{
                    return "Ups, tidak dapat memuat data";
                }
            }
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
        $role = $request->user()->role->name;

        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;
        $nama = $pelatihan ? $pelatihan->name : null;

        if($pelatihan){
            if($role == 'etm'){
                if($pelatihan->education_acc_status_id != 1 && $pelatihan->active_status_id != 2){
                    $messages = [
                        'name.required' => 'Mohon tuliskan materi pelatihan',
                        'position.required' => 'Mohon tentukan sasaran pelatihan',
                        'speaker.required_if' => 'Mohon tentukan narasumber pelatihan',
                        'speaker_name.required_if' => 'Mohon tentukan narasumber pelatihan',
                        'semester.required' => 'Mohon pilih salah satu semester',
                        'status.required' => 'Mohon tentukan sifat pelatihan',
                        'organizer.required' => 'Mohon tentukan penyelenggara pelatihan'
                    ];

                    $this->validate($request, [
                        'name' => 'required',
                        'position' => 'required',
                        'date' => 'nullable|date',
                        'speaker_category' => 'required',
                        'speaker' => 'required_if:speaker_category,1',
                        'speaker_name' => 'required_if:speaker_category,2',
                        'semester' => 'required',
                        'status' => 'required',
                        'organizer' => 'required'
                    ], $messages);

                    $tahunajaran = TahunAjaran::find($pelatihan->academic_year_id);
                    $semester = $tahunajaran->semester()->where('semester_id',$tahunajaran->academic_year.'-'.$request->semester)->first();
                    $status = StatusWajib::find($request->status);

                    if($semester && $status){
                        $existing_pelatihan = 0;
                        // if($pelatihan->name != $request->name || $pelatihan->semester_id != $semester->id){
                        //     $existing_pelatihan = Pelatihan::where(['name' => $request->name, 'academic_year_id' => $tahunajaran->id, 'semester_id' => $semester->id])->count();
                        // }

                        if($existing_pelatihan == 0){
                            $pelatihan->name = $request->name;
                            $pelatihan->desc = isset($request->desc) ? $request->desc : null;
                            $pelatihan->date = isset($request->date) ? Date::parse($request->date) : null;
                            $pelatihan->place = isset($request->place) ? $request->place : null;
                            $pelatihan->speaker_id = isset($request->speaker) && $request->speaker_category == 1 ? $request->speaker : null;
                            $pelatihan->speaker_name = isset($request->speaker_name) && $request->speaker_category == 2 ? $request->speaker_name : null;
                            $pelatihan->semester_id = isset($semester) ? $semester->id : null;
                            $pelatihan->mandatory_status_id = isset($status) ? $status->id : null;
                            $pelatihan->organizer_id = isset($request->organizer) ? $request->organizer : null;

                            $sasaran_ids = $pelatihan->sasaran()->pluck('id');

                            $i = 0;
                            $len = count($request->position);

                            if($len > 0){
                                foreach($request->position as $p){
                                    if(count($sasaran_ids) > 0){
                                        if($i < $len){
                                            $sasaran = SasaranPelatihan::find($sasaran_ids->shift());
                                            $sasaran->position_id = $p;
                                            $sasaran->save();
                                        }
                                    }
                                    else{
                                        $sasaran = new SasaranPelatihan();
                                        $sasaran->position_id = $p;

                                        $pelatihan->sasaran()->save($sasaran);
                                    }

                                    $i++;
                                }

                                if(count($sasaran_ids) > 0) SasaranPelatihan::destroy($sasaran_ids);
                            }

                            $pelatihan->save();

                            Session::flash('success','Data materi pelatihan berhasil diubah');
                        }

                        else Session::flash('danger','Data materi pelatihan sudah ada');
                        
                    }

                    else Session::flash('danger','Data materi pelatihan gagal diubah');
                }
                
                elseif($pelatihan->education_acc_status_id == 1 && $pelatihan->active_status_id != 2){
                    $messages = [
                        'date.date' => 'Mohon periksa kembali format tanggal',
                    ];

                    $this->validate($request, [
                        'date' => 'nullable|date'
                    ], $messages);

                    $pelatihan->desc = isset($request->desc) ? $request->desc : null;
                    $pelatihan->date = isset($request->date) ? Date::parse($request->date) : null;
                    $pelatihan->place = isset($request->place) ? $request->place : null;

                    $pelatihan->save();

                    Session::flash('success','Data materi pelatihan berhasil diubah');
                }
            }

            elseif($role == 'etl'){
                if($pelatihan->active_status_id != 2){
                    if($pelatihan->education_acc_status_id != 1){
                        $messages = [
                            'acc_status.required' => 'Mohon tentukan persetujuan'
                        ];

                        $this->validate($request, [
                            'acc_status' => 'required'
                        ], $messages);

                        $pelatihan->education_acc_id = $request->user()->pegawai->id;
                        $pelatihan->education_acc_status_id = $request->acc_status;
                        $pelatihan->education_acc_time = Date::now('Asia/Jakarta');

                        $pelatihan->save();

                        Session::flash('success','Data '.$nama.' berhasil diubah');
                    }
                    else{
                        $messages = [
                            'name.required' => 'Mohon tuliskan materi pelatihan',
                            'speaker.required_if' => 'Mohon tentukan narasumber pelatihan',
                            'speaker_name.required_if' => 'Mohon tentukan narasumber pelatihan'
                        ];

                        $this->validate($request, [
                            'name' => 'required',
                            'speaker_category' => 'required',
                            'speaker' => 'required_if:speaker_category,1',
                            'speaker_name' => 'required_if:speaker_category,2',
                        ], $messages);

                        $pelatihan->name = $request->name;
                        $pelatihan->desc = isset($request->desc) ? $request->desc : null;
                        $pelatihan->speaker_id = isset($request->speaker) && $request->speaker_category == 1 ? $request->speaker : null;
                        $pelatihan->speaker_name = isset($request->speaker_name) && $request->speaker_category == 2 ? $request->speaker_name : null;

                        $pelatihan->save();

                        Session::flash('success','Data '.$nama.' berhasil diubah');
                    }
                }
                else Session::flash('danger','Data '.$nama.' gagal diubah');
            }
        }

        return redirect()->route('pelatihan.materi.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAttribute(Request $request)
    {
        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;

        if(!$request->name || ($request->name && !in_array($request->name,['date','place']))){
            $response = collect([
                'status' => 'danger',
                'message' => 'Perubahan data pelatihan gagal disimpan'
            ]);

            return $response->toJson();
        }

        if($pelatihan && $pelatihan->active_status_id != 2){
            if($request->name == 'date'){
                $pelatihan->date = $request->value ? Date::parse($request->value) : null;
                $pelatihan->save();
            }
            elseif($request->name == 'place'){
                $pelatihan->place = $request->value ? $request->value : null;
                $pelatihan->save();
            }

            $pelatihan->fresh();
            
            $response = collect([
                'status' => 'success',
                'message' => 'Perubahan data pelatihan berhasil disimpan',
                'date' => $pelatihan->date ? Date::parse($pelatihan->date)->format('Y-m-d') : null,
                'date_id' => $pelatihan->date ? $pelatihan->dateFullId : null,
            ]);

            return $response->toJson();
        }
        else{
            $response = collect([
                'status' => 'danger',
                'message' => 'Pelatihan tidak ditemukan'
            ]);

            return $response->toJson();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $role = $request->user()->role->name;
        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;
        $nama = $pelatihan ? $pelatihan->name : null;

        if($pelatihan && (($role == 'etm' && (!$pelatihan->education_acc_status_id || $pelatihan->education_acc_status_id == 2)) || ($role == 'etl' && $pelatihan->education_acc_status_id == 1)) && $pelatihan->active_status_id != 2){
            $pelatihan->presensi()->delete();
            $pelatihan->sasaran()->delete();
            $pelatihan->delete();

            Session::flash('success','Data '.$nama.' berhasil dihapus');
        }

        else Session::flash('danger','Data '.$nama.' gagal dihapus');

        return redirect()->route('pelatihan.materi.index');
    }

    /**
     * End the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function end(Request $request)
    {
        $pelatihan = $request->id ? Pelatihan::find($request->id) : null;
        $nama = $pelatihan ? $pelatihan->name : null;

        if($pelatihan && $pelatihan->education_acc_status_id == 1){
            if($pelatihan->active_status_id != 2){
                $counter = Setting::where('name','training_counter')->first();
                $training_counter = ($counter->value)+1;
                
                $roman_month = $this->romanMonth();
                $year = Date::now('Asia/Jakarta')->format('Y');

                $pelatihan->number = $training_counter.'/YYS/'.$roman_month.'/'.$year;
                $pelatihan->speaker_name = $pelatihan->speaker;
                $pelatihan->active_status_id = 2;

                $pelatihan->presensi()->whereNull('presence_status_id')->delete();

                $pelatihan->save();

                $counter->value = $training_counter;
                $counter->save();

                Session::flash('success','Data '.$nama.' berhasil disimpan');
            }
            else Session::flash('success','Pelatihan '.$nama.' sudah dinyatakan selesai');
        }

        elseif(!$pelatihan->education_acc_status_id || $pelatihan->education_acc_status_id == 2) Session::flash('danger','Data belum dapat disimpan sampai ETL menyetujui pelatihan ini');

        else Session::flash('danger','Data '.$nama.' gagal disimpan');

        return redirect()->route('pelatihan.materi.detail',['id' => $pelatihan->id]);
    }

    /**
     * Generate roman month.
     */
    public function romanMonth(){
        $month = Date::now('Asia/Jakarta')->format('m');

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

        return $returnValue;
    }
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
}
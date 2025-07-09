<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Kbm\JamPelajaran;
use App\Models\Kbm\TahunAjaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\Kelas;
use App\Models\Level;
use App\Models\Unit;

class JadwalPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;
        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->orderBy('major_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->orderBy('major_id','asc')->get();
        }
        
        return view('kbm.jadwalpelajaran.index',compact('kelases'));
    }

    public function find(Request $request)
    {
        //
        // Validate
        $request->validate([
            'hari' => 'required',
            'kelas' => 'required',
        ]);
        //  coba-coba pindah data
        $kelas = $request->kelas;
        $hari = $request->hari;

        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari);
    }

    public function found($kelas, $hari)
    {
        //
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        // cek daftar guru
        $gurus = Pegawai::whereHas('units',function($query)use($unit){
            $query->where('unit_id',$unit)->whereHas('jabatans',function($query){
                $query->whereIn('position_id',[3,4,5,6,7]);
            });
        })->aktif()->get()->sortBy('name')->all();

        if($unit == 5){
            // cek daftar mapel
            $mapels = MataPelajaran::all();
        }else{
            // cek daftar mapel
            $mapels = MataPelajaran::where('unit_id',$unit)->get();
        }

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // cek data kelas dipilih
        $kelasnya = Kelas::find($kelas);
        // dd($kelasnya);

        // cek daftar jam pelajaran hari yang dipilih
        $jams = JamPelajaran::where('level_id',$kelasnya->level_id)->where('day',$hari)->orderBy('hour_start','asc')->get();

        // check semester yg sedang aktif
        $smsaktif = Semester::where('is_active',1)->first();

        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }
        // kelas daftar kelas

        // jadwal
        $jadwals = JadwalPelajaran::where('class_id',$kelas)->where('semester_id',$smsaktif->id)->where('day',$hari)->orderBy('hour_start','asc')->get();
        
        return view('kbm.jadwalpelajaran.jadwal',compact('kelases','gurus','mapels','jams','kelas','hari','kelasnya','jadwals'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }

        return view('kbm.jadwalpelajaran.tambah',compact('kelases'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $kelas, $hari)
    {
        //

        // Validate
        $request->validate([
            'jam' => 'required',
        ]);

        //cek unit
        $unit = Auth::user()->pegawai->unit_id;
        
        // cek data kelas dipilih
        $kelasnya = Kelas::find($kelas);
        
        // cek daftar jam pelajaran hari yang dipilih
        $jam = JamPelajaran::find($request->jam);

        // check tahun akademik yg sedang aktif
        $semester = Semester::where('is_active',1)->first();

        // create to table
        JadwalPelajaran::create([
            'day' => $hari,
            'class_id' => $kelas,
            'schedule_id' => $request->jam,
            'hour_start' => $jam->hour_start,
            'hour_end' => $jam->hour_end,
            'teacher_id' => $request->guru,
            'subject_id' => $request->mapel,
            'level_id' => $kelasnya->level_id,
            'semester_id' => $semester->id,
            'description' => $jam->description,
        ]);

        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari)->with('success','Tambah Jadwal Berhasil');

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
    public function update(Request $request, $kelas, $hari, $id)
    {
        //
        $jampel = JamPelajaran::find($request->jam);
        

        $jadwal = JadwalPelajaran::find($id);
        $jadwal->hour_start = $jampel->hour_start;
        $jadwal->hour_end = $jampel->hour_end;
        $jadwal->description = $jampel->description;
        $jadwal->schedule_id = $request->jam;
        $jadwal->teacher_id = $request->guru;
        $jadwal->subject_id = $request->mapel;
        $jadwal->save();

        // dd($pesan);

        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari)->with('success','Ubah Jadwal Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $kelas, $hari, $id)
    {
        //
        $jadwal = JadwalPelajaran::find($id);
        $jadwal->delete();
        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari)->with('success','Hapus Jadwal Berhasil');
    }

    public function unduh()
    {
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        $siunit= Unit::find($unit);

        if($unit == 5){
            // check level
            $levels = Level::all();
        }else{
            // check level
            $levels = Level::where('unit_id',$unit)->get();
        }

        // init sheet
        $spreadsheet = new Spreadsheet;
        $sheet = 0;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // check semester yg sedang aktif
        $semestersekarang = Semester::where('is_active',1)->first();

        // init column & row
        $columninit = 'B';
        $rowinit = 7;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];

        // dd($levels);
        foreach($levels as $level)
        {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheet)
            ->setCellValue('B2', 'Jadwal Pelajaran Kelas '.$level->level);
            $spreadsheet->getActiveSheet($sheet)->setTitle("Kelas ".$level->level_romawi);
            // SENIN
            // init column & row for waktu
            $columnwaktu = $columninit;
            $rowwaktu = $rowinit;

            $kolomawal = $columnwaktu;
            $rowawal = $rowwaktu;
            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Senin');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Senin')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, date("h:i", $jam->hour_start).'-'.$jam->hour_end);
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu,  Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            // pindah 1 row ke bawah
            $rowwaktu++;
            $columnwaktu++;
            $columnwaktu++;
            $columselasa = $columnwaktu;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;
            $rowakhir = $rowkelas;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columninit;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari senin
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Senin')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->jam ? $jadwal->jam->description : null);
                        // dd($jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);

            // Selasa
            // init column & row for waktu
            $columnwaktu = $columselasa;
            $kolomawal = $columnwaktu;
            $rowwaktu = $rowinit;
            $rowawal = $rowwaktu;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Selasa');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Selasa')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            // pindah 1 row ke bawah
            $rowwaktu++;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;
            $rowakhir = $rowkelas;
            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columselasa;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari selasa
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Selasa')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);

            $rowkelas++;
            $rowkelas++;
            // Rabu
            // init column & row for waktu
            $columnwaktu = $columninit;
            $rowwaktu = $rowkelas;
            $rowinitkamis = $rowkelas;
            $kolomawal = $columnwaktu;
            $rowawal = $rowinitkamis;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Rabu');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Rabu')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            // pindah 1 row ke bawah
            $rowwaktu++;
            $columnwaktu++;
            $columnwaktu++;
            $columselasa = $columnwaktu;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columninit;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Rabu
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Rabu')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);


            // Kamis
            // init column & row for waktu
            $columnwaktu = $columselasa;
            $rowwaktu = $rowinitkamis;
            $kolomawal = $columnwaktu;
            $rowawal = $rowwaktu;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Kamis');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Kamis')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowwaktu.':'.$kolomakhir.$rowwaktu)->applyFromArray($styleArray);
            // pindah 1 row ke bawah
            $rowwaktu++;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columselasa;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Kamis
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Kamis')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);

            $rowkelas++;
            $rowkelas++;
            $rowkelas++;

            // Jum'at
            // init column & row for waktu
            $columnwaktu = $columninit;
            $rowwaktu = $rowkelas;
            $rowinitkamis = $rowkelas;
            $kolomawal = $columnwaktu;
            $rowawal = $rowinitkamis;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, "Jum'at");
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day',"Jum'at")->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowwaktu.':'.$kolomakhir.$rowwaktu)->applyFromArray($styleArray);
            // pindah 1 row ke bawah
            $rowwaktu++;
            $columnwaktu++;
            $columnwaktu++;
            $columselasa = $columnwaktu;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columninit;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Rabu
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day',"Jum'at")
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir=$rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);


            // Sabtu
            // init column & row for waktu
            $columnwaktu = $columselasa;
            $rowwaktu = $rowinitkamis;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Sabtu');
            $kolomawal = $columnwaktu;
            $rowawal = $rowwaktu;
            // dd($kolomawal.$rowawal.' '.$columnwaktu.$rowwaktu);
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Sabtu')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowwaktu.':'.$kolomakhir.$rowwaktu)->applyFromArray($styleArray);
            // pindah 1 row ke bawah
            $rowwaktu++;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columselasa;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Kamis
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Sabtu')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);
            $kolomawal = 'B';
            $kolomakhir = 'AG';
            while($kolomawal !== $kolomakhir){
                $spreadsheet->getActiveSheet()->getColumnDimension($kolomawal)->setWidth(15);
                $kolomawal++;
            }
            $sheet++;
        }
        $writer = new Xls($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Jadwal-Pelajaran-'.$siunit->name.'.xls"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');


    }

    public function guruMapel($mapel)
    {
        $guru = Pegawai::whereHas('skbmDetail',function($q) use ($mapel){
            $q->where('subject_id',$mapel);
        })->aktif()->pluck("name","id");
        return json_encode($guru);
    }

    public function guruUnit()
    {
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        // cek daftar guru
        $guru = Pegawai::whereHas('units',function($query)use($unit){
            $query->where('unit_id',$unit)->whereHas('jabatans',function($query){
                $query->whereIn('position_id',[3,4,5,6,7]);
            });
        })->aktif()->get()->sortBy('name')->pluck("name","id");
        return json_encode($guru);
    }
}
=======
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\MataPelajaran;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Kbm\JamPelajaran;
use App\Models\Kbm\TahunAjaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\Kelas;
use App\Models\Level;
use App\Models\Unit;

class JadwalPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;
        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->orderBy('major_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->orderBy('major_id','asc')->get();
        }
        
        return view('kbm.jadwalpelajaran.index',compact('kelases'));
    }

    public function find(Request $request)
    {
        //
        // Validate
        $request->validate([
            'hari' => 'required',
            'kelas' => 'required',
        ]);
        //  coba-coba pindah data
        $kelas = $request->kelas;
        $hari = $request->hari;

        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari);
    }

    public function found($kelas, $hari)
    {
        //
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        // cek daftar guru
        $gurus = Pegawai::whereHas('units',function($query)use($unit){
            $query->where('unit_id',$unit)->whereHas('jabatans',function($query){
                $query->whereIn('position_id',[3,4,5,6,7]);
            });
        })->aktif()->get()->sortBy('name')->all();

        if($unit == 5){
            // cek daftar mapel
            $mapels = MataPelajaran::all();
        }else{
            // cek daftar mapel
            $mapels = MataPelajaran::where('unit_id',$unit)->get();
        }

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // cek data kelas dipilih
        $kelasnya = Kelas::find($kelas);
        // dd($kelasnya);

        // cek daftar jam pelajaran hari yang dipilih
        $jams = JamPelajaran::where('level_id',$kelasnya->level_id)->where('day',$hari)->orderBy('hour_start','asc')->get();

        // check semester yg sedang aktif
        $smsaktif = Semester::where('is_active',1)->first();

        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }
        // kelas daftar kelas

        // jadwal
        $jadwals = JadwalPelajaran::where('class_id',$kelas)->where('semester_id',$smsaktif->id)->where('day',$hari)->orderBy('hour_start','asc')->get();
        
        return view('kbm.jadwalpelajaran.jadwal',compact('kelases','gurus','mapels','jams','kelas','hari','kelasnya','jadwals'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        if($unit == 5){
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }else{
            $kelases = Kelas::where('unit_id',$unit)->where('academic_year_id',$tahunsekarang->id)->orderBy('level_id','asc')->get();
        }

        return view('kbm.jadwalpelajaran.tambah',compact('kelases'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $kelas, $hari)
    {
        //

        // Validate
        $request->validate([
            'jam' => 'required',
        ]);

        //cek unit
        $unit = Auth::user()->pegawai->unit_id;
        
        // cek data kelas dipilih
        $kelasnya = Kelas::find($kelas);
        
        // cek daftar jam pelajaran hari yang dipilih
        $jam = JamPelajaran::find($request->jam);

        // check tahun akademik yg sedang aktif
        $semester = Semester::where('is_active',1)->first();

        // create to table
        JadwalPelajaran::create([
            'day' => $hari,
            'class_id' => $kelas,
            'schedule_id' => $request->jam,
            'hour_start' => $jam->hour_start,
            'hour_end' => $jam->hour_end,
            'teacher_id' => $request->guru,
            'subject_id' => $request->mapel,
            'level_id' => $kelasnya->level_id,
            'semester_id' => $semester->id,
            'description' => $jam->description,
        ]);

        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari)->with('success','Tambah Jadwal Berhasil');

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
    public function update(Request $request, $kelas, $hari, $id)
    {
        //
        $jampel = JamPelajaran::find($request->jam);
        

        $jadwal = JadwalPelajaran::find($id);
        $jadwal->hour_start = $jampel->hour_start;
        $jadwal->hour_end = $jampel->hour_end;
        $jadwal->description = $jampel->description;
        $jadwal->schedule_id = $request->jam;
        $jadwal->teacher_id = $request->guru;
        $jadwal->subject_id = $request->mapel;
        $jadwal->save();

        // dd($pesan);

        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari)->with('success','Ubah Jadwal Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $kelas, $hari, $id)
    {
        //
        $jadwal = JadwalPelajaran::find($id);
        $jadwal->delete();
        return redirect('/kependidikan/kbm/pelajaran/jadwal-pelajaran/'.$kelas.'/'.$hari)->with('success','Hapus Jadwal Berhasil');
    }

    public function unduh()
    {
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        $siunit= Unit::find($unit);

        if($unit == 5){
            // check level
            $levels = Level::all();
        }else{
            // check level
            $levels = Level::where('unit_id',$unit)->get();
        }

        // init sheet
        $spreadsheet = new Spreadsheet;
        $sheet = 0;

        // check tahun akademik yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active',1)->first();

        // check semester yg sedang aktif
        $semestersekarang = Semester::where('is_active',1)->first();

        // init column & row
        $columninit = 'B';
        $rowinit = 7;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];

        // dd($levels);
        foreach($levels as $level)
        {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheet)
            ->setCellValue('B2', 'Jadwal Pelajaran Kelas '.$level->level);
            $spreadsheet->getActiveSheet($sheet)->setTitle("Kelas ".$level->level_romawi);
            // SENIN
            // init column & row for waktu
            $columnwaktu = $columninit;
            $rowwaktu = $rowinit;

            $kolomawal = $columnwaktu;
            $rowawal = $rowwaktu;
            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Senin');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Senin')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, date("h:i", $jam->hour_start).'-'.$jam->hour_end);
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu,  Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            // pindah 1 row ke bawah
            $rowwaktu++;
            $columnwaktu++;
            $columnwaktu++;
            $columselasa = $columnwaktu;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;
            $rowakhir = $rowkelas;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columninit;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari senin
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Senin')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->jam ? $jadwal->jam->description : null);
                        // dd($jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);

            // Selasa
            // init column & row for waktu
            $columnwaktu = $columselasa;
            $kolomawal = $columnwaktu;
            $rowwaktu = $rowinit;
            $rowawal = $rowwaktu;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Selasa');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Selasa')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            // pindah 1 row ke bawah
            $rowwaktu++;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;
            $rowakhir = $rowkelas;
            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columselasa;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari selasa
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Selasa')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);

            $rowkelas++;
            $rowkelas++;
            // Rabu
            // init column & row for waktu
            $columnwaktu = $columninit;
            $rowwaktu = $rowkelas;
            $rowinitkamis = $rowkelas;
            $kolomawal = $columnwaktu;
            $rowawal = $rowinitkamis;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Rabu');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Rabu')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            // pindah 1 row ke bawah
            $rowwaktu++;
            $columnwaktu++;
            $columnwaktu++;
            $columselasa = $columnwaktu;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columninit;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Rabu
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Rabu')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);


            // Kamis
            // init column & row for waktu
            $columnwaktu = $columselasa;
            $rowwaktu = $rowinitkamis;
            $kolomawal = $columnwaktu;
            $rowawal = $rowwaktu;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Kamis');
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Kamis')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowwaktu.':'.$kolomakhir.$rowwaktu)->applyFromArray($styleArray);
            // pindah 1 row ke bawah
            $rowwaktu++;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columselasa;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Kamis
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Kamis')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);

            $rowkelas++;
            $rowkelas++;
            $rowkelas++;

            // Jum'at
            // init column & row for waktu
            $columnwaktu = $columninit;
            $rowwaktu = $rowkelas;
            $rowinitkamis = $rowkelas;
            $kolomawal = $columnwaktu;
            $rowawal = $rowinitkamis;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, "Jum'at");
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day',"Jum'at")->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowwaktu.':'.$kolomakhir.$rowwaktu)->applyFromArray($styleArray);
            // pindah 1 row ke bawah
            $rowwaktu++;
            $columnwaktu++;
            $columnwaktu++;
            $columselasa = $columnwaktu;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columninit;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Rabu
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day',"Jum'at")
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir=$rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);


            // Sabtu
            // init column & row for waktu
            $columnwaktu = $columselasa;
            $rowwaktu = $rowinitkamis;

            // show hari pada kolom pertama 
            $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, 'Sabtu');
            $kolomawal = $columnwaktu;
            $rowawal = $rowwaktu;
            // dd($kolomawal.$rowawal.' '.$columnwaktu.$rowwaktu);
            $columnwaktu++;

            // check jam pelajaran pada level
            $jams = JamPelajaran::select('hour_start','hour_end')->where('level_id',$level->id)->where('day','Sabtu')->orderBy('hour_start','asc')->get();
            // show jam pelajaran
            foreach($jams as $jam)
            {
                // cetak
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnwaktu.$rowwaktu, Carbon::parse($jam->hour_start)->format('H:i').'-'.Carbon::parse($jam->hour_end)->format('H:i'));
                // pindah 1 colum ke kanan
                $kolomakhir=$columnwaktu;
                $columnwaktu++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowwaktu.':'.$kolomakhir.$rowwaktu)->applyFromArray($styleArray);
            // pindah 1 row ke bawah
            $rowwaktu++;
            // init row mapel per kelas
            $rowkelas = $rowwaktu;

            //cari daftar kelas yg ada pada tahun pelajaran ini
            $kelases = Kelas::where('academic_year_id',$tahunsekarang->id)->where('level_id',$level->id)->orderBy('major_id','asc')->orderBy('level_id','asc')->get();
            // dd($kelases);
            foreach($kelases as $kelas)
            {
                // init column lagi klo ganti kelas
                $columnkelas = $columselasa;

                // cetak nama kelas
                $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $kelas->level->name.' '.$kelas->namakelases->class_name);

                // next column
                $columnkelas++;

                // cari jadwal si kelas pada semester aktif dan hari Kamis
                $jadwals = JadwalPelajaran::select('subject_id','description','schedule_id')
                ->where('class_id',$kelas->id)
                ->where('semester_id',$semestersekarang->id)
                ->where('day','Sabtu')
                ->orderBy('hour_start','asc')
                ->get();

                foreach($jadwals as $jadwal)
                {
                    //check ada jadwal ga??
                    if($jadwal->subject_id == null)
                    {
                        // cetak deskripsi jam mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->description);
                        // $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, '-');
                    }
                    else
                    {
                        // cetak mapel
                        $spreadsheet->setActiveSheetIndex($sheet)->setCellValue($columnkelas.$rowkelas, $jadwal->mapel->subject_name);
                    }

                    $columnkelas++;
                }
                $rowakhir = $rowkelas;
                $rowkelas++;
            }
            $spreadsheet->setActiveSheetIndex($sheet)->getStyle($kolomawal.$rowawal.':'.$kolomakhir.$rowakhir)->applyFromArray($styleArray);
            $kolomawal = 'B';
            $kolomakhir = 'AG';
            while($kolomawal !== $kolomakhir){
                $spreadsheet->getActiveSheet()->getColumnDimension($kolomawal)->setWidth(15);
                $kolomawal++;
            }
            $sheet++;
        }
        $writer = new Xls($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Jadwal-Pelajaran-'.$siunit->name.'.xls"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');


    }

    public function guruMapel($mapel)
    {
        $guru = Pegawai::whereHas('skbmDetail',function($q) use ($mapel){
            $q->where('subject_id',$mapel);
        })->aktif()->pluck("name","id");
        return json_encode($guru);
    }

    public function guruUnit()
    {
        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        // cek daftar guru
        $guru = Pegawai::whereHas('units',function($query)use($unit){
            $query->where('unit_id',$unit)->whereHas('jabatans',function($query){
                $query->whereIn('position_id',[3,4,5,6,7]);
            });
        })->aktif()->get()->sortBy('name')->pluck("name","id");
        return json_encode($guru);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

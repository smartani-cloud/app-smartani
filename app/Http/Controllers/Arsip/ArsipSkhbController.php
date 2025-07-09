<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Arsip;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Arsip;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Unit;

class ArsipSkhbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // teacher = employee_id
        $teacher = Auth::user()->pegawai->id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // check class
        $kelas = Kelas::where('teacher_id', $teacher)->where('academic_year_id', $tahunsekarang->id)->first();

        if ($kelas == null) {
            $siswas = null;
        } else {
            // search student class
            $siswas = Siswa::where('class_id', $kelas->id)->with('identitas:id,student_name')->get()->sortBy('identitas.student_name')->values();
        }

        return view('penilaian.arsipskhb.index', compact('siswas', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        // validasi
        $this->validate($request, [
            'siswa' => 'required',
            'file' => 'required|mimes:pdf|max:10000',
        ]);
        // dd($request->file);

        $archive_id = 2;

        // check unit_id
        $unit = Auth::user()->pegawai->unit_id;

        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // menyimpan data file yang diupload ke variabel $file
        $file = $request->file('file');

        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'arsip';

        $siswa = Siswa::find($request->siswa);

        $extFile = $file->getClientOriginalExtension();

        $FileName = 'SKHB_' . $siswa->identitas->student_nis . '_' . $siswa->identitas->student_name . '.' . $extFile;

        // upload ke folder file_siswa di dalam folder public
        $file->move('arsip', $FileName);


        // create to table
        Arsip::create([
            'student_id' => $request->siswa,
            'academic_year_id' => $tahunsekarang->id,
            'unit_id' => $unit,
            'file' => $FileName,
            'archive_type_id' => $archive_id,
        ]);

        return redirect('/kependidikan/skhb/arsip')->with('sukses', 'Unggah SKHB Siswa Berhasil');
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
}
=======
<?php

namespace App\Http\Controllers\Arsip;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Arsip;
use App\Models\Siswa\Siswa;
use App\Models\Kbm\Kelas;
use App\Models\Unit;

class ArsipSkhbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // teacher = employee_id
        $teacher = Auth::user()->pegawai->id;
        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // check class
        $kelas = Kelas::where('teacher_id', $teacher)->where('academic_year_id', $tahunsekarang->id)->first();

        if ($kelas == null) {
            $siswas = null;
        } else {
            // search student class
            $siswas = Siswa::where('class_id', $kelas->id)->with('identitas:id,student_name')->get()->sortBy('identitas.student_name')->values();
        }

        return view('penilaian.arsipskhb.index', compact('siswas', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        // validasi
        $this->validate($request, [
            'siswa' => 'required',
            'file' => 'required|mimes:pdf|max:10000',
        ]);
        // dd($request->file);

        $archive_id = 2;

        // check unit_id
        $unit = Auth::user()->pegawai->unit_id;

        // check tahun yg sedang aktif
        $tahunsekarang = TahunAjaran::where('is_active', 1)->first();

        // menyimpan data file yang diupload ke variabel $file
        $file = $request->file('file');

        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'arsip';

        $siswa = Siswa::find($request->siswa);

        $extFile = $file->getClientOriginalExtension();

        $FileName = 'SKHB_' . $siswa->identitas->student_nis . '_' . $siswa->identitas->student_name . '.' . $extFile;

        // upload ke folder file_siswa di dalam folder public
        $file->move('arsip', $FileName);


        // create to table
        Arsip::create([
            'student_id' => $request->siswa,
            'academic_year_id' => $tahunsekarang->id,
            'unit_id' => $unit,
            'file' => $FileName,
            'archive_type_id' => $archive_id,
        ]);

        return redirect('/kependidikan/skhb/arsip')->with('sukses', 'Unggah SKHB Siswa Berhasil');
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
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

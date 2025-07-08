<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Kbm\TahunAjaran;
use App\Models\Kbm\Semester;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tahuns = TahunAjaran::orderBy('academic_year_start', 'asc')->get();
        $aktif = TahunAjaran::where('is_active',1)->first();
        $semesters = Semester::where('academic_year_id',$aktif->id)->orderBy('semester_id','asc')->get();
        $smsaktif = Semester::where('is_active',1)->first();
        // dd($aktif);
        return view('kbm.tahunajaran', compact('tahuns','aktif','semesters','smsaktif'));
    }

    public function ubah(Request $request)
    {
        $id = $request->tahun;
        $aktif = TahunAjaran::where('is_active',1)->first();
        if($aktif !== null){
            $nonaktif = $aktif->id;
            $tahun = TahunAjaran::find($nonaktif);
            $tahun->is_active = 0;
            $tahun->save();
            // dd($nonaktif);
        }
        $tahun = TahunAjaran::find($id);
        $tahun->is_active = 1;
        $tahun->save();


        $smtaktif = Semester::where('is_active',1)->first();
        if($smtaktif !== null){
            $nonaktif = $smtaktif->id;
            $semester = Semester::find($nonaktif);
            $semester->is_active = 0;
            $semester->save();
            // dd($nonaktif);
        }
        $semester = Semester::where('academic_year_id',$id)->first();
        $semester->is_active = 1;
        $semester->save();
        return redirect('/kependidikan/kbm/tahun-ajaran');
    }

    public function semesterAktif(Request $request)
    {
        $id = $request->semester;
        $aktif = Semester::where('is_active',1)->first();
        if($aktif !== null){
            $nonaktif = $aktif->id;
            $semester = Semester::find($nonaktif);
            $semester->is_active = 0;
            $semester->save();
            // dd($nonaktif);
        }
        $semester = Semester::find($id);
        $semester->is_active = 1;
        $semester->save();
        return redirect('/kependidikan/kbm/tahun-ajaran');
    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'academic_year_start' => 'required|numeric',
            'academic_year_end' => 'required|numeric',
        ]);

        //create academic_year
        $academic_year = $request->academic_year_start."/".$request->academic_year_end;
        //create semester_id
        $semester_ganjil = $academic_year."-1";
        $semester_genap = $academic_year."-2";
        // dd($academic_year);

        TahunAjaran::create([
    		'academic_year_start' => $request->academic_year_start,
            'academic_year_end' => $request->academic_year_end,
            'academic_year' => $academic_year,
            'is_active' => 0,
        ]);
        
        //save last academic_year_id
        $academic_year = TahunAjaran::orderBy('id', 'desc')->first();


        //create semester ganjil
        Semester::create([
    		'semester_id' => $semester_ganjil,
            'semester' => 'Ganjil',
            'academic_year_id' => $academic_year->id,
            'is_active' => 0,
        ]);
        
        //create semester genap
        Semester::create([
    		'semester_id' => $semester_genap,
            'semester' => 'Genap',
            'academic_year_id' => $academic_year->id,
            'is_active' => 0,
        ]);
        
        return redirect('/kependidikan/kbm/tahun-ajaran');
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

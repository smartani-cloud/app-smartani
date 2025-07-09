<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Kbm\JadwalPelajaran;
use App\Models\Kbm\JamPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Level;

class JamPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // check unit_id user
        $unit = auth()->user()->pegawai->unit_id;


        if($unit == 5){
            $levels = Level::orderBy('id','asc')->get();
        }else{
            $levels = Level::where('unit_id',$unit)->orderBy('id','asc')->get();
        }

        return view('kbm.jampelajaran.index',compact('levels'));
    }


    public function find(Request $request)
    {
        // Validate
        $request->validate([
            'hari' => 'required',
            'level' => 'required',
        ]);
        //  coba-coba pindah data
        $tingkat = $request->level;
        $hari = $request->hari;

        return redirect('/kependidikan/kbm/pelajaran/waktu-pelajaran/'.$tingkat.'/'.$hari);
    }



    public function found($tingkat, $hari)
    {

        // check unit_id user
        $unit = Auth::user()->pegawai->unit_id;

        $kelas = Level::find($tingkat);

        if($unit == 5){
            $levels = Level::orderBy('id','asc')->get();
        }else{
            $levels = Level::where('unit_id',$unit)->orderBy('id','asc')->get();
        }

        $jams = JamPelajaran::where('level_id',$tingkat)->where('day',$hari)->orderBy('hour_start','asc')->get();

        $used = null;
        foreach($jams as $jam){
            if($jam->jadwalpelajarans()->count() > 0) $used[$jam->id] = 1;
            else $used[$jam->id] = 0;
        }

        return view('kbm.jampelajaran.find',compact('levels','jams','kelas','tingkat','hari','used'));
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
        // Validate
        $request->validate([
            'mulai' => 'required',
            'selesai' => 'required',
            'Keterangan' => 'required',
            'hari' => 'required',
            'level' => 'required',
        ]);
        JamPelajaran::create([
            'hour_start' => $request->mulai,
            'hour_end' => $request->selesai,
            'description' => $request->Keterangan,
            'day' => $request->hari,
            'level_id' => $request->level,
        ]);
        $kelas = Level::find($request->level);

        $pesan = 'Tambah jam pelajaran kelas '.$kelas->level.' hari '.$request->hari.' Berhasil';

        return redirect('/kependidikan/kbm/pelajaran/waktu-pelajaran/'.$kelas->id.'/'.$request->hari)->with('success',$pesan);
        dd($request);


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
        $request->validate([
            'mulai' => 'required',
            'selesai' => 'required',
            'Keterangan' => 'required',
        ]);

        // check semester yg sedang aktif
        $smsaktif = Semester::where('is_active',1)->first();

        $jadwaljuga = JadwalPelajaran::where('schedule_id',$id)->where('semester_id',$smsaktif->id)->get();
        foreach($jadwaljuga as $listjadwal){
            $listjadwal->hour_start = $request->mulai;
            $listjadwal->hour_end = $request->selesai;
            $listjadwal->description = $request->Keterangan;
            $listjadwal->save();
        }
        
    	$jampel = JamPelajaran::find($id);
        $jampel->hour_start = $request->mulai;
        $jampel->hour_end = $request->selesai;
        $jampel->description = $request->Keterangan;
        $jampel->save();

        $kelas = Level::find($jampel->level_id);

        $pesan = 'Ubah jam pelajaran kelas '.$kelas->level.' hari '.$jampel->day.' berhasil';

        // dd($pesan);

        return redirect('/kependidikan/kbm/pelajaran/waktu-pelajaran/'.$kelas->id.'/'.$jampel->day)->with('success',$pesan);
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
    	$kelas = JamPelajaran::find($id);
        $kelas->delete();
        
        return redirect('/kependidikan/kbm/pelajaran/waktu-pelajaran/')->with('success','Hapus Jam Pelajaran Berhasil');
    }
}

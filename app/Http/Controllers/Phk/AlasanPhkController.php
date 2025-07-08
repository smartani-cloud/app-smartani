<?php

namespace App\Http\Controllers\Phk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Phk\AlasanPhk;

class AlasanPhkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alasan = AlasanPhk::orderBy('created_at','desc')->get();

        return view('kepegawaian.etm.alasan_phk_index', compact('alasan'));
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
            'reason.required' => 'Mohon tuliskan alasan PHK',
        ];

        $this->validate($request, [
            'reason' => 'required',
        ], $messages);

        $count = AlasanPhk::where('reason', $request->reason)->count();
        
        if($count < 1){
            $alasan = new AlasanPhk();
            $alasan->reason = $request->reason;
            $alasan->save();

            Session::flash('success','Data alasan PHK berhasil ditambahkan');
        }

        else Session::flash('danger','Data alasan PHK sudah pernah ditambahkan');
        
        return redirect()->route('alasanphk.index');
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
        $alasan = $request->id ? AlasanPhk::find($request->id) : null;

        return view('kepegawaian.etm.alasan_phk_ubah', compact('alasan'));
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
        $messages = [
            'reason.required' => 'Mohon tuliskan alasan terakhir',
        ];

        $this->validate($request, [
            'reason' => 'required',
        ], $messages);

        $alasan = AlasanPhk::find($request->id);
        $count = AlasanPhk::where('reason',$request->reason)->where('id','!=',$request->id)->count();

        if($alasan && $count < 1){
            $alasan->reason = $request->reason;
            $alasan->save();

            Session::flash('success','Data alasan PHK berhasil diubah');
        }

        else Session::flash('danger','Perubahan data alasan PHK gagal disimpan');

        return redirect()->route('alasanphk.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $alasan = AlasanPhk::find($id);
        $dismissal_count = $alasan->phk()->count();

        if($alasan && $dismissal_count < 1){
            $alasan->delete();

            Session::flash('success','Data alasan PHK berhasil dihapus');
        }
        else Session::flash('danger','Data alasan PHK gagal dihapus');

        return redirect()->route('alasanphk.index');
    }
}

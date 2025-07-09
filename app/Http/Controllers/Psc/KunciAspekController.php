<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Setting;

class KunciAspekController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            return view('kepegawaian.pa.psc.kunci_aspek_index', compact('lock'));
        }

        else return redirect()->route('kepegawaian.index');
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
    public function update(Request $request)
    {
        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if(!$lock){
                $lock = new Setting();
                $lock->name = 'psc_aspect_lock_status';
                $lock->value = 1;
                $lock->save();

                $lock->fresh();
            }

            if($request->lock == 'on'){
                $lock->value = 1;
                Session::flash('success','Aspek Evaluasi dan IKU berhasil dibuka');
            }
            else{
                $lock->value = 0;
                Session::flash('success','Aspek Evaluasi dan IKU berhasil dikunci');
            }
            $lock->save();

            return redirect()->route('psc.aspek.kunci.index');
        }

        else return redirect()->route('kepegawaian.index');
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

<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Psc\PscGrade;
use App\Models\Psc\PscGradeSet;
use App\Models\Unit;

class RentangNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $psc = PscGradeSet::orderBy('created_at')->get();

        return view('kepegawaian.pa.psc.rentang_nilai_index', compact('psc'));
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
        $messages = [
            'name.required' => 'Mohon tuliskan nama daftar rentang nilai',
            'grade.required' => 'Mohon tuliskan nilai huruf',
            'start.required' => 'Mohon tuliskan awal rentang',
            'end.required' => 'Mohon tuliskan akhir rentang',
        ];

        $this->validate($request, [
            'name' => 'required',
            'grade' => 'required',
            'start' => 'required',
            'end' => 'required'
        ], $messages);

        $count = PscGradeSet::where('name', $request->name)->count();
        
        if($count < 1){
            $set = new PscGradeSet();
            $set->name = $request->name;
            $set->save();

            $set->fresh();

            foreach($request->grade as $key => $g){
                $grade = new PscGrade();
                $grade->name = $g;
                $grade->start = $request->start[$key];
                $grade->end = $request->end[$key];

                $set->grade()->save($grade);
            }

            Session::flash('success','Daftar rentang nilai berhasil ditambahkan');
        }

        else Session::flash('danger','Daftar rentang nilai sudah pernah ditambahkan');
        
        return redirect()->route('psc.rentang.index');
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
        $usedCount = $request->id ? PscGradeSet::where('id',$request->id)->whereHas('grade.scores',function($q){$q->where('acc_status_id',1);})->count() : null;
        $set = $request->id ? PscGradeSet::find($request->id) : null;

        if(($usedCount && $usedCount > 0)){
            //return "Rentang nilai tidak dapat diubah. Silakan buat daftar baru.";
            return view('kepegawaian.pa.psc.rentang_nilai_detail', compact('set'));
        }
        elseif($set){
            return view('kepegawaian.pa.psc.rentang_nilai_ubah', compact('set'));
        }
        else{
            return "Rentang nilai tidak ditemukan.";
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
        $messages = [
            'name.required' => 'Mohon tuliskan nama daftar rentang nilai',
            'grade.required' => 'Mohon tuliskan nilai huruf',
            'start.required' => 'Mohon tuliskan awal rentang',
            'end.required' => 'Mohon tuliskan akhir rentang',
        ];

        $this->validate($request, [
            'name' => 'required',
            'grade' => 'required',
            'start' => 'required',
            'end' => 'required'
        ], $messages);

        $usedCount = $request->id ? PscGradeSet::where('id',$request->id)->whereHas('grade.scores',function($q){$q->where('acc_status_id',1);})->count() : null;
        $set = PscGradeSet::find($request->id);
        $count = PscGradeSet::where('name', $request->name)->where('id','!=',$request->id)->count();

        if(($usedCount && $usedCount > 0)){
            Session::flash('danger','Rentang nilai tidak dapat diubah. Silakan buat daftar baru.');
        }
        elseif($set && $count < 1){
            $set->name = $request->name;
            $set->save();

            $set->fresh();

            $max = count($request->grade) > $set->grade()->count() ? count($request->grade) : $set->grade()->count();

            for($i=0;$i<$max;$i++){
                if(isset($set->grade[$i])){
                    $grade = $set->grade[$i];
                    if(isset($request->grade[$i])){
                        $grade->name = $request->grade[$i];
                        $grade->start = $request->start[$i];
                        $grade->end = $request->end[$i];
                        $grade->save();
                    }
                    else $grade->delete();
                }
                elseif(isset($request->grade[$i])){
                    $grade = new PscGrade();
                    $grade->name = $request->grade[$i];
                    $grade->start = $request->start[$i];
                    $grade->end = $request->end[$i];

                    $set->grade()->save($grade);
                }
            }

            Session::flash('success','Daftar rentang nilai berhasil diubah');
        }

        else Session::flash('danger','Perubahan daftar rentang nilai gagal disimpan');
        
        return redirect()->route('psc.rentang.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usedCount = PscGradeSet::where('id',$id)->whereHas('grade.scores',function($q){$q->where('acc_status_id',1);})->count();
        $set = PscGradeSet::find($id);
        $used = $set->grade()->has('evaluasiPegawai')->count();

        if(($usedCount && $usedCount > 0)){
            Session::flash('danger','Rentang nilai tidak dapat dihapus');
        }
        elseif($set){
            if($used < 1){
                $set->grade()->delete();
                $set->forceDelete();
            }
            else $set->delete();

            Session::flash('success','Daftar rentang nilai berhasil dihapus');
        }
        else Session::flash('danger','Daftar rentang nilai gagal dihapus');
        
        return redirect()->route('psc.rentang.index');
    }

    /**
     * Activate the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        $set = PscGradeSet::find($id);

        if($set){
            PscGradeSet::where('status_id',1)->update(['status_id' => 2]);
            $set->update(['status_id' => 1]);

            Session::flash('success','Daftar rentang nilai berhasil dipilih');
        }
        else Session::flash('danger','Daftar rentang nilai gagal dipilih');
        
        return redirect()->route('psc.rentang.index');
    }
}

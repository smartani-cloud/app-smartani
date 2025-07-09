<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Penempatan\Jabatan;
use App\Models\Penempatan\KategoriJabatan;
use App\Models\Psc\PaRoleMapping;
use App\Models\Psc\PscRoleMapping;
use App\Models\Unit;

class PemetaanPeranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mapping = Jabatan::select('id','name','category_id')->has('pscRoleMapping')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

        $kategori = KategoriJabatan::select('id','name')->get();

        $jabatan = null;
        foreach($kategori as $k){
            $position = $k->jabatan()->select('id','code','name','category_id')->whereDoesntHave('pscRoleMapping')->aktif()->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

            if($position && count($position) > 0){
                if(!$jabatan){
                    $jabatan = collect($position);
                }
                else{
                    $jabatan = $jabatan->concat(collect($position));
                }
            }
        }

        $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

        $struktural = Jabatan::select('id','name')->where(function($q){
            $q->where('placement_id',1)->orWhereNull('placement_id');
        })->aktif()->get();

        return view('kepegawaian.pa.psc.pemetaan_peran_index', compact('mapping','jabatan','penempatan','struktural'));
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
            'position.required' => 'Mohon pilih salah satu jabatan',
            'create.required' => 'Mohon pilih salah satu jabatan yang dapat membuat aspek',
            'view.required' => 'Mohon pilih salah satu jabatan yang dapat melihat nilai',
        ];

        $this->validate($request, [
            'position' => 'required',
            'create' => 'required',
            'view' => 'required'
        ], $messages);

        $pscMapping = PscRoleMapping::where('target_position_id', $request->position);
        
        if($pscMapping->count() < 1){
            $target = Jabatan::find($request->position);
            if($target){
                $role = PaRoleMapping::select('id','name')->psc()->get();
                foreach($role as $r){
                    $requestItem = $request->{$r->name};

                    if(isset($requestItem)){
                        foreach($requestItem as $i){
                            if(isset($i)){
                                $thisMapping = new PscRoleMapping();
                                $thisMapping->target_position_id = $request->position;
                                $thisMapping->pa_role_mapping_id = $r->id;
                                $thisMapping->position_id = $i;
                                $thisMapping->save();
                            }
                        }
                    }
                }
            }

            Session::flash('success','Data pemetaan peran '.$target->name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data pemetaan peran sudah pernah ditambahkan');
        
        return redirect()->route('psc.peran.index');
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
        $jabatan = $request->id ? Jabatan::find($request->id) : null;

        $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

        $struktural = Jabatan::select('id','name')->where(function($q){
            $q->where('placement_id',1)->orWhereNull('placement_id');
        })->aktif()->get();

        return view('kepegawaian.pa.psc.pemetaan_peran_ubah', compact('jabatan','penempatan','struktural'));
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
            'editCreate.required' => 'Mohon pilih salah satu jabatan yang dapat membuat aspek',
            'editView.required' => 'Mohon pilih salah satu jabatan yang dapat melihat nilai',
        ];

        $this->validate($request, [
            'editCreate' => 'required',
            'editView' => 'required'
        ], $messages);

        $target = Jabatan::find($request->id);

        if($target){
            $role = PaRoleMapping::select('id','name')->psc()->get();
            
            foreach($role as $r){
                $requestName = 'edit'.ucwords($r->name);
                $requestItem = $request->{$requestName};

                if(isset($requestItem)){
                    $thisMapping = $target->pscRoleMapping()->where('pa_role_mapping_id',$r->id)->get();
                    $countMapping = count($thisMapping);

                    $max = count($requestItem) > $countMapping ? count($requestItem) : $countMapping;

                    for($i=0;$i<$max;$i++){
                        if(isset($thisMapping[$i])){
                            $mapping = $thisMapping[$i];
                            if(isset($requestItem[$i])){
                                $mapping->position_id = $requestItem[$i];
                                $mapping->save();
                            }
                            else $mapping->delete();
                        }
                        elseif(isset($requestItem[$i])){
                            $mapping = new PscRoleMapping();
                            $mapping->target_position_id = $request->id;
                            $mapping->pa_role_mapping_id = $r->id;
                            $mapping->position_id = $requestItem[$i];
                            $mapping->save();
                        }
                    }
                }
            }

            Session::flash('success','Data pemetaan peran '.$target->name.' berhasil diubah');
        }

        else Session::flash('danger','Data pemetaan peran gagal disimpan');
        
        return redirect()->route('psc.peran.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jabatan = Jabatan::find($id);

        if($jabatan){
            $jabatan->pscRoleMapping()->delete();

            Session::flash('success','Data pemetaan peran '.$jabatan->name.' berhasil dihapus');
        }
        else Session::flash('danger','Data pemetaan peran gagal dihapus');
        
        return redirect()->route('psc.peran.index');
    }
}
=======
<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Penempatan\Jabatan;
use App\Models\Penempatan\KategoriJabatan;
use App\Models\Psc\PaRoleMapping;
use App\Models\Psc\PscRoleMapping;
use App\Models\Unit;

class PemetaanPeranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mapping = Jabatan::select('id','name','category_id')->has('pscRoleMapping')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

        $kategori = KategoriJabatan::select('id','name')->get();

        $jabatan = null;
        foreach($kategori as $k){
            $position = $k->jabatan()->select('id','code','name','category_id')->whereDoesntHave('pscRoleMapping')->aktif()->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

            if($position && count($position) > 0){
                if(!$jabatan){
                    $jabatan = collect($position);
                }
                else{
                    $jabatan = $jabatan->concat(collect($position));
                }
            }
        }

        $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

        $struktural = Jabatan::select('id','name')->where(function($q){
            $q->where('placement_id',1)->orWhereNull('placement_id');
        })->aktif()->get();

        return view('kepegawaian.pa.psc.pemetaan_peran_index', compact('mapping','jabatan','penempatan','struktural'));
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
            'position.required' => 'Mohon pilih salah satu jabatan',
            'create.required' => 'Mohon pilih salah satu jabatan yang dapat membuat aspek',
            'view.required' => 'Mohon pilih salah satu jabatan yang dapat melihat nilai',
        ];

        $this->validate($request, [
            'position' => 'required',
            'create' => 'required',
            'view' => 'required'
        ], $messages);

        $pscMapping = PscRoleMapping::where('target_position_id', $request->position);
        
        if($pscMapping->count() < 1){
            $target = Jabatan::find($request->position);
            if($target){
                $role = PaRoleMapping::select('id','name')->psc()->get();
                foreach($role as $r){
                    $requestItem = $request->{$r->name};

                    if(isset($requestItem)){
                        foreach($requestItem as $i){
                            if(isset($i)){
                                $thisMapping = new PscRoleMapping();
                                $thisMapping->target_position_id = $request->position;
                                $thisMapping->pa_role_mapping_id = $r->id;
                                $thisMapping->position_id = $i;
                                $thisMapping->save();
                            }
                        }
                    }
                }
            }

            Session::flash('success','Data pemetaan peran '.$target->name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data pemetaan peran sudah pernah ditambahkan');
        
        return redirect()->route('psc.peran.index');
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
        $jabatan = $request->id ? Jabatan::find($request->id) : null;

        $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

        $struktural = Jabatan::select('id','name')->where(function($q){
            $q->where('placement_id',1)->orWhereNull('placement_id');
        })->aktif()->get();

        return view('kepegawaian.pa.psc.pemetaan_peran_ubah', compact('jabatan','penempatan','struktural'));
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
            'editCreate.required' => 'Mohon pilih salah satu jabatan yang dapat membuat aspek',
            'editView.required' => 'Mohon pilih salah satu jabatan yang dapat melihat nilai',
        ];

        $this->validate($request, [
            'editCreate' => 'required',
            'editView' => 'required'
        ], $messages);

        $target = Jabatan::find($request->id);

        if($target){
            $role = PaRoleMapping::select('id','name')->psc()->get();
            
            foreach($role as $r){
                $requestName = 'edit'.ucwords($r->name);
                $requestItem = $request->{$requestName};

                if(isset($requestItem)){
                    $thisMapping = $target->pscRoleMapping()->where('pa_role_mapping_id',$r->id)->get();
                    $countMapping = count($thisMapping);

                    $max = count($requestItem) > $countMapping ? count($requestItem) : $countMapping;

                    for($i=0;$i<$max;$i++){
                        if(isset($thisMapping[$i])){
                            $mapping = $thisMapping[$i];
                            if(isset($requestItem[$i])){
                                $mapping->position_id = $requestItem[$i];
                                $mapping->save();
                            }
                            else $mapping->delete();
                        }
                        elseif(isset($requestItem[$i])){
                            $mapping = new PscRoleMapping();
                            $mapping->target_position_id = $request->id;
                            $mapping->pa_role_mapping_id = $r->id;
                            $mapping->position_id = $requestItem[$i];
                            $mapping->save();
                        }
                    }
                }
            }

            Session::flash('success','Data pemetaan peran '.$target->name.' berhasil diubah');
        }

        else Session::flash('danger','Data pemetaan peran gagal disimpan');
        
        return redirect()->route('psc.peran.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jabatan = Jabatan::find($id);

        if($jabatan){
            $jabatan->pscRoleMapping()->delete();

            Session::flash('success','Data pemetaan peran '.$jabatan->name.' berhasil dihapus');
        }
        else Session::flash('danger','Data pemetaan peran gagal dihapus');
        
        return redirect()->route('psc.peran.index');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

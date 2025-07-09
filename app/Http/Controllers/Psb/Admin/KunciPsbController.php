<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Setting;
use App\Models\Unit;

class KunciPsbController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'psb.admin.kunci-psb';
        $this->active = 'Kunci PSB';
        $this->route = 'kependidikan.psb.kunci';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        // Check targets
        if(in_array($request->user()->role->name,['ketuayys','pembinayys','direktur','ctl'])){
            $lock = Setting::where('name','psb_lock_status')->first();

            $unit = Unit::select('id','name','psb_active','new_admission_active','transfer_admission_active')->sekolah()->get();

            $active = $this->active;
            $route = $this->route;

            $editable = false;
            if(in_array($role,['ctl'])) $editable = true;

            return view($this->template.'-index', compact('lock','active','route','unit','editable'));
        }

        else return redirect()->route('kependidikan.index');
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
        if(in_array($request->user()->role->name,['ctl'])){
            $lock = Setting::where('name','psb_lock_status')->first();

            if(!$lock){
                $lock = new Setting();
                $lock->name = 'psb_lock_status';
                $lock->value = 0;
                $lock->save();

                $lock->fresh();
            }

            $lock->value = $request->lock == 'on' ? 0 : 1;
            $lock->save();

            $unit = Unit::select('id','name','psb_active','new_admission_active','transfer_admission_active')->sekolah()->get();

            $type = ['new','transfer'];
            
            foreach($unit as $u){
                $counter = 0;
                foreach($type as $t){
                    $inputValue = 'lock-'.strtolower($u->name).'-'.$t;
                    $attr = ($t == 'new' ? 'new' : 'transfer').'_admission_active';
                    if($request->{$inputValue} == 'on'){
                        $u->{$attr} = 1;
                        $counter++;
                    }
                    else $u->{$attr} = 0;
                }
                $u->psb_active = $counter >= 1 ? 1 : 0;
                $u->save();
            }

            Session::flash('success','Kunci PSB berhasil diperbarui');

            return redirect()->route($this->route.'.index');
        }

        else return redirect()->route('kependidikan.index');
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

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Setting;
use App\Models\Unit;

class KunciPsbController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'psb.admin.kunci-psb';
        $this->active = 'Kunci PSB';
        $this->route = 'kependidikan.psb.kunci';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        // Check targets
        if(in_array($request->user()->role->name,['ketuayys','pembinayys','direktur','ctl'])){
            $lock = Setting::where('name','psb_lock_status')->first();

            $unit = Unit::select('id','name','psb_active','new_admission_active','transfer_admission_active')->sekolah()->get();

            $active = $this->active;
            $route = $this->route;

            $editable = false;
            if(in_array($role,['ctl'])) $editable = true;

            return view($this->template.'-index', compact('lock','active','route','unit','editable'));
        }

        else return redirect()->route('kependidikan.index');
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
        if(in_array($request->user()->role->name,['ctl'])){
            $lock = Setting::where('name','psb_lock_status')->first();

            if(!$lock){
                $lock = new Setting();
                $lock->name = 'psb_lock_status';
                $lock->value = 0;
                $lock->save();

                $lock->fresh();
            }

            $lock->value = $request->lock == 'on' ? 0 : 1;
            $lock->save();

            $unit = Unit::select('id','name','psb_active','new_admission_active','transfer_admission_active')->sekolah()->get();

            $type = ['new','transfer'];
            
            foreach($unit as $u){
                $counter = 0;
                foreach($type as $t){
                    $inputValue = 'lock-'.strtolower($u->name).'-'.$t;
                    $attr = ($t == 'new' ? 'new' : 'transfer').'_admission_active';
                    if($request->{$inputValue} == 'on'){
                        $u->{$attr} = 1;
                        $counter++;
                    }
                    else $u->{$attr} = 0;
                }
                $u->psb_active = $counter >= 1 ? 1 : 0;
                $u->save();
            }

            Session::flash('success','Kunci PSB berhasil diperbarui');

            return redirect()->route($this->route.'.index');
        }

        else return redirect()->route('kependidikan.index');
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

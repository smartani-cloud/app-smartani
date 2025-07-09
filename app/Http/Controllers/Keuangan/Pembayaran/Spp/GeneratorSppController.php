<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Http\Services\Generator\SppGenerator;

class GeneratorSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'SPP Generator';
        $this->route = 'spp.generator';
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
        if(in_array($request->user()->role->name,['faspv','akunspv'])){
            $active = $this->active;
            $route = $this->route;

            return view($this->template.$route.'-index', compact('active','route'));
        }

        else return redirect()->route('keuangan.index');
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // Check targets
        if(in_array($request->user()->role->name,['faspv','akunspv'])){
            SppGenerator::resetSppDeduction();
            SppGenerator::generateDeduction();
            SppGenerator::generateFromTransaction();

            Session::flash('success','SPP berhasil dibangkitkan ulang');

            return redirect()->route($this->route.'.index');
        }

        else return redirect()->route('keuangan.index');
    }    
}
=======
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Http\Services\Generator\SppGenerator;

class GeneratorSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'SPP Generator';
        $this->route = 'spp.generator';
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
        if(in_array($request->user()->role->name,['faspv','akunspv'])){
            $active = $this->active;
            $route = $this->route;

            return view($this->template.$route.'-index', compact('active','route'));
        }

        else return redirect()->route('keuangan.index');
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // Check targets
        if(in_array($request->user()->role->name,['faspv','akunspv'])){
            SppGenerator::resetSppDeduction();
            SppGenerator::generateDeduction();
            SppGenerator::generateFromTransaction();

            Session::flash('success','SPP berhasil dibangkitkan ulang');

            return redirect()->route($this->route.'.index');
        }

        else return redirect()->route('keuangan.index');
    }    
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

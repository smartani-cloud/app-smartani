<<<<<<< HEAD
<?php

namespace Modules\HR\Http\Controllers\EmployeeManagement\References;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Rekrutmen\KategoriPegawai;
use App\Models\Rekrutmen\StatusPegawai;

class PartnerTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'kepegawaian.rekrutmen.';
        $this->active = 'Jenis Mitra';
        $this->route = 'jenis-mitra';
        $this->parent = 'Mitra';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = StatusPegawai::mitra()->pegawaiAktif()->orderBy('status')->get();
        $used = null;
        foreach($data as $d){
            if($d->pegawai()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'_index', compact('data','used','active','route'));
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
            'name.required' => 'Mohon tuliskan '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $checkCount = StatusPegawai::where('status', $this->parent.' '.$request->name)->mitra()->pegawaiAktif()->count();
        $allStatus = StatusPegawai::mitra()->pegawaiAktif();

        if($checkCount < 1 && $allStatus->count() < 100){
            $item = new StatusPegawai();
            // Code Generator
            $statusMitra = StatusPegawai::select('code')->where('status',$this->parent)->first();
            if(!$statusMitra) return redirect()->route($this->route.'.index');
            $lastStatus = $allStatus->select('code')->orderBy('code','DESC')->first();
            $lastCode = $lastStatus ? explode('.',$lastStatus->code)[1] : 0;
            $item->code = $statusMitra->code.'.'.sprintf('%02d',$lastCode+1);
            $item->status = $this->parent.' '.$request->name;
            $item->show_name = $this->parent.' '.$request->name;
            $item->desc = $this->parent.' '.$request->name.' SIT Auliya';
            $kategori = KategoriPegawai::select('id')->where('name','Mitra')->first();
            $item->category_id = $kategori->id;
            $item->save();

            Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
        }

        elseif($allStatus->count() == 99) Session::flash('danger','Tidak dapat menambahkan data lagi');

        else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

        return redirect()->route($this->route.'.index');
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
        $data = $request->id ? StatusPegawai::where('id',$request->id)->mitra()->pegawaiAktif()->first() : null;
        $active = $this->active;
        $route = $this->route;

        if($data) return view($this->template.$route.'_ubah', compact('data','active','route'));
        else return "Ups, tidak dapat memuat data";
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
            'name.required' => 'Mohon tuliskan '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $item = StatusPegawai::where('id',$request->id)->mitra()->pegawaiAktif()->first();
        $checkCount = StatusPegawai::where('status', $this->parent.' '.$request->name)->where('id','!=',$request->id)->mitra()->pegawaiAktif()->count();
        $allStatus = StatusPegawai::mitra()->pegawaiAktif();

        if($item && $checkCount < 1){
            $old = substr(strstr($item->status," "), 1);
            $item->status = $this->parent.' '.$request->name;
            $item->show_name = $this->parent.' '.$request->name;
            $item->desc = $this->parent.' '.$request->name.' SIT Auliya';
            $kategori = KategoriPegawai::select('id')->where('name','Mitra')->first();
            $item->category_id = $kategori->id;

            $item->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->name);
        }

        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = StatusPegawai::where('id',$id)->mitra()->pegawaiAktif()->first();
        $used_count = $item->pegawai()->count();
        if($item && $used_count < 1){
            $name = substr(strstr($item->status," "), 1);
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
=======
<?php

namespace Modules\HR\Http\Controllers\EmployeeManagement\References;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Rekrutmen\KategoriPegawai;
use App\Models\Rekrutmen\StatusPegawai;

class PartnerTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'kepegawaian.rekrutmen.';
        $this->active = 'Jenis Mitra';
        $this->route = 'jenis-mitra';
        $this->parent = 'Mitra';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = StatusPegawai::mitra()->pegawaiAktif()->orderBy('status')->get();
        $used = null;
        foreach($data as $d){
            if($d->pegawai()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'_index', compact('data','used','active','route'));
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
            'name.required' => 'Mohon tuliskan '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $checkCount = StatusPegawai::where('status', $this->parent.' '.$request->name)->mitra()->pegawaiAktif()->count();
        $allStatus = StatusPegawai::mitra()->pegawaiAktif();

        if($checkCount < 1 && $allStatus->count() < 100){
            $item = new StatusPegawai();
            // Code Generator
            $statusMitra = StatusPegawai::select('code')->where('status',$this->parent)->first();
            if(!$statusMitra) return redirect()->route($this->route.'.index');
            $lastStatus = $allStatus->select('code')->orderBy('code','DESC')->first();
            $lastCode = $lastStatus ? explode('.',$lastStatus->code)[1] : 0;
            $item->code = $statusMitra->code.'.'.sprintf('%02d',$lastCode+1);
            $item->status = $this->parent.' '.$request->name;
            $item->show_name = $this->parent.' '.$request->name;
            $item->desc = $this->parent.' '.$request->name.' SIT Auliya';
            $kategori = KategoriPegawai::select('id')->where('name','Mitra')->first();
            $item->category_id = $kategori->id;
            $item->save();

            Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
        }

        elseif($allStatus->count() == 99) Session::flash('danger','Tidak dapat menambahkan data lagi');

        else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

        return redirect()->route($this->route.'.index');
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
        $data = $request->id ? StatusPegawai::where('id',$request->id)->mitra()->pegawaiAktif()->first() : null;
        $active = $this->active;
        $route = $this->route;

        if($data) return view($this->template.$route.'_ubah', compact('data','active','route'));
        else return "Ups, tidak dapat memuat data";
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
            'name.required' => 'Mohon tuliskan '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $item = StatusPegawai::where('id',$request->id)->mitra()->pegawaiAktif()->first();
        $checkCount = StatusPegawai::where('status', $this->parent.' '.$request->name)->where('id','!=',$request->id)->mitra()->pegawaiAktif()->count();
        $allStatus = StatusPegawai::mitra()->pegawaiAktif();

        if($item && $checkCount < 1){
            $old = substr(strstr($item->status," "), 1);
            $item->status = $this->parent.' '.$request->name;
            $item->show_name = $this->parent.' '.$request->name;
            $item->desc = $this->parent.' '.$request->name.' SIT Auliya';
            $kategori = KategoriPegawai::select('id')->where('name','Mitra')->first();
            $item->category_id = $kategori->id;

            $item->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->name);
        }

        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = StatusPegawai::where('id',$id)->mitra()->pegawaiAktif()->first();
        $used_count = $item->pegawai()->count();
        if($item && $used_count < 1){
            $name = substr(strstr($item->status," "), 1);
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

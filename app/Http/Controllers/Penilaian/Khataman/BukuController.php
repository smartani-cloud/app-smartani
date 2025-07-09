<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Khataman;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\Kurdeka\Buku;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'buku';
        $this->modul = $modul;
        $this->active = 'Buku';
        $this->route = $this->subsystem.'.penilaian.khataman.'.$this->modul;
        $this->creatable = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $data = $units = $used = null;

        $isCreatable = $this->creatable;

        $unitList = Unit::select('id','name')->sekolah();
        if(in_array($role,['kepsek','wakasek','guru'])){
            $myUnit = auth()->user()->pegawai->unit->name;
            if($unit && $unit != $myUnit){
                $unit = null;
            }
            else $unit = $myUnit;
            $unitList = $unitList->where('name',$myUnit);
        }
        $unitList = $unitList->get();

        if($unit){
            $unit = Unit::sekolah()->where('name',$unit)->first();
            
            if($unit){
                // Inti Function
                $data = Buku::select('id','title','total_pages')->orderBy('title');
                if($role == 'guru'){
                    $data = $data->where(function($q)use($isCreatable,$unit){
                        $q->whereHas('units.unit',function($q)use($unit){
                            $q->where('id',$unit->id);
                        })->when($isCreatable,function($q){
                            return $q->orDoesntHave('units');
                        });
                    });
                }
                $data = $data->get();

                $used = null;
                foreach($data as $d){
                    $relatedUnit = Unit::select('name')->whereHas('books.buku',function($q)use($d){
                        $q->where('id',$d->id);
                    })->get();
                    $units[$d->id] = $relatedUnit && count($relatedUnit) > 0 ? implode(', ',$relatedUnit->pluck('name')->toArray()) : null;
                    if($d->units()->count() > 0 || $d->khatam()->count() > 0) $used[$d->id] = 1;
                    else $used[$d->id] = 0;
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek','guru'])){
                    $unit = auth()->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if(in_array($role,['kepsek','wakasek','guru'])){
                $unit = auth()->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','isCreatable','data','units','used'));
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
    public function store(Request $request, $unit)
    {
        $role = auth()->user()->role->name;
    
        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            if($this->creatable){
                // Inti function
                $messages = [
                    'title.required' => 'Mohon tuliskan judul buku',
                    'title.max' => 'Panjang judul buku maksimal 150 karakter'
                ];

                $this->validate($request, [
                    'title' => 'required|max:150'
                ], $messages);

                $count = Buku::where([
                    'title' => $request->title
                ]);
                
                if($count->count() < 1){
                    $book = new Buku();
                    $book->title = $request->title;
                    $book->total_pages = $request->pages;
                    $book->save();

                    Session::flash('success','Data buku '.$request->title.' berhasil ditambahkan');
                }
                else Session::flash('danger','Data buku '.$request->title.' sudah pernah ditambahkan');
            }
            else{
                Session::flash('danger', 'Tidak dapat menambahkan data');
            }
            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
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
    public function edit(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        $unitList = Unit::select('id','name')->sekolah();
        if(in_array($role,['kepsek','wakasek','guru'])){
            $unitList = $unitList->where('name',auth()->user()->pegawai->unit->name);
        }
        $unitList = $unitList->get();

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $data = $request->id ? Buku::where([
                'id' => $request->id
            ])->first() : null;
            $active = $this->active;
            $route = $this->route;

            return view($route.'-edit', compact('active','route','unitList','unit','data'));
        }
        else return 'Ups, unit tidak ditemukan';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $messages = [
                'editUnit.required' => 'Mohon pilih salah satu unit',
                'editTitle.required' => 'Mohon tuliskan judul buku',
                'editTitle.max' => 'Panjang judul buku maksimal 150 karakter'
            ];

            $this->validate($request, [
                'editTitle' => 'required|max:150'
            ], $messages);

            $item = Buku::where([
                'id' => $request->id
            ])->first();

            $count = Buku::where([
                'title' => $request->editName
            ])->where('id','!=',$request->id)->count();

            if($item && $count < 1){
                $old = $item->title;
                $item->title = $request->editTitle;
                $item->total_pages = $request->editPages;
                $item->save();
                
                Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->title);
            }

            else Session::flash('danger','Perubahan data gagal disimpan');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $unit, $id)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $item = Buku::where([
                'id' => $request->id
            ])->first();

            $used_count = $item ? $item->units()->count() : 0;
            if($item && $item->units()->count() < 1 && $item->khatam()->count() < 1){
                $name = $item->name;
                $item->delete();

                Session::flash('success','Data '.$name.' berhasil dihapus');
            }
            else Session::flash('danger','Data gagal dihapus');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Khataman;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\Kurdeka\Buku;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'buku';
        $this->modul = $modul;
        $this->active = 'Buku';
        $this->route = $this->subsystem.'.penilaian.khataman.'.$this->modul;
        $this->creatable = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $data = $units = $used = null;

        $isCreatable = $this->creatable;

        $unitList = Unit::select('id','name')->sekolah();
        if(in_array($role,['kepsek','wakasek','guru'])){
            $myUnit = auth()->user()->pegawai->unit->name;
            if($unit && $unit != $myUnit){
                $unit = null;
            }
            else $unit = $myUnit;
            $unitList = $unitList->where('name',$myUnit);
        }
        $unitList = $unitList->get();

        if($unit){
            $unit = Unit::sekolah()->where('name',$unit)->first();
            
            if($unit){
                // Inti Function
                $data = Buku::select('id','title','total_pages')->orderBy('title');
                if($role == 'guru'){
                    $data = $data->where(function($q)use($isCreatable,$unit){
                        $q->whereHas('units.unit',function($q)use($unit){
                            $q->where('id',$unit->id);
                        })->when($isCreatable,function($q){
                            return $q->orDoesntHave('units');
                        });
                    });
                }
                $data = $data->get();

                $used = null;
                foreach($data as $d){
                    $relatedUnit = Unit::select('name')->whereHas('books.buku',function($q)use($d){
                        $q->where('id',$d->id);
                    })->get();
                    $units[$d->id] = $relatedUnit && count($relatedUnit) > 0 ? implode(', ',$relatedUnit->pluck('name')->toArray()) : null;
                    if($d->units()->count() > 0 || $d->khatam()->count() > 0) $used[$d->id] = 1;
                    else $used[$d->id] = 0;
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek','guru'])){
                    $unit = auth()->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if(in_array($role,['kepsek','wakasek','guru'])){
                $unit = auth()->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','isCreatable','data','units','used'));
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
    public function store(Request $request, $unit)
    {
        $role = auth()->user()->role->name;
    
        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            if($this->creatable){
                // Inti function
                $messages = [
                    'title.required' => 'Mohon tuliskan judul buku',
                    'title.max' => 'Panjang judul buku maksimal 150 karakter'
                ];

                $this->validate($request, [
                    'title' => 'required|max:150'
                ], $messages);

                $count = Buku::where([
                    'title' => $request->title
                ]);
                
                if($count->count() < 1){
                    $book = new Buku();
                    $book->title = $request->title;
                    $book->total_pages = $request->pages;
                    $book->save();

                    Session::flash('success','Data buku '.$request->title.' berhasil ditambahkan');
                }
                else Session::flash('danger','Data buku '.$request->title.' sudah pernah ditambahkan');
            }
            else{
                Session::flash('danger', 'Tidak dapat menambahkan data');
            }
            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
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
    public function edit(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        $unitList = Unit::select('id','name')->sekolah();
        if(in_array($role,['kepsek','wakasek','guru'])){
            $unitList = $unitList->where('name',auth()->user()->pegawai->unit->name);
        }
        $unitList = $unitList->get();

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $data = $request->id ? Buku::where([
                'id' => $request->id
            ])->first() : null;
            $active = $this->active;
            $route = $this->route;

            return view($route.'-edit', compact('active','route','unitList','unit','data'));
        }
        else return 'Ups, unit tidak ditemukan';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $messages = [
                'editUnit.required' => 'Mohon pilih salah satu unit',
                'editTitle.required' => 'Mohon tuliskan judul buku',
                'editTitle.max' => 'Panjang judul buku maksimal 150 karakter'
            ];

            $this->validate($request, [
                'editTitle' => 'required|max:150'
            ], $messages);

            $item = Buku::where([
                'id' => $request->id
            ])->first();

            $count = Buku::where([
                'title' => $request->editName
            ])->where('id','!=',$request->id)->count();

            if($item && $count < 1){
                $old = $item->title;
                $item->title = $request->editTitle;
                $item->total_pages = $request->editPages;
                $item->save();
                
                Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->title);
            }

            else Session::flash('danger','Perubahan data gagal disimpan');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $unit, $id)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $item = Buku::where([
                'id' => $request->id
            ])->first();

            $used_count = $item ? $item->units()->count() : 0;
            if($item && $item->units()->count() < 1 && $item->khatam()->count() < 1){
                $name = $item->name;
                $item->delete();

                Session::flash('success','Data '.$name.' berhasil dihapus');
            }
            else Session::flash('danger','Data gagal dihapus');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

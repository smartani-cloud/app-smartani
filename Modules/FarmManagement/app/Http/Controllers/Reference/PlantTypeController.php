<?php

namespace Modules\FarmManagement\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use Modules\FarmManagement\Models\PlantCategory;
use Modules\FarmManagement\Models\PlantType;

class PlantTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = 'farmmanagement';
        $this->template = 'reference.';
        $this->active = 'Jenis Tanaman';
        $this->route = 'plant-type';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PlantType::all();
        $categories = PlantCategory::all();

        $used = null;
        foreach($data as $d){
            if($d->plants()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-index', compact('data','used','module','active','route','categories'));
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
            'name.required' => 'Mohon tuliskan nama '.strtolower($this->active),
            'category.required' => 'Mohon pilih salah satu kategori tanaman'
        ];

        $request->validate([
            'name' => 'required',
            'category' => 'required'
        ], $messages);

        $count = PlantType::where(['name' => $request->name,'category_id' => $request->category])->count();

        if($count < 1){
            $category = PlantCategory::where('id',$request->category)->first();
            if($category){
                $item = new PlantType();
                $item->category_id = $request->category;
                $item->name = $request->name;
                $item->save();

                Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
            }

            else Session::flash('danger','Mohon pilih salah satu kategori tanaman');
        }

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
        $data = $request->id ? PlantType::find($request->id) : null;

        if($data){
            $categories = PlantCategory::all();

            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-edit', compact('data','module','active','route','categories'));
        }
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
            'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active),
            'editCategory.required' => 'Mohon pilih salah satu kategori tanaman'
        ];

        $request->validate([
            'editName' => 'required',
            'editCategory' => 'required'
        ], $messages);

        $item = PlantType::find($request->id);
        $tips = PlantType::where(['name' => $request->editName,'category_id' => $request->editCategory])->where('id','!=',$request->id);

        if($item && $tips->count() < 1){
            $category = PlantCategory::where('id',$request->editCategory)->first();
            if($category){
                $name = $item->name;
                $item->name = $request->editName;
                $item->category_id = $request->editCategory;
                $item->save();
                
                Session::flash('success','Data '.$name.' berhasil diubah');
            }

            else Session::flash('danger','Mohon pilih salah satu kategori tanaman');
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
        $item = PlantType::find($id);
        $used_count = $item ? $item->plants()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->delete();

            Session::flash('success','Data '.strtolower($this->active).' '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}

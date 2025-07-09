<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Psc\PscIndicator;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscRoleMapping;
use App\Models\Unit;

class AspekUtamaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $isTopManagements = in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']);


        // Check targets
        if($isTopManagements || (!$isTopManagements && in_array($request->user()->role->name,['etl','etm']))){
            $tahun = TahunAjaran::where('is_active',1)->latest()->first();

            $indicators = $this->getIndicators();

            $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

            if($isTopManagements)
                $folder = 'read-only';
            else
                $folder = 'pa';

            return view('kepegawaian.'.$folder.'.psc.aspek_utama_index', compact('tahun','indicators','penempatan'));
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
        $messages = [
            'parent.required' => 'Mohon pilih salah satu IKU induk',
            'name.required' => 'Mohon tuliskan indikator kinerja utama',
            'percentage.required' => 'Mohon isi bobot nilai indikator',
            'grader.required' => 'Mohon pilih setidaknya satu jabatan yang dapat menilai',
        ];

        $this->validate($request, [
            'parent' => 'required',
            'name' => 'required',
            'percentage' => 'required',
            'grader' => 'required'
        ], $messages);

        // Check ETM & ETL
        if(in_array($request->user()->role->name,['etl','etm'])){
            $parents = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip(1)->take(1)->get();
            if(in_array($request->parent, $parents->pluck('id')->toArray())){
                $parent = (object) $parents->where('id',$request->parent)->first();

                $indikator = new PscIndicator();
                $indikator->parent_id = $parent->childs()->select('id')->first()->id;
                $indikator->name = $request->name;
                $indikator->level = 3;
                $indikator->percentage = $request->percentage;
                $indikator->is_fillable = 1;
                $indikator->is_static = 1;
                $indikator->employee_id = $request->user()->pegawai->id;
                $indikator->position_id = $request->user()->pegawai->jabatan->id;
                $indikator->save();

                $indikator->fresh();

                if(isset($request->grader)){
                    $indikator->penilai()->sync($request->grader);
                }
            }

            Session::flash('success','Data indikator '.$request->name.' berhasil ditambahkan');
            
            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
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
        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            // Inti function
            $indikator = PscIndicator::static()->where(['id' => $request->id])->first();

            if($indikator){
                // $skip = $limit = 1;
                // $parentIndicators = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();

                $penempatan = $usedCount = null;

                if($indikator->is_fillable == 1 && $indikator->childs()->static()->count() < 1){
                    $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code)','ASC')->get();

                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();
                }

                return view('kepegawaian.pa.psc.aspek_utama_ubah', compact('indikator','penempatan','usedCount'));
            }
            else return "Data indikator tidak ditemukan";
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
            'editName.required' => 'Mohon tuliskan indikator kinerja utama'
        ];

        $this->validate($request, [
            'editName' => 'required'
        ], $messages);

        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            $indikator = PscIndicator::static()->where(['id' => $request->id])->first();

            if($indikator){
                // $skip = $limit = 1;
                // $parentIndicators = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();

                $oldName = $indikator->name;

                $indikator->name = $request->editName;
                $indikator->save();

                $indikator->fresh();

                if($indikator->is_fillable == 1 && $indikator->childs()->static()->count() < 1){
                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();

                    if(isset($request->editGrader) && $usedCount < 1){
                        $indikator->penilai()->sync($request->editGrader);
                    }
                }
                Session::flash('success','Data indikator '.$oldName.' berhasil diubah');
            }
            else Session::flash('danger','Data indikator tidak ditemukan');

            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Update all related resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        if(in_array($request->user()->role->name,['etl','etm'])){
            // Inti function
            $indikator = $this->getIndicators();

            if($indikator){
                $successCount = 0;
                foreach($indikator as $i){
                    $inputName = 'value-'.$i->id;
                    $requestValue = $request->{$inputName};
                    if($requestValue && ($requestValue >= 0 && $requestValue <= 100) && ($requestValue != $i->percentage)){
                        $i->percentage = $requestValue;
                        $i->save();

                        $successCount++;
                    }
                }

                if($successCount > 0) Session::flash('success', $successCount.' data indikator berhasil diperbarui');
            }
            else Session::flash('success','Data indikator gagal diperbarui');
            
            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            $indikator = PscIndicator::static()->where(['id' => $id])->first();

            if($indikator){
                $oldName = $indikator->name;

                if($indikator->is_fillable == 1 && $indikator->childs()->static()->count() < 1){
                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();

                    if($usedCount < 1){
                        if($indikator->nilaiDetail()->count() > 0){
                            $indikator->delete();
                        }
                        else{
                            $indikator->penilai()->detach();
                            $indikator->forceDelete();
                        }
                        Session::flash('success','Data indikator '.$oldName.' berhasil dihapus');
                    }
                    else Session::flash('danger','Data indikator tidak dapat dihapus karena masih ada proses penilaian aktif');
                }
                else Session::flash('danger','Data indikator tidak dapat dihapus');
            }
            else Session::flash('danger','Data indikator tidak ditemukan');

            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
    }

    public static function getIndicators($parent = null){
        $indicators = null;

        $staticIndicators = PscIndicator::static();

        if($parent){
            $staticIndicators = $staticIndicators->where([
                'parent_id' => $parent
            ]);
        }
        else{
            $staticIndicators = $staticIndicators->whereNull('parent_id');
        }

        $staticIndicators = $staticIndicators->get();

        $allIndikators = null;
        if($staticIndicators && count($staticIndicators) > 0){
            $allIndikators = $staticIndicators;
        }

        if($allIndikators && count($allIndikators) > 0){
            foreach($allIndikators as $i){
                if(!$indicators){
                    $indicators = collect();
                }
                $indicators = $indicators->push($i);

                if($i->childs()->count() > 0){
                    $childs = self::getIndicators($i->id);
                    if($childs && count($childs) > 0){
                        $indicators = $indicators->concat($childs);
                    }
                }
            }
        }

        return $indicators;
    }
}
=======
<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Psc\PscIndicator;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscRoleMapping;
use App\Models\Unit;

class AspekUtamaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $isTopManagements = in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']);


        // Check targets
        if($isTopManagements || (!$isTopManagements && in_array($request->user()->role->name,['etl','etm']))){
            $tahun = TahunAjaran::where('is_active',1)->latest()->first();

            $indicators = $this->getIndicators();

            $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

            if($isTopManagements)
                $folder = 'read-only';
            else
                $folder = 'pa';

            return view('kepegawaian.'.$folder.'.psc.aspek_utama_index', compact('tahun','indicators','penempatan'));
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
        $messages = [
            'parent.required' => 'Mohon pilih salah satu IKU induk',
            'name.required' => 'Mohon tuliskan indikator kinerja utama',
            'percentage.required' => 'Mohon isi bobot nilai indikator',
            'grader.required' => 'Mohon pilih setidaknya satu jabatan yang dapat menilai',
        ];

        $this->validate($request, [
            'parent' => 'required',
            'name' => 'required',
            'percentage' => 'required',
            'grader' => 'required'
        ], $messages);

        // Check ETM & ETL
        if(in_array($request->user()->role->name,['etl','etm'])){
            $parents = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip(1)->take(1)->get();
            if(in_array($request->parent, $parents->pluck('id')->toArray())){
                $parent = (object) $parents->where('id',$request->parent)->first();

                $indikator = new PscIndicator();
                $indikator->parent_id = $parent->childs()->select('id')->first()->id;
                $indikator->name = $request->name;
                $indikator->level = 3;
                $indikator->percentage = $request->percentage;
                $indikator->is_fillable = 1;
                $indikator->is_static = 1;
                $indikator->employee_id = $request->user()->pegawai->id;
                $indikator->position_id = $request->user()->pegawai->jabatan->id;
                $indikator->save();

                $indikator->fresh();

                if(isset($request->grader)){
                    $indikator->penilai()->sync($request->grader);
                }
            }

            Session::flash('success','Data indikator '.$request->name.' berhasil ditambahkan');
            
            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
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
        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            // Inti function
            $indikator = PscIndicator::static()->where(['id' => $request->id])->first();

            if($indikator){
                // $skip = $limit = 1;
                // $parentIndicators = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();

                $penempatan = $usedCount = null;

                if($indikator->is_fillable == 1 && $indikator->childs()->static()->count() < 1){
                    $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code)','ASC')->get();

                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();
                }

                return view('kepegawaian.pa.psc.aspek_utama_ubah', compact('indikator','penempatan','usedCount'));
            }
            else return "Data indikator tidak ditemukan";
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
            'editName.required' => 'Mohon tuliskan indikator kinerja utama'
        ];

        $this->validate($request, [
            'editName' => 'required'
        ], $messages);

        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            $indikator = PscIndicator::static()->where(['id' => $request->id])->first();

            if($indikator){
                // $skip = $limit = 1;
                // $parentIndicators = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();

                $oldName = $indikator->name;

                $indikator->name = $request->editName;
                $indikator->save();

                $indikator->fresh();

                if($indikator->is_fillable == 1 && $indikator->childs()->static()->count() < 1){
                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();

                    if(isset($request->editGrader) && $usedCount < 1){
                        $indikator->penilai()->sync($request->editGrader);
                    }
                }
                Session::flash('success','Data indikator '.$oldName.' berhasil diubah');
            }
            else Session::flash('danger','Data indikator tidak ditemukan');

            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Update all related resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        if(in_array($request->user()->role->name,['etl','etm'])){
            // Inti function
            $indikator = $this->getIndicators();

            if($indikator){
                $successCount = 0;
                foreach($indikator as $i){
                    $inputName = 'value-'.$i->id;
                    $requestValue = $request->{$inputName};
                    if($requestValue && ($requestValue >= 0 && $requestValue <= 100) && ($requestValue != $i->percentage)){
                        $i->percentage = $requestValue;
                        $i->save();

                        $successCount++;
                    }
                }

                if($successCount > 0) Session::flash('success', $successCount.' data indikator berhasil diperbarui');
            }
            else Session::flash('success','Data indikator gagal diperbarui');
            
            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Check targets
        if(in_array($request->user()->role->name,['etl','etm'])){
            $indikator = PscIndicator::static()->where(['id' => $id])->first();

            if($indikator){
                $oldName = $indikator->name;

                if($indikator->is_fillable == 1 && $indikator->childs()->static()->count() < 1){
                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();

                    if($usedCount < 1){
                        if($indikator->nilaiDetail()->count() > 0){
                            $indikator->delete();
                        }
                        else{
                            $indikator->penilai()->detach();
                            $indikator->forceDelete();
                        }
                        Session::flash('success','Data indikator '.$oldName.' berhasil dihapus');
                    }
                    else Session::flash('danger','Data indikator tidak dapat dihapus karena masih ada proses penilaian aktif');
                }
                else Session::flash('danger','Data indikator tidak dapat dihapus');
            }
            else Session::flash('danger','Data indikator tidak ditemukan');

            return redirect()->route('psc.utama.index');
        }

        else return redirect()->route('kepegawaian.index');
    }

    public static function getIndicators($parent = null){
        $indicators = null;

        $staticIndicators = PscIndicator::static();

        if($parent){
            $staticIndicators = $staticIndicators->where([
                'parent_id' => $parent
            ]);
        }
        else{
            $staticIndicators = $staticIndicators->whereNull('parent_id');
        }

        $staticIndicators = $staticIndicators->get();

        $allIndikators = null;
        if($staticIndicators && count($staticIndicators) > 0){
            $allIndikators = $staticIndicators;
        }

        if($allIndikators && count($allIndikators) > 0){
            foreach($allIndikators as $i){
                if(!$indicators){
                    $indicators = collect();
                }
                $indicators = $indicators->push($i);

                if($i->childs()->count() > 0){
                    $childs = self::getIndicators($i->id);
                    if($childs && count($childs) > 0){
                        $indicators = $indicators->concat($childs);
                    }
                }
            }
        }

        return $indicators;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

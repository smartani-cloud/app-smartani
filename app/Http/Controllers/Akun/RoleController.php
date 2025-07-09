<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Models\Unit;

class RoleController extends Controller
{
    /**
     * Change the unit of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeUnit(Request $request, $unit)
    {
        if($request->user()->pegawai->units()->count() > 0){
            $unit = Unit::where('name',$unit)->first();
            $unit = $unit ? $request->user()->pegawai->units()->where('unit_id',$unit->id)->first() : null;
            if($unit){
                $pegawai = $request->user()->pegawai;
                $pegawai->unit_id = $unit->unit->id;
                $pegawai->save();
            }
        }
        //return redirect()->back();
        return redirect()->route('sso');
    }

    /**
     * Change the role of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeRole(Request $request, $unit, $position)
    {
    	if($request->user()->pegawai->units()->count() > 0){
    		$unit = Unit::where('name',$unit)->first();
    		$unit = $unit ? $request->user()->pegawai->units()->where('unit_id',$unit->id)->first() : null;
    		if($unit){
    			$jabatan = $unit->jabatans()->where('position_id',Crypt::decryptString($position))->first();
    			if($jabatan){
    				$pegawai = $request->user()->pegawai;
    				$pegawai->unit_id = $unit->unit->id;
    				$pegawai->position_id = $jabatan->id;
    				$pegawai->save();

    				$pegawai->fresh();

    				$login = $pegawai->login;
    				$login->role_id = $jabatan->role->id;
    				$login->save();
    			}
    		}
    	}
        //return redirect()->back();
        return redirect()->route('sso');
    }
}
=======
<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Models\Unit;

class RoleController extends Controller
{
    /**
     * Change the unit of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeUnit(Request $request, $unit)
    {
        if($request->user()->pegawai->units()->count() > 0){
            $unit = Unit::where('name',$unit)->first();
            $unit = $unit ? $request->user()->pegawai->units()->where('unit_id',$unit->id)->first() : null;
            if($unit){
                $pegawai = $request->user()->pegawai;
                $pegawai->unit_id = $unit->unit->id;
                $pegawai->save();
            }
        }
        //return redirect()->back();
        return redirect()->route('sso');
    }

    /**
     * Change the role of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeRole(Request $request, $unit, $position)
    {
    	if($request->user()->pegawai->units()->count() > 0){
    		$unit = Unit::where('name',$unit)->first();
    		$unit = $unit ? $request->user()->pegawai->units()->where('unit_id',$unit->id)->first() : null;
    		if($unit){
    			$jabatan = $unit->jabatans()->where('position_id',Crypt::decryptString($position))->first();
    			if($jabatan){
    				$pegawai = $request->user()->pegawai;
    				$pegawai->unit_id = $unit->unit->id;
    				$pegawai->position_id = $jabatan->id;
    				$pegawai->save();

    				$pegawai->fresh();

    				$login = $pegawai->login;
    				$login->role_id = $jabatan->role->id;
    				$login->save();
    			}
    		}
    	}
        //return redirect()->back();
        return redirect()->route('sso');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

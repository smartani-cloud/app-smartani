<?php

namespace Modules\Core\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Core\Models\References\Region;

class RegionController extends Controller
{
    public function fetchCities(Request $request)
    {
        $data['cities'] = Region::whereRaw('LENGTH(code) = 5')->where('code','LIKE',$request->province.'.%')->orderBy('name', 'ASC')->get(["name", "code"]);
        return response()->json($data);
    }

    public function fetchSubdistricts(Request $request)
    {
        $data['subdistricts'] = Region::whereRaw('LENGTH(code) = 8')->where('code','LIKE',$request->city.'.%')->orderBy('name', 'ASC')->get(["name", "code"]);
        return response()->json($data);
    }

    public function fetchVillages(Request $request)
    {
        $data['villages'] = Region::whereRaw('LENGTH(code) = 13')->where('code','LIKE',$request->subdistrict.'.%')->orderBy('name', 'ASC')->get(["name", "code"]);
        return response()->json($data);
    }
}

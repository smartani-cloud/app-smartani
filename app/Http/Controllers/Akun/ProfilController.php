<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profile = $request->user()->profiles()->where('profilable_type',$request->user()->role->group->profilable_type)->first();

        return view('akun.profil_index', compact('profile'));
    }
}
=======
<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profile = $request->user()->profiles()->where('profilable_type',$request->user()->role->group->profilable_type)->first();

        return view('akun.profil_index', compact('profile'));
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

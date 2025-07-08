<?php

namespace App\Http\Controllers\Psb\Ortu;

use App\Additionals\getAnak;
use App\Http\Controllers\Controller;
use App\Models\LoginUser;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\IdentitasSiswa;
use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Siswa;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class OrtuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // dd(auth()->user()->username);
        $anaks = getAnak::listAnak(auth()->user()->user_id);

        return view('psb.ortu.index',compact('anaks'));
    }

    public function siswa($nickname)
    {
        //

        $anak = CalonSiswa::where('parent_id',auth()->user()->user_id)->where('student_nickname',$nickname)->first();

        return view('psb.ortu.siswa',compact('anak'));
    }

    public function siswaAktif($nickname)
    {
        //

        $anak = IdentitasSiswa::where('parent_id',auth()->user()->user_id)->where('student_nickname',$nickname)->first();
        $history_student = Siswa::where('student_id',$anak->id)->where('is_lulus',0)->first();
        // dd($anak->siswas[0]->virtualAccount, $history_student->virtualAccount);
        return view('psb.ortu.siswa-aktif',compact('anak'));
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
    public function show()
    {
        //
        return view('psb.ortu.profile.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
        return view('psb.ortu.profile.edit');
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
        // dd($request);

        $ortu = OrangTua::find(auth()->user()->user_id);


        $ortu->father_name = $request->father_name;
        $ortu->father_nik = $request->father_nik;
        $ortu->father_phone = $request->father_phone_number;
        $ortu->father_email = $request->father_email;
        $ortu->father_job = $request->father_job;
        $ortu->father_position = $request->father_position; 
        $ortu->father_phone_office = $request->father_phone_office;
        $ortu->father_job_address = $request->father_job_address; 
        $ortu->father_salary = $request->father_salary; 
        $ortu->mother_name = $request->mother_name;
        $ortu->mother_nik = $request->mother_nik;
        $ortu->mother_phone = $request->mother_phone_number;
        $ortu->mother_email = $request->mother_email;
        $ortu->mother_job = $request->mother_job;
        $ortu->mother_position = $request->mother_position; 
        $ortu->mother_phone_office = $request->mother_phone_office;
        $ortu->mother_job_address = $request->mother_job_address; 
        $ortu->mother_salary = $request->mother_salary; 
        $ortu->parent_address = $request->parent_address;
        $ortu->parent_phone_number = $request->parent_phone_number;
        $ortu->guardian_name = $request->guardian_name;
        $ortu->guardian_nik = $request->guardian_nik;
        $ortu->guardian_phone_number = $request->guardian_phone_number;
        $ortu->guardian_email = $request->guardian_email;
        $ortu->guardian_job = $request->guardian_job;
        $ortu->guardian_position = $request->guardian_position; 
        $ortu->guardian_phone_office = $request->guardian_phone_office;
        $ortu->guardian_job_address = $request->guardian_job_address; 
        $ortu->guardian_salary = $request->guardian_salary; 
        $ortu->guardian_address = $request->guardian_address;

        $ortu->save();

        // dd($ortu,$request);

        return redirect()->route('psb.profil')->with('success','Ubah profil berhasil');

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

    public function password()
    {
        return view('psb.ortu.profile.password');
    }

    public function changePassword(Request $request)
    {
        // dd(auth()->user()->password);
        if (Hash::check($request->old_password, auth()->user()->password)) { 
            $user = LoginUser::find(auth()->user()->id);
            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();
            return redirect()->back()->with('success', 'Password berhasil diubah');
        } else {
            return redirect()->back()->with('danger', 'Password lama tidak sesuai');
        }
    }

//     public function resetPassword(Request $request)
//     {
//         $token = Str::random(64);

//         $whatsAppText = rawurlencode("*[SISTA] Konfirmasi Reset Sandi*

// Assalamu'alaikum Ayah Bunda Ananda Hari.
// Baru-baru ini kami menerima permintaan untuk mereset sandi akun SISTA Anda. Jika Anda tidak melakukannya, silahkan abaikan pesan ini. Pesan ini akan kadaluarsa setelah 2 jam.

// Klik tautan di bawah untuk reset sandi:

// ".route('reset.password.get', $token));

//         return redirect('https://api.whatsapp.com/send?phone=085770711800&text='.$whatsAppText);
//     }

    public function encrypt()
    {
        $parents = OrangTua::all();
        foreach($parents as $parent){
            if(strlen($parent->father_phone) < 20){
                $parent->father_nik = encrypt($parent->father_nik);
                $parent->mother_nik = encrypt($parent->mother_nik);
                $parent->guardian_nik = encrypt($parent->guardian_nik);
                $parent->father_phone = encrypt($parent->father_phone);
                $parent->mother_phone = encrypt($parent->mother_phone);
                $parent->guardian_phone_number = encrypt($parent->guardian_phone_number);
                $parent->parent_address = encrypt($parent->parent_address);
                $parent->parent_phone_number = encrypt($parent->parent_phone_number);
                $parent->save();
            }
        }
        dd($parents);
    }
}

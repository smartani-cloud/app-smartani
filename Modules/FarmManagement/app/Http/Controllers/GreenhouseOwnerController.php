<?php

namespace Modules\FarmManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

use Modules\FarmManagement\Models\GreenhouseOwner;
use Modules\Core\Models\References\Region;
use Modules\Access\Models\Role;

use App\Models\JenisKelamin as Gender;
use App\Models\LoginUser;

class GreenhouseOwnerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = 'farmmanagement';
        $this->template = '';
        $this->active = 'Pemilik Greenhouse';
        $this->route = 'greenhouse-owner';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = GreenhouseOwner::all();

        $used = null;
        foreach($data as $d){
            if($d->units()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-index', compact('data','used','module','active','route'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $genders = Gender::all();
        $provinces = Region::provinces()->get();

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-create', compact('module','active','route','genders','provinces'));
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
            'name.required' => 'Mohon tuliskan nama lengkap dan gelar',
            'nickname.required' => 'Mohon tuliskan nama panggilan',
            'photo.file' => 'Pastikan foto adalah berkas yang valid',
            'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 1 MB',
            'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, .png, atau .webp',
            'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 100x200 px',
            'nik.required' => 'Mohon tuliskan NIK',
            'nik.numeric' => 'Pastikan NIK hanya mengandung angka',
            'nik.unique' => 'Pastikan NIK belum terdaftar',
            'npwp.numeric' => 'Pastikan NPWP hanya mengandung angka',
            'gender.required' => 'Mohon pilih salah satu jenis kelamin',
            'gender.exists' => 'Mohon pilih jenis kelamin yang valid',
            'birth_place.required' => 'Mohon tuliskan tempat lahir',
            'birthday_year.required' => 'Mohon masukkan tahun lahir',
            'birthday_year.numeric' => 'Pastikan tahun lahir hanya mengandung angka',
            'birthday_month.required' => 'Mohon masukkan bulan lahir',
            'birthday_month.numeric' => 'Pastikan bulan lahir hanya mengandung angka',
            'birthday_day.required' => 'Mohon masukkan tanggal lahir',
            'birthday_day.numeric' => 'Pastikan tanggal lahir hanya mengandung angka',
            'province.required' => 'Mohon pilih salah satu provinsi',
            'city.required' => 'Mohon pilih salah satu kabupaten/kota',
            'subdistrict.required' => 'Mohon pilih salah satu kecamatan',
            'village.required' => 'Mohon pilih salah satu desa/kelurahan',
            'village.exists' => 'Mohon pilih desa/kelurahan yang valid',
            'address.required' => 'Mohon tuliskan alamat',
            'rt.required' => 'Mohon masukkan RT',
            'rt.integer' => 'Pastikan RT hanya mengandung angka',
            'rt.between' => 'Pastikan RT antara 0 sampai 100',
            'rw.required' => 'Mohon masukkan RW',
            'rw.integer' => 'Pastikan RW hanya mengandung angka',
            'rw.between' => 'Pastikan RW antara 0 sampai 100',
            'phone_number.required' => 'Mohon tuliskan nomor seluler',
            'phone_number.numeric' => 'Pastikan nomor seluler hanya mengandung angka',
            'email.required' => 'Mohon tuliskan alamat email',
            'email.email' => 'Mohon tuliskan alamat email yang valid',
        ];

        $request->validate([
            'name' => 'required',
            'nickname' => 'required',
            'photo' => 'file|max:1024|mimes:jpg,jpeg,png,webp|dimensions:min_width=100,min_height=200',
            'nik' => [
                'required',
                'numeric',
                Rule::unique('Modules\FarmManagement\Models\GreenhouseOwner','nik')
            ],
            'npwp' => 'nullable|numeric',
            'gender' => 'required|exists:App\Models\JenisKelamin,id',
            'birth_place' => 'required',
            'birthday_year' => 'required|numeric',
            'birthday_month' => 'required|numeric',
            'birthday_day' => 'required|numeric',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required|exists:Modules\Core\Models\References\Region,code',
            'address' => 'required',
            'rt' => 'required|integer|between:0,100',
            'rw' => 'required|integer|between:0,100',
            'phone_number' => 'required|numeric',
            'email' => 'required|email'
        ], $messages);

        $village = Region::select('id','code')->whereRaw('LENGTH(code) = 13')->where('code',$request->village)->first();
        if(!$village) return redirect()->back()->withInput();

        $owner = GreenhouseOwner::where(['nik' => $request->nik]);

        if($owner->count() < 1){
            if($request->file('photo') && $request->file('photo')->isValid()) {
                // Pindah foto owner ke folder public
                $file = $request->file('photo');
                $photo = $request->nik . '_' . time() . '_photo.' . $file->getClientOriginalExtension();
                $file->storeAs('img/photo/owner/', $photo, 'public');
            }

            $item = new GreenhouseOwner();
            $item->name = $request->name;
            $item->nickname = $request->nickname;
            $item->photo = isset($photo) ? $photo : null;
            $item->nik = $request->nik;
            $item->npwp = isset($request->npwp) ? $request->npwp : null;
            $item->gender_id = $request->gender;
            $item->birth_place = ucwords($request->birth_place);
            $item->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
            $item->address = $request->address;
            $item->rt = $request->rt;
            $item->rw = $request->rw;
            $item->region_id = isset($village) ? $village->id : null;
            $item->phone_number = $request->phone_number;
            $item->email = $request->email;
            $item->unit_id = null;
            $item->active_status_id = 5; // Aktif
            $item->save();

            $item->refresh();

            $role = Role::select('id','role_group_id')->where('name','owner')->first();

            if(!$role) $role = Role::select('id','role_group_id')->where('name','undefined')->first();

            $user = new LoginUser();
            $user->username = $item->email;
            $user->email = $item->email;
            $user->password = bcrypt(Carbon::parse($item->birth_date)->format('dmY'));
            $user->user_id = $item->id;
            $user->role_id = $role->id;
            $user->status_id = 5; // Aktif
            $user->save();

            $user->refresh();

            $user->profiles()->create([
                'profilable_id' => $item->id,
                'profilable_type' => $role->group->profilable_type,
                'is_default' => 1,
            ]);

            Session::flash('success','Data '. $item->name .' berhasil ditambahkan');
        }
        else{
            $owner = $owner->first();
            Session::flash('danger','Data '.$owner->nik.' sudah pernah ditambahkan');
        }

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
        $data = GreenhouseOwner::where('id',$id)->first();

        if($data){
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-show', compact('data','active','route'));
        }
        return redirect()->route($this->route.'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = GreenhouseOwner::find($id);

        if($data){
            $genders = Gender::all();

            $provinces = Region::provinces()->get();

            $cities = $subdistricts = $villages = null;

            if($data->region){
                $cities = Region::citiesByCode($data->region->code)->orderBy('name')->get();
                $subdistricts = Region::subdistrictsByCode($data->region->code)->orderBy('name')->get();
                $villages = Region::villagesByCode($data->region->code)->orderBy('name')->get();
            }

            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-edit', compact('data','active','route','genders','provinces','cities','subdistricts','villages'));
        }
        return redirect()->route($this->route.'.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $messages = [
            'name.required' => 'Mohon tuliskan nama lengkap dan gelar',
            'nickname.required' => 'Mohon tuliskan nama panggilan',
            'photo.file' => 'Pastikan foto adalah berkas yang valid',
            'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 1 MB',
            'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, .png, atau .webp',
            'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 100x200 px',
            'nik.required' => 'Mohon tuliskan NIK',
            'nik.numeric' => 'Pastikan NIK hanya mengandung angka',
            'nik.unique' => 'Pastikan NIK belum terdaftar',
            'npwp.numeric' => 'Pastikan NPWP hanya mengandung angka',
            'gender.required' => 'Mohon pilih salah satu jenis kelamin',
            'gender.exists' => 'Mohon pilih jenis kelamin yang valid',
            'birth_place.required' => 'Mohon tuliskan tempat lahir',
            'birthday_year.required' => 'Mohon masukkan tahun lahir',
            'birthday_year.numeric' => 'Pastikan tahun lahir hanya mengandung angka',
            'birthday_month.required' => 'Mohon masukkan bulan lahir',
            'birthday_month.numeric' => 'Pastikan bulan lahir hanya mengandung angka',
            'birthday_day.required' => 'Mohon masukkan tanggal lahir',
            'birthday_day.numeric' => 'Pastikan tanggal lahir hanya mengandung angka',
            'province.required' => 'Mohon pilih salah satu provinsi',
            'city.required' => 'Mohon pilih salah satu kabupaten/kota',
            'subdistrict.required' => 'Mohon pilih salah satu kecamatan',
            'village.required' => 'Mohon pilih salah satu desa/kelurahan',
            'village.exists' => 'Mohon pilih desa/kelurahan yang valid',
            'address.required' => 'Mohon tuliskan alamat',
            'rt.required' => 'Mohon masukkan RT',
            'rt.integer' => 'Pastikan RT hanya mengandung angka',
            'rt.between' => 'Pastikan RT antara 0 sampai 100',
            'rw.required' => 'Mohon masukkan RW',
            'rw.integer' => 'Pastikan RW hanya mengandung angka',
            'rw.between' => 'Pastikan RW antara 0 sampai 100',
            'phone_number.required' => 'Mohon tuliskan nomor seluler',
            'phone_number.numeric' => 'Pastikan nomor seluler hanya mengandung angka',
            'email.required' => 'Mohon tuliskan alamat email',
            'email.email' => 'Mohon tuliskan alamat email yang valid',
        ];

        $request->validate([
            'name' => 'required',
            'nickname' => 'required',
            'photo' => 'file|max:1024|mimes:jpg,jpeg,png,webp|dimensions:min_width=100,min_height=200',
            'nik' => [
                'required',
                'numeric',
                Rule::unique('Modules\FarmManagement\Models\GreenhouseOwner','nik')->ignore($request->id)
            ],
            'npwp' => 'nullable|numeric',
            'gender' => 'required|exists:App\Models\JenisKelamin,id',
            'birth_place' => 'required',
            'birthday_year' => 'required|numeric',
            'birthday_month' => 'required|numeric',
            'birthday_day' => 'required|numeric',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required|exists:Modules\Core\Models\References\Region,code',
            'address' => 'required',
            'rt' => 'required|integer|between:0,100',
            'rw' => 'required|integer|between:0,100',
            'phone_number' => 'required|numeric',
            'email' => 'required|email'
        ], $messages);

        $village = Region::select('id','code')->whereRaw('LENGTH(code) = 13')->where('code',$request->village)->first();
        if(!$village) return redirect()->back()->withInput();

        $item = GreenhouseOwner::where(['nik' => $request->nik])->first();

        if($item){
            if($request->file('photo') && $request->file('photo')->isValid()) {
                // Hapus file foto owner di folder public
                if($item->photoPath && Storage::disk('public')->exists('img/photo/owner/'.$item->photo)) Storage::disk('public')->delete('img/photo/owner/'.$item->photo);

                // Pindah foto owner ke folder public
                $file = $request->file('photo');
                $photo = $request->nik . '_' . time() . '_photo.' . $file->extension();
                $file->storeAs('img/photo/owner/', $photo, 'public');
            }

            $old = $item->name;
            $item->name = $request->name;
            $item->nickname = $request->nickname;
            $item->photo = isset($photo) ? $photo : $item->photo;
            $item->nik = $request->nik;
            $item->npwp = $request->npwp;
            $item->gender_id = $request->gender;
            $item->birth_place = ucwords($request->birth_place);
            $item->birth_date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
            $item->address = $request->address;
            $item->rt = $request->rt;
            $item->rw = $request->rw;
            $item->region_id = $village->id;
            $item->email = $request->email;
            $item->phone_number = $request->phone_number;

            $item->save();

            $item->refresh();

            Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->name ? ' menjadi '.$item->name : null));
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
        $item = GreenhouseOwner::find($id);
        $used_count = $item ? $item->units()->count() : 0;
        if($item && $used_count < 1){
            // Hapus file foto owner di folder public
            if($item->photoPath && Storage::disk('public')->exists('img/photo/owner/'.$item->photo)) Storage::disk('public')->delete('img/photo/owner/'.$item->photo);

            $name = $item->name;

            foreach($item->userProfiles as $profile){
                $profile->user->delete();
                $profile->delete();
            }

            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}

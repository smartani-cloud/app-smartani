<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Rekrutmen\KategoriPegawai;
use App\Models\Rekrutmen\Pegawai;

class KepegawaianController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        
        $viewYayasan = in_array($role,['admin','pembinayys','ketuayys','direktur','etl','etm','fam','am']);

        $pejabat = Pegawai::whereIn('position_id',['15','16','17'])->pluck('id');

        $pegawai = Pegawai::select('id','name','photo','nip','gender_id','birth_date','position_id','unit_id','employee_status_id','join_date');

        if(in_array($role,['kepsek','wakasek'])){
            $pegawai = $pegawai->whereHas('units',function($q)use($request){
                $q->where('unit_id',$request->user()->pegawai->unit_id);
            })->orderBy('created_at','desc');
        }
        else{
            $pegawai = $pegawai->orderBy('created_at','desc');
        }

        if(!$viewYayasan){
            $pegawai = $pegawai->where('nip','!=','0')->whereNotIn('id',$pejabat);
        }

        $pegawai = $pegawai->aktif()->get();

        $count = [
            'total' => 0,
            'pt' => 0,
            'ptt' => 0,
            'ptth' => 0,
            'pttk' => 0,
            'auliya' => 0,
            'mitra' => 0,
            'yayasan' => 0,
            'laki' => 0,
            'perempuan' => 0,
            'tk' => 0,
            'sd' => 0,
            'smp' => 0,
            'sma' => 0,
            'guru_tk' => 0,
            'guru_sd' => 0,
            'guru_smp' => 0,
            'guru_sma' => 0,
            'guru_multi' => 0
        ];

        if(isset($pegawai)){
            $count['pt'] = $pegawai->where('employee_status_id',1)->count();
            $count['pttk'] = $pegawai->where('employee_status_id',3)->count();
            $count['ptth'] = $pegawai->where('employee_status_id',4)->count();
            $count['ptt'] = $count['pttk'] + $count['ptth'];
            $kategori = KategoriPegawai::select('id','name')->get();
            foreach($kategori as $k){
                $count[strtolower($k->name)] = $pegawai->whereIn('employee_status_id',$k->statuses()->select('id')->get()->pluck('id')->toArray())->count();    
            }
            $count['total'] = $count['pt']+$count['ptt'];

            $count['laki'] = $pegawai->whereIn('employee_status_id',[1,3,4])->where('gender_id',1)->count();
            $count['perempuan'] = $pegawai->whereIn('employee_status_id',[1,3,4])->where('gender_id',2)->count();

            $count['tk'] = Pegawai::whereIn('employee_status_id',[1,3,4])->whereHas('units',function($q){
                $q->where('unit_id',1);
            })->aktif()->count();
            $count['sd'] = Pegawai::whereIn('employee_status_id',[1,3,4])->whereHas('units',function($q){
                $q->where('unit_id',2);
            })->aktif()->count();
            $count['smp'] = Pegawai::whereIn('employee_status_id',[1,3,4])->whereHas('units',function($q){
                $q->where('unit_id',3);
            })->aktif()->count();
            $count['sma'] = Pegawai::whereIn('employee_status_id',[1,3,4])->whereHas('units',function($q){
                $q->where('unit_id',4);
            })->aktif()->count();
            $count['manajemen'] = Pegawai::whereIn('employee_status_id',[1,3,4])->whereHas('units',function($q){
                $q->where('unit_id',5);
            })->aktif()->count();

            $count['guru_tk'] = Pegawai::whereIn('employee_status_id',[1,3,4,5])->has('units','=',1)->whereHas('units',function($q){
                $q->where('unit_id',1)->whereHas('jabatans',function($q){
                    $q->whereIn('position_id',[1,2,5,6]);
                });
            })->aktif()->count();
            $count['guru_sd'] = Pegawai::whereIn('employee_status_id',[1,3,4,5])->has('units','=',1)->whereHas('units',function($q){
                $q->where('unit_id',2)->whereHas('jabatans',function($q){
                    $q->whereIn('position_id',[1,2,3,5,7]);
                });
            })->aktif()->count();
            $count['guru_smp'] = Pegawai::whereIn('employee_status_id',[1,3,4,5])->has('units','=',1)->whereHas('units',function($q){
                $q->where('unit_id',3)->whereHas('jabatans',function($q){
                    $q->whereIn('position_id',[1,2,3,5,7]);
                });
            })->aktif()->count();
            $count['guru_sma'] = Pegawai::whereIn('employee_status_id',[1,3,4,5])->has('units','=',1)->whereHas('units',function($q){
                $q->where('unit_id',4)->whereHas('jabatans',function($q){
                    $q->whereIn('position_id',[1,2,3,5,7]);
                });
            })->aktif()->count();
            $count['guru_multi'] = Pegawai::whereIn('employee_status_id',[1,3,4,5])->has('units','>',1)->whereHas('units',function($q){
                $q->whereHas('jabatans',function($q){
                    $q->whereIn('position_id',[3,5,6,7]);
                });
            })->aktif()->count();
        }
        
        if(in_array($role,['pembinayys','ketuayys','direktur','etl','etm','fam','faspv','am','aspv']))
            $folder = 'leadership';
        elseif(in_array($role,[
            'kepsek','wakasek']))
            $folder = 'kepsek';
        else $folder = 'read-only';

        return view('kepegawaian.'.$folder.'.dasbor_index', compact('viewYayasan','pejabat','pegawai','count'));
        //return view('kepegawaian.'.$folder.'.dasbor_index');
    }
}

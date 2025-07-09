<?php

namespace App\Http\Controllers\Pelatihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Jenssegers\Date\Date;

use App\Models\Pelatihan\Pelatihan;
use App\Models\Pelatihan\PresensiPelatihan;
use App\Models\Pelatihan\StatusPresensi;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Rekrutmen\Pegawai;

class KehadiranPelatihanController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $pelatihan = $request->id ? Pelatihan::aktif()->where('id',$request->id)->first() : null;
        $kehadiran = StatusPresensi::where('status',$request->status)->first();
        $pegawai = $request->employee_id ? Pegawai::aktif()->where('id',$request->employee_id)->first() : null;

        if(!$kehadiran){
            $response = collect([
                'status' => 'danger',
                'message' => 'Data kehadiran pelatihan gagal disimpan'
            ]);

            return $response->toJson();
        }

        if($pelatihan && $pegawai){
            $sasaran = $pelatihan->sasaran()->pluck('position_id');
            $peserta = null;
            if($pegawai->units()->count() > 0){
                $posisi = JabatanUnit::whereIn('id',$sasaran)->whereIn('unit_id',$pegawai->units()->pluck('unit_id'))->pluck('position_id');
                $peserta = Pegawai::aktif()->where('id',$pegawai->id)->whereHas('units',function($query)use($pegawai,$posisi){
                    $query->whereIn('unit_id',$pegawai->units()->pluck('unit_id'))->whereHas('jabatans',function($query)use($posisi){
                        $query->whereIn('position_id',$posisi);
                    });
                })->first();
            }

            if($peserta){
                if($request->user()->role->name == 'etm'){
                    $nama = $peserta->name;
                    $presensi = $pelatihan->presensi()->where('employee_id', $pegawai->id)->first();
                    if(!$presensi){
                        $presensi = new PresensiPelatihan();
                        $presensi->employee_id = $peserta->id;
                        $presensi->education_acc_id = $request->user()->pegawai->id;
                        $presensi->presence_status_id = $kehadiran->id;
                        $presensi->education_acc_time = Date::now('Asia/Jakarta');

                        $pelatihan->presensi()->save($presensi);
                    }
                    else{
                        $presensi->education_acc_id = $request->user()->pegawai->id;
                        $presensi->presence_status_id = $kehadiran->id;
                        $presensi->education_acc_time = Date::now('Asia/Jakarta');
                    }

                    $presensi->save();

                    $presensi->fresh();

                    $response = collect([
                        'status' => 'success',
                        'message' => 'Data kehadiran pelatihan '.$nama.' berhasil disimpan',
                        'acc_time' => Date::parse($presensi->education_acc_time)->format('j M Y H.i.s')
                    ]);

                    return $response->toJson();
                }
                else{
                    $response = collect([
                        'status' => 'danger',
                        'message' => 'Data kehadiran pelatihan gagal disimpan'
                    ]);

                    return $response->toJson();
                }
            }
            else{
                $response = collect([
                    'status' => 'danger',
                    'message' => 'Pegawai tidak ditemukan'
                ]);

                return $response->toJson();
            }
        }
        else{
            $message = 'Data tidak ditemukan';
            if(!$pelatihan) $message = 'Pelatihan tidak ditemukan';
            elseif(!$pegawai) $message = 'Pegawai tidak ditemukan';
            $response = collect([
                'status' => 'danger',
                'message' => $message
            ]);

            return $response->toJson();
        }
    }

    /**
     * Cancel the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        $pelatihan = $request->id ? Pelatihan::aktif()->where('id',$request->id)->first() : null;
        $pegawai = $request->employee_id ? Pegawai::aktif()->where('id',$request->employee_id)->first() : null;

        if($pelatihan && $pegawai){
            $nama = $pegawai->name;
            $presensi = $pelatihan->presensi()->where('employee_id', $pegawai->id)->first();
            
            if($presensi){
                $presensi->education_acc_id = null;
                $presensi->presence_status_id = null;
                $presensi->education_acc_time = null;

                $presensi->save();

                $response = collect([
                    'status' => 'success',
                    'message' => 'Data kehadiran pelatihan '.$nama.' berhasil dibatalkan'
                ]);
            }
            else{
                $response = collect([
                    'status' => 'danger',
                    'message' => 'Peserta tidak ditemukan'
                ]);
            }

            return $response->toJson();
        }
        else{
            $message = 'Data tidak ditemukan';
            if(!$pelatihan) $message = 'Pelatihan tidak ditemukan';
            elseif(!$pegawai) $message = 'Pegawai tidak ditemukan';
            $response = collect([
                'status' => 'danger',
                'message' => $message
            ]);

            return $response->toJson();
        }
    }
}

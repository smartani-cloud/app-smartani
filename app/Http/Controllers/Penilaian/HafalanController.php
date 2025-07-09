<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Alquran\Juz;
use App\Models\Alquran\StatusHafalan;
use App\Models\Alquran\Surat;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Surah;
use App\Models\Penilaian\Tahfidz;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class HafalanController extends Controller
{
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $siswa = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $riwayatKelas = null;

        $juz = $surat = $status = $rpd = null;
        
        $semesterList = Semester::all();

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
                    if($role == 'guru'){
                        $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
                    }
                    $kelas = $kelas->first();

                    if($kelas){
                        // Inti Function
                        $riwayatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name')->values();
                        if($siswa){
                        	$riwayat = $kelas->riwayat()->select('student_id')->where(['semester_id' => $semester->id, 'student_id' => $siswa])->first();
                        	if($riwayat){
                                $siswa = $riwayat->siswa()->select('id')->first();
                        		$juz = Juz::orderBy('id','desc')->get();
						        $surat = Surat::all();
						        $status = StatusHafalan::orderBy('id','desc')->get();

        						$quran = $kelas->unit->mataPelajaran()->select('id')->where('subject_name', 'like', "Qur'an")->first();

						        $jadwal = $quran ? $kelas->jadwal()->where([
			                        'subject_id' => $quran->id,
			                        'level_id' => $kelas->level_id,
			                        'semester_id' => $semester->id
			                    ])->first() : null;

			                    $rpd = $jadwal ? $quran->predicate()->whereHas('RpdType',function($q){
			                        $q->where('rpd_type','Hafalan');
			                    })->where([
			                        'employee_id' => $jadwal->teacher_id,
			                        'level_id' => $kelas->level_id,
			                        'semester_id' => $semester->id
			                    ])->orderBy('predicate', 'ASC')->get() : null;
                        	}
		                    else{
		                        return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
		                    }
                        }
                    }
                    else{
                        return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
            }
            else{
            	if($role == 'guru'){
            		$kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
            		if($kelas){
            			return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            		}
            		else return redirect()->route('penilaian.hafalan.index');
            	}
            }
        }
        else{
            $semester = Semester::aktif()->first();
            $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get();
            if($role == 'guru'){
                $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'teacher_id' => $request->user()->pegawai->id])->first();
                if($kelas){
                    return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
                else return redirect()->route('penilaian.hafalan.index');
            }
        }

        return view('penilaian.'.$role.'.hafalan', compact('semesterList', 'semester', 'kelasList', 'kelas', 'riwayatKelas', 'siswa', 'juz', 'surat', 'status', 'rpd'));
    }

    public function update(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = $request->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('penilaian.hafalan.index');

        $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]);
            if($role == 'guru'){
                $kelas = $kelas->where('teacher_id', $request->user()->pegawai->id);
            }
            $kelas = $kelas->first();

            if($kelas){
                if($siswa){
                	$riwayat = $kelas->riwayat()->select('student_id')->where(['semester_id' => $semester->id, 'student_id' => $siswa])->first();
                	if($riwayat){
                		$siswa = $riwayat->siswa()->select('id')->first();
                		$pwedit = md5($request->pwedit);
                        $pass = $request->user()->pegawai->verification_password;
                        if($pwedit == $pass){
                        	$rapor = $siswa->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                            if($rapor){
                            	$tahfidz = $rapor ? $rapor->tahfidz : null;
                            	if(!$tahfidz){
                            		$tahfidz = $rapor->tahfidz()->save(new Tahfidz());
                            		$tahfidz->fresh();
                            	}
                            	$detail = $tahfidz->detail();
	                            if($detail->count() > 0){
	                            	$detail->delete();
	                            }
                            	$success = 0;
                            	if($request->jenis){
			                        foreach($request->jenis as $key => $jenis){
			                            if(in_array($jenis,['surat','juz'])){
					                		$juz = isset($request->juz[$key]) && $jenis == 'juz' ? Juz::where('id',$request->juz[$key])->first() : null;
					                		$surat = isset($request->surat[$key]) && $jenis == 'surat' ? Surat::where('id',$request->surat[$key])->first() : null;
					                		$status = isset($request->status[$key]) ? StatusHafalan::where('id',$request->status[$key])->first() : null;

			                                $detail = new Surah();
			                                $detail->juz_id = $juz ? $juz->id : null;
			                                $detail->surah_id = $surat ? $surat->id : null;
			                                $detail->status_id = $status ? $status->id : null;
			                                $detail->predicate = isset($request->predikat[$key]) || strlen($request->predikat[$key]) > 0 ? $request->predikat[$key] : '';
			                                $tahfidz->detail()->save($detail);
			                                $success++;
			                            }
			                        }
			                    }
			                    if(isset($request->deskripsi)){
				                    $quran = $kelas->unit->mataPelajaran()->select('id')->where('subject_name', 'like', "Qur'an")->first();

			                		$jadwal = $quran ? $kelas->jadwal()->where([
			                			'subject_id' => $quran->id,
			                			'level_id' => $kelas->level_id,
			                			'semester_id' => $semester->id
			                		])->first() : null;

			                		$rpd = $jadwal ? $quran->predicate()->whereHas('RpdType',function($q){
			                			$q->where('rpd_type','Hafalan');
			                		})->where([
			                			'id' => $request->deskripsi,
			                			'employee_id' => $jadwal->teacher_id,
			                			'level_id' => $kelas->level_id,
			                			'semester_id' => $semester->id
			                		]) : null;

			                		if($rpd && $rpd->count() > 0){
			                			$tahfidz->rpd_id = $request->deskripsi;
			                			$tahfidz->save();
			                		}
			                    }
			                    if($success > 0 && ($success == count($request->jenis))){
			                    	Session::flash('success', 'Semua perubahan nilai hafalan berhasil disimpan');
			                    }
			                    elseif($success > 0 && ($success < count($request->jenis))){
			                    	Session::flash('success', 'Beberapa perubahan nilai hafalan berhasil disimpan');
			                    }
                            }
                            else{
                            	Session::flash('danger', 'Belum ada nilai yang dimasukkan untuk siswa ini');
                            }
                        }
                        else{
                            Session::flash('danger', 'Password Verifikasi Tidak Sesuai! Harap mengisi password dengan benar dan pastikan Anda telah mengkonfigurasi password verifikasi!');
                        }
                        return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->id]);
                	}
                	else{
                		Session::flash('danger', 'Tidak ada data siswa yang ditemukan');
                	}
                }
                return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route('penilaian.hafalan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route('penilaian.hafalan.index');
        }
    }
}


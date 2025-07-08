@extends('template.main.master')

@section('title')
Akun Anggaran
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Akun Anggaran</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">Akun Anggaran</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Akun Anggaran</h6>
            </div>
            @if(count($akun) > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Bisa Diisi</th>
                            <th>Eksklusif</th>
                            <th>Kategori</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                      @php $no = 1; @endphp
                      @foreach($akun as $a)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $a->code }}</td>
                            <td>{{ $a->name }}</td>
                            <td>
                              @if($a->is_fillable == 1)
                              <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Ya"></i>
                              @elseif($a->is_fillable == 0)
                              <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak"></i>
                              @else
                              -
                              @endif
                            </td>
                            <td>
                              @if($a->is_exclusive == 1)
                              <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Ya"></i>
                              @elseif($a->is_exclusive == 0)
                              <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak"></i>
                              @else
                              -
                              @endif
                            </td>
                            <td>
                              {{ $a->kategori ? $a->kategori->name : '-'}}
                            </td>
                            <td>
                              @if(!$a->deleted_at)
                              <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Aktif"></i>
                              @else
                              <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak"></i>
                              @endif
                            </td>
                            @php
                            $usedCount = $exUsedCount = null;
                            if($a->is_exclusive == 0){
                              $usedCount = $a->ppa()->whereHas('ppa',function($q)use($tahunPelajaran,$tahun){
                                $q->where(function($q)use($tahunPelajaran,$tahun){
                                  $q->where(function($q)use($tahunPelajaran){
                                    $q->where('academic_year_id', $tahunPelajaran->id)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','LIKE','APB-KSO%');
                                    })->where(function($q){
                                      $q->where(function($q){
                                        $q->doesntHave('lppa')->whereHas('bbk.bbk',function($q){
                                          $q->has('jenisAnggaran')->where(function($q){
                                            $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                          });
                                        });
                                      })->orWhereHas('lppa',function($q){
                                        $q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                      });
                                    });
                                  })->orWhere(function($q)use($tahun){
                                    $q->where('year', $tahun)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','APBY');
                                    })->where(function($q){
                                      $q->where(function($q){
                                        $q->doesntHave('lppa')->whereHas('bbk.bbk',function($q){
                                          $q->has('jenisAnggaran')->where(function($q){
                                            $q->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                          });
                                        });
                                      })->orWhereHas('lppa',function($q){
                                        $q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                      });
                                    });
                                  });
                                });
                              })->count();
                            }
                            elseif($a->is_exclusive == 1){
                              $exUsedCount = $a->ppa()->whereHas('ppa',function($q)use($tahunPelajaran,$tahun){
                                $q->where(function($q)use($tahunPelajaran,$tahun){
                                  $q->where(function($q)use($tahunPelajaran){
                                    $q->where('academic_year_id', $tahunPelajaran->id)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','LIKE','APB-KSO%');
                                    })->whereHas('bbk.bbk',function($q){
                                      $q->has('jenisAnggaran')->where(function($q){
                                        $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                      });
                                    });
                                  })->orWhere(function($q)use($tahun){
                                    $q->where('year', $tahun)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','APBY');
                                    })->whereHas('bbk.bbk',function($q){
                                      $q->has('jenisAnggaran')->where(function($q){
                                        $q->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                      });
                                    });
                                  });
                                });
                              })->count();
                            }
                            @endphp
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data akun anggaran yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@endsection
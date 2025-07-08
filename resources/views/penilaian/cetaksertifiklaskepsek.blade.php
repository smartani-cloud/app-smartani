@extends('template.main.master')

@section('title')
Cetak Sertifikat IKLaS
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cetak Sertifikat IKLaS</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Seritikat</a></li>
        <li class="breadcrumb-item active" aria-current="page">IKLaS</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran</label>
                            <div class="col-sm-9">
                                <select aria-label="Tahun" name="tahunajaran" class="form-control" id="academicYearOpt" onchange="getSiswa(this.value)" required="required">
                                    @foreach($tahun as $t)
                                    @if($t->is_active == 1 || $t->sertifIklas()->where('unit_id',Auth::user()->pegawai->unit->id)->count() > 0)
                                    <option value="{{ $t->academic_year_start.'-'.$t->academic_year_end }}" {{ $tahunsekarang->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="listSiswa">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Cetak Sertifikat IKLaS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($sertif) > 0)
                                @foreach($sertif as $s)
                                <tr>
                                    <td>{{$s->siswa->identitas->student_name}}</td>
                                    <td class="text-center">
                                        @if($s->tahunAjaran->semester->where('is_active',1)->first()->semester == 'Genap')
                                        <form action="{{route('sertifiklaskepsek.print')}}" target="_blank" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$s->id}}">
                                            <button type="submit" class="btn btn-brand-green btn-sm"><i class="fa fas fa-print"></i> Cetak Sertifikat</button>&nbsp;
                                        </form>
                                        @else
                                        Menunggu Semester Genap
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="2" class="text-center">Data Kosong</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<script type="text/javascript">
    function getSiswa(tahunajaran){
        var faSpin = $('<i></i>').addClass('fa fa-spin fa-circle-notch fa-lg text-brand-green');
        var loadingText = $('<h5></h5>').addClass('font-weight-light mb-3').html('Memuat...');
        var loadingContainer = $('<div></div>').addClass('text-center my-5').append(faSpin,loadingText);
        $('#listSiswa').html(loadingContainer);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('sertifiklaskepsek.getsiswa') }}",
            data: {
                'tahunajaran': tahunajaran,
                'unit_id': {{ auth()->user()->pegawai->unit->id }}
            },
            type: 'POST',
            success: function(response) {
                $('#listSiswa').html(response.html);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection
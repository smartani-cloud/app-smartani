<<<<<<< HEAD
@extends('penilaian.iku_edukasi_persen_index')

@section('ledger')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green percentage-title">Mengumpulkan Data...</h6>
            </div>
            <div class="card-body pt-1 pb-4 px-4">
                @php
                $globalPercentage = 0;
                $color = 'brand-green';
                @endphp
                <div class="d-flex align-items-center">
                  <div class="text-percentage mr-3">{{ number_format($globalPercentage, 0, ',', '.') }}%</div>
                  <div style="width:100%">
                    <div class="progress">
                      <div id="loadingProgress" class="progress-bar bg-brand-green progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $globalPercentage }}%" data-toggle="tooltip" title="{{ '0 / '.number_format(($kelasList ? count($kelasList) : 0), 0, ',', '.') }}" aria-valuenow="{{ $globalPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Kelas</h6>
            </div>
            <div class="card-body p-3">
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ Session::get('danger') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(count($kelasList) > 0)
                <div class="table-load p-4">
                    <div class="row">
                      <div class="col-12">
                        <div class="text-center my-5">
                          <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
                          <h5 class="font-weight-light mb-3">Memuat...</h5>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="table-responsive" style="display: none;">
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="3">Kelas</th>
                                @php
                                $competences = $refIklas->groupBy('competence')->keys()->all();
                                @endphp
                                @foreach($competences as $c)
                                @php
                                $categoryCount = $refIklas->where('competence',$c)->count();
                                @endphp
                                <th colspan="{{ $categoryCount > 0 ? $categoryCount : '1'}}">{{ $c }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($refIklas as $i)
                                <th>{{ $i->categoryNumber.' '.$i->category }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($refIklas as $i)
                                <th>Prosentase Pencapaian</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data kelas yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-scripts')

<script>
var classCount = 0;
var classes = {!! $kelasList->sortBy('levelName')->pluck('id') !!}

$(document).ready(function()
{
    // $('#filter_submit').click(function(){
    //     getData();
    // });
    classes.map((item, index) => {
        getData(item);
    });
});

function getData(id){
    //var score = $('input[name="score"]').val();
    var start = Date.now();
    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            class : id
        },
        beforeSend  : function() {
          console.log("Loading...");
        },
        complete    : function() {
        }, 
        success: function async(response){
          if(response.status == 'success'){
            console.log(response);
            var t = $('#dataTable').DataTable();
            var id = null;
            var loop = 1;
            response.data.map((item, index) => { 
                row = [item.name];
                item.percentages.map((item, index) => {
                    row.push(item+'%');
                });
                t.row.add(row).draw(false);
              // let row = '<tr>+'+
              //           '<td>'+item.id+'</td>'+
              //           '<td>'+item.name+'</td>';
              // item.percentages.map((item, index) => {
              //   row += '<td>'+item+'%</td>';
              // });
              // row += '</tr>';
              // $('#tbody').append(row);
            });
            var end = Date.now(); // this happens AFTER you've fetched the data
            console.log(end - start)

            classCount++;

            var pcg = Math.floor((classCount/classes.length)*100);  
            $('#loadingProgress').attr('style','width:'+pcg+'%').attr('aria-valuenow',pcg+'%').attr('data-original-title',classCount+' / '+classes.length);
            $('.text-percentage').html(pcg+'%');

            if(classCount == 1){
                $('.table-load').hide();
                $('.table-responsive').show();
            }

            if(classCount >= classes.length){
                if($('#loadingProgress').hasClass("progress-bar-striped progress-bar-animated"))
                    $('#loadingProgress').removeClass("progress-bar-striped progress-bar-animated");
                $('.percentage-title').html('Data Berhasil Termuat');
            }
          }
          else{
            if(response.message)
              alert(response.message);
            else alert('Sorry, the server is returning an unknown error');
          }
        },
        error: function(xhr, textStatus, errorThrown){
          var errorMessage = xhr.status + ': ' + xhr.statusText
          alert('Error - ' + xhr.responseText);
        },
    });
}
</script>
=======
@extends('penilaian.iku_edukasi_persen_index')

@section('ledger')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green percentage-title">Mengumpulkan Data...</h6>
            </div>
            <div class="card-body pt-1 pb-4 px-4">
                @php
                $globalPercentage = 0;
                $color = 'brand-green';
                @endphp
                <div class="d-flex align-items-center">
                  <div class="text-percentage mr-3">{{ number_format($globalPercentage, 0, ',', '.') }}%</div>
                  <div style="width:100%">
                    <div class="progress">
                      <div id="loadingProgress" class="progress-bar bg-brand-green progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $globalPercentage }}%" data-toggle="tooltip" title="{{ '0 / '.number_format(($kelasList ? count($kelasList) : 0), 0, ',', '.') }}" aria-valuenow="{{ $globalPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Kelas</h6>
            </div>
            <div class="card-body p-3">
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ Session::get('danger') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(count($kelasList) > 0)
                <div class="table-load p-4">
                    <div class="row">
                      <div class="col-12">
                        <div class="text-center my-5">
                          <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
                          <h5 class="font-weight-light mb-3">Memuat...</h5>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="table-responsive" style="display: none;">
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="3">Kelas</th>
                                @php
                                $competences = $refIklas->groupBy('competence')->keys()->all();
                                @endphp
                                @foreach($competences as $c)
                                @php
                                $categoryCount = $refIklas->where('competence',$c)->count();
                                @endphp
                                <th colspan="{{ $categoryCount > 0 ? $categoryCount : '1'}}">{{ $c }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($refIklas as $i)
                                <th>{{ $i->categoryNumber.' '.$i->category }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($refIklas as $i)
                                <th>Prosentase Pencapaian</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data kelas yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-scripts')

<script>
var classCount = 0;
var classes = {!! $kelasList->sortBy('levelName')->pluck('id') !!}

$(document).ready(function()
{
    // $('#filter_submit').click(function(){
    //     getData();
    // });
    classes.map((item, index) => {
        getData(item);
    });
});

function getData(id){
    //var score = $('input[name="score"]').val();
    var start = Date.now();
    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            class : id
        },
        beforeSend  : function() {
          console.log("Loading...");
        },
        complete    : function() {
        }, 
        success: function async(response){
          if(response.status == 'success'){
            console.log(response);
            var t = $('#dataTable').DataTable();
            var id = null;
            var loop = 1;
            response.data.map((item, index) => { 
                row = [item.name];
                item.percentages.map((item, index) => {
                    row.push(item+'%');
                });
                t.row.add(row).draw(false);
              // let row = '<tr>+'+
              //           '<td>'+item.id+'</td>'+
              //           '<td>'+item.name+'</td>';
              // item.percentages.map((item, index) => {
              //   row += '<td>'+item+'%</td>';
              // });
              // row += '</tr>';
              // $('#tbody').append(row);
            });
            var end = Date.now(); // this happens AFTER you've fetched the data
            console.log(end - start)

            classCount++;

            var pcg = Math.floor((classCount/classes.length)*100);  
            $('#loadingProgress').attr('style','width:'+pcg+'%').attr('aria-valuenow',pcg+'%').attr('data-original-title',classCount+' / '+classes.length);
            $('.text-percentage').html(pcg+'%');

            if(classCount == 1){
                $('.table-load').hide();
                $('.table-responsive').show();
            }

            if(classCount >= classes.length){
                if($('#loadingProgress').hasClass("progress-bar-striped progress-bar-animated"))
                    $('#loadingProgress').removeClass("progress-bar-striped progress-bar-animated");
                $('.percentage-title').html('Data Berhasil Termuat');
            }
          }
          else{
            if(response.message)
              alert(response.message);
            else alert('Sorry, the server is returning an unknown error');
          }
        },
        error: function(xhr, textStatus, errorThrown){
          var errorMessage = xhr.status + ': ' + xhr.statusText
          alert('Error - ' + xhr.responseText);
        },
    });
}
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection
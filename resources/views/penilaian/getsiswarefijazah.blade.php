@if($siswa->isEmpty())
<hr>
@else
<hr>
<?php foreach ($siswa as $siswae) {
    if ($siswae->report_status_id != 1) {
        $valid_all = FALSE;
        break;
    } else {
        $valid_all = TRUE;
    }
} ?>
@if ($valid_all == FALSE && auth()->user()->role->name == 'kepsek')
<div class="row">
    <div class="col-md-12">
        <a data-toggle="modal" data-target="#ConfirmModal" class="float-right btn btn-success text-white mb-2" style="cursor: pointer;">Validasi Semua</a>
    </div>
</div>
@endif
@endif
<table class="table align-items-center table-flush">
    <thead class="thead-light">
        <tr>
            <th>Nama Siswa</th>
            <th class="text-right">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if($siswa->isEmpty())
        <tr>
            <td colspan="2" class="text-center">Data Kosong</td>
        </tr>
        @else
        @foreach ($siswa as $siswas)
        <tr>
            <td>
                {{$siswas->siswa->identitas->student_name}}
                @if ($siswas->report_status_id == 1)
                &nbsp;<span class="badge badge-success">Tervalidasi</span>
                @endif
            </td>
            <td class="text-right">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('refijazah.lihatnilai')}}" target="_blank" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{$siswas->student_id}}">
                            <input type="hidden" name="semester" value="{{$semester->id}}">
                            <input type="hidden" name="level_id" value="{{$siswas->siswa->kelas->level_id}}">
                            <input type="hidden" name="major_id" value="{{$siswas->siswa->kelas->major_id}}">
                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat Nilai</button>
                            @if ($siswas->report_status_id == 0 && auth()->user()->role->name == 'kepsek')
                            <a href="/kependidikan/ijazahkepsek/refijazah/validasi/{{$siswas->siswa->id}}"><button type="button" class="btn btn-brand-green btn-sm"><i class="fa fa-check"></i> Validasi</button></a>
                            @endif
                        </form>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
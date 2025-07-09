@if($siswa->isEmpty())
<hr>
@else
<hr>
<div class="row">
    <div class="col-md-12 text-right">
        <?php foreach ($siswa as $siswae) {
            if ($siswae->report_status_id != 1) {
                $valid_all = FALSE;
                break;
            } else {
                $valid_all = TRUE;
            }
        } ?>
        @if($valid_all && $semester->semester == "Genap" && auth()->user()->role->name == 'kepsek')
        @php
        $firstStudent = $siswa[0]->kelas->riwayat()->where(['semester_id' => $semester->id, 'student_id' => $siswa[0]->siswa->id])->first();
        @endphp
        @if($firstStudent && ($firstStudent->level_id == $siswa[0]->siswa->level_id && $firstStudent->class_id == $siswa[0]->siswa->class_id) && (in_array($firstStudent->level_id,[2,8,11,14])))
        <a style="cursor: pointer;" data-toggle="modal" data-target="#NaikModal" class="btn btn-primary text-white mb-2">Konfirmasi Kelulusan</a>
        @elseif($firstStudent && ($firstStudent->level_id == $siswa[0]->siswa->level_id && $firstStudent->class_id == $siswa[0]->siswa->class_id))
        <a style="cursor: pointer;" data-toggle="modal" data-target="#NaikModal" class="btn btn-primary text-white mb-2">Konfirmasi Kenaikan/Tinggal Kelas</a>
        @endif
        @endif
        @if($siswa[0]->report_status_pts_id == 1 && $valid_all == FALSE && auth()->user()->role->name == 'kepsek')
        <a style="cursor: pointer;" data-toggle="modal" data-target="#ConfirmModal" class="btn btn-success text-white mb-2">Validasi Semua</a>
        @endif
    </div>
</div>
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
                @if (auth()->user()->pegawai->unit_id == 1)
                @php
                $route = route('paskepsek.lihatnilaitk');
                @endphp
                @else
                @php
                $route = route('paskepsek.lihatnilai');
                @endphp
                @endif
                <form action="{{$route}}" target="_blank" method="POST">
                    @csrf
                    <input type="hidden" name="semester" value="{{$semester->id}}">
                    <input type="hidden" name="id" value="{{$siswas->student_id}}">
                    <input type="hidden" name="level_id" value="{{$siswas->kelas->level->id}}">
                    <input type="hidden" name="major_id" value="{{$siswas->kelas->major_id}}">
                    @if($siswas->report_status_id == 1 && auth()->user()->role->name == 'kepsek')
                    <a href="{{ route('paskepsek.cover',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $siswas->kelas->id, 'id' => $siswas->student_id])}}" class="btn btn-sm btn-brand-green" target="_blank"><i class="fa fa-print"></i> Cetak Cover</button></a>&nbsp;
                    @endif
                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat Nilai</button>&nbsp;
                    @if($siswas->report_status_id == 1 && auth()->user()->role->name == 'kepsek')
                    <a href="{{ route('paskepsek.akhir',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $siswas->kelas->id, 'id' => $siswas->student_id])}}" class="btn btn-sm btn-secondary" target="_blank"><i class="fa fa-print"></i> Cetak Halaman Akhir</a>&nbsp;
                    @endif
                    @if($siswas->report_status_id == 0 && $siswas->report_status_pts_id == 1 && auth()->user()->role->name == 'kepsek')
                    <a href="/kependidikan/penilaiankepsek/pas/validasi/{{$siswas->siswa->id}}"><button type="button" class="btn btn-brand-green btn-sm"><i class="fa fa-check"></i> Validasi</button></a>
                    @endif
                </form>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
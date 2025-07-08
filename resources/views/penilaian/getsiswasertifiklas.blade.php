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
                @if(($tahunsekarang->id != $s->tahunAjaran->id) || (($tahunsekarang->id == $s->tahunAjaran->id) && $s->tahunAjaran->semester()->where('is_active',1)->first()->semester == 'Genap'))
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
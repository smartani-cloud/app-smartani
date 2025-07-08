<form action="{{route('nilaisikappts.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="mapel_id" id="idmapel" />
    <input type="hidden" name="class_id" id="idkelas" />
    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th>Nama Siswa</th>
                <th class="text-center">Nilai Sikap</th>
            </tr>
        </thead>
        <tbody>
            @if($siswa)
            <?php $i = 0; ?>
            @foreach ($siswa as $key => $siswas)
            <tr>
                <td>{{$siswas->identitas->student_name}}</td>
                <td>
                    <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}" />
                    <select name="sikap[]" class="form-control">
                        <option value="">== Pilih ==</option>
                        <option value="A" <?php if ($nilaisikap[$key] && $nilaisikap[$key]->predicate == "A") echo "selected"; ?>>A</option>
                        <option value="B" <?php if ($nilaisikap[$key] && $nilaisikap[$key]->predicate == "B") echo "selected"; ?>>B</option>
                        <option value="C" <?php if ($nilaisikap[$key] && $nilaisikap[$key]->predicate == "C") echo "selected"; ?>>C</option>
                        <option value="D" <?php if ($nilaisikap[$key] && $nilaisikap[$key]->predicate == "D") echo "selected"; ?>>D</option>
                        <option value="-" <?php if ($nilaisikap[$key] && $nilaisikap[$key]->predicate == "-") echo "selected"; ?>>-</option>
                    </select>
                </td>
            </tr>
            <?php $i++; ?>
            @endforeach
            @else
            <tr>
                <td colspan="2" class="text-center">Data Kosong</td>
            </tr>
            @endif
        </tbody>
    </table>
    @if($countrapor > 0)
    @if($validasi > 0)
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
    @endif
    @else
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
    @endif
</form>
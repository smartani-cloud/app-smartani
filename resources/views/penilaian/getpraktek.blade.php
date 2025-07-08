<form action="{{route('nilaipraktek.simpan')}}" method="POST">
    <input type="hidden" name="mapel_id" id="idmapel" />
    <input type="hidden" name="class_id" id="idkelas" />
    @csrf
    <hr>
    <div class="table-responsive">
        <table class="table align-items-center table-sm" style="width:100%">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th>Nama</th>
                    <th class="text-right">Nilai Praktek</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($siswa as $key => $siswas)
                <tr>
                    <td><label>{{$siswas->identitas->student_name}}</label></td>
                    <td class="float-right">
                        <input type="number" name="praktek[]" <?php if ($nilaipraktek[$key]) echo  'value="' . $nilaipraktek[$key]->score . '"'; ?> class="form-control" style="width: 150px;">
                        <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
</form>
<<<<<<< HEAD
<form action="{{route('nilaiaspek.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa_id}}">

    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th class="text-center">Aspek Perkembangan</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @if(count($aspek) > 0)
            @foreach($aspek as $key => $aspeks)
            <tr>
                <td width="30%">
                    <input type="hidden" name="aspek_id[]" value="{{$aspeks->id}}" required>
                    <input type="text" class="form-control" value="{{$aspeks->dev_aspect}}" readonly>
                </td>
                <td width="70%">
                    <select class="form-control" name="predikat[]" required>
                        @php
                        $nilai = $nilaiaspek ? $nilaiaspek->nilai->where('development_aspect_id',$aspeks->id)->first() : null;
                        @endphp
                        @if($rpd[$key] != NULL)
                        <option value="">== Pilih ==</option>
                        @foreach ($rpd[$key] as $rpds)
                        <option value="{{$rpds->id}}" {{ $nilai && $rpds->id == $nilai->aspect_description_id ? 'selected' : '' }}>{{$rpds->predicate}}</option>
                        @endforeach
                        @else
                        <option value="">Data Kosong</option>
                        @endif
                    </select>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td class="text-center" colspan="2">Belum ada data</td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
=======
<form action="{{route('nilaiaspek.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa_id}}">

    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th class="text-center">Aspek Perkembangan</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @if(count($aspek) > 0)
            @foreach($aspek as $key => $aspeks)
            <tr>
                <td width="30%">
                    <input type="hidden" name="aspek_id[]" value="{{$aspeks->id}}" required>
                    <input type="text" class="form-control" value="{{$aspeks->dev_aspect}}" readonly>
                </td>
                <td width="70%">
                    <select class="form-control" name="predikat[]" required>
                        @php
                        $nilai = $nilaiaspek ? $nilaiaspek->nilai->where('development_aspect_id',$aspeks->id)->first() : null;
                        @endphp
                        @if($rpd[$key] != NULL)
                        <option value="">== Pilih ==</option>
                        @foreach ($rpd[$key] as $rpds)
                        <option value="{{$rpds->id}}" {{ $nilai && $rpds->id == $nilai->aspect_description_id ? 'selected' : '' }}>{{$rpds->predicate}}</option>
                        @endforeach
                        @else
                        <option value="">Data Kosong</option>
                        @endif
                    </select>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td class="text-center" colspan="2">Belum ada data</td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</form>
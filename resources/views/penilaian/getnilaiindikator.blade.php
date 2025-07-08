<form action="{{route('nilaiindikator.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa_id}}">

    <table class="table align-items-center table-flush">
        @foreach ($aspek as $key => $aspeks)
        <thead class="bg-brand-green text-white">
            <tr>
                <th colspan="2" class="text-center">Aspek Perkembangan {{$aspeks->dev_aspect}}</th>
            </tr>
        </thead>
        <thead class="bg-secondary text-white">
            <tr>
                <th class="text-center">Indikator Perkembangan</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @if($indikator[$key] != NULL)
            @foreach($indikator[$key] as $indikators)
            <?php
            if ($nilaiindikator) {
                $detailindikator = App\Models\Penilaian\NilaiPASTK::where([['aspect_indicator_id', $indikators->id], ['report_final_kg_id', $nilaiindikator->id]])->first();
            }
            ?>
            <tr>
                <td width="60%">
                    <input type="hidden" name="indikator_id[]" value="{{$indikators->id}}" required>
                    <input type="text" class="form-control" value="{{$indikators->indicator}}" readonly>
                </td>
                <td width="40%">
                    <select class="form-control" name="predikat[]" required>
                        @if($rpd->isEmpty())
                        <option value="">Data Kosong</option>
                        @else
                        <option value="">== Pilih ==</option>
                        @foreach ($rpd as $rpds)
                        <option value="{{$rpds->id}}" <?php if (isset($detailindikator) && $detailindikator->indicator_description_id == $rpds->id) echo "selected"; ?>>{{$rpds->predicate}}</option>
                        @endforeach
                        @endif
                    </select>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="2" class="text-center">Indikator Aspek Belum Diisi</td>
            </tr>
            @endif
        </tbody>
        @endforeach
    </table>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
</form>
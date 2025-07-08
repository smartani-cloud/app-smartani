@if ($jumlahkd && $rpd == 4)
<form action="{{route('nilaiketerampilan.simpan')}}" method="POST">
    <input type="hidden" name="mapel_id" id="idmapel" />
    <input type="hidden" name="class_id" id="idkelas" />
    @csrf
    <hr>
    <div class="table-responsive">
        <table class="table align-items-center table-sm table-fixed" style="width:100%">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th>Nama</th>
                    <?php
                    $jumkd = $jumlahkd->kd;
                    for ($i = 1; $i <= $jumkd; $i++) {
                    ?>
                        <th class="text-center">NH{{$i}}</th>
                    <?php } ?>
                    <th class="text-center">Nilai Akhir</th>
                </tr>
            </thead>
            <input type="hidden" name="jumlahkd" value="{{$jumlahkd->kd}}">
            <?php
            for ($x = 1; $x <= $jumlahkd->kd; $x++) {
            ?>
                <input type="hidden" name="fieldkd[]" value="{{'kd'.$x}}">
            <?php } ?>
            <tbody>
                @foreach ($siswa as $key => $siswas)
                <tr>
                    <td><label style="width: 150px;">{{$siswas->identitas->student_name}}</label></td>
                    <?php
                    $jumkd = $jumlahkd->kd;
                    for ($i = 1; $i <= $jumkd; $i++) {
                        $x = $i - 1;
                    ?>
                        <td>
                            <input type="number" min="0" max="100" name="kd{{$i}}[]" <?php if ($nilaiketerampilan[$key] && isset($nilaiketerampilan[$key]->nilaiketerampilandetail[$x])) echo 'value="' . $nilaiketerampilan[$key]->nilaiketerampilandetail[$x]->score . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaiketerampilan[$key] && $nilaiketerampilan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                        </td>
                    <?php } ?>
                    <td>
                        <input type="number" min="0" max="100" name="na[]" <?php if ($nilaiketerampilan[$key]) echo 'value="' . number_format((float)$nilaiketerampilan[$key]->mean, 0, ',', '') . '"'; ?> class="form-control" style="width: 70px;" readonly>
                    </td>
                    <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}">
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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
@elseif(!$jumlahkd && $rpd < 4)
<hr>
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH dan Predikat Keterampilan Belum Diatur!</td>
        </tr>
    </thead>
</table>
@elseif(!$jumlahkd && $rpd == 4)
<hr>
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH Belum Diatur!</td>
        </tr>
    </thead>
</table>
@elseif($jumlahkd && $rpd < 4)
<hr>
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Predikat Keterampilan Belum Diatur!</td>
        </tr>
    </thead>
</table>
@else
<hr>
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Ups, terjadi error!</td>
        </tr>
    </thead>
</table>
@endif
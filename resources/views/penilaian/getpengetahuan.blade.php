<<<<<<< HEAD
<hr>
@if ($jumlahkd && $rpd == 4)
<form action="{{route('nilaipengetahuan.simpan')}}" method="POST">
    <input type="hidden" name="mapel_id" id="idmapel" />
    <input type="hidden" name="class_id" id="idkelas" />
    @if (isset($semester_id))
    <input type="hidden" name="semester_id" value="{{$semester_id}}" />
    <input type="hidden" name="kepsek" value="1" />
    @endif
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Presentase Penilaian Harian, PTS, PAS, Project</label>
                <div class="row">
                    <div class="col-md-3">
                        <input type="number" name="persenkd" placeholder="Penilaian Harian" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_kd . '"'; ?> class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="persenpts" placeholder="PTS" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_pts . '"'; ?> class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="persenpas" placeholder="PAS" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_pas . '"'; ?> class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="persenproject" placeholder="Project" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_project . '"'; ?> class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table class="table align-items-center table-sm" style="width:100%">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th>Nama</th>
                    <?php
                    $jumkd = $jumlahkd->kd;
                    for ($i = 1; $i <= $jumkd; $i++) {
                    ?>
                        <th class="text-center">NH{{$i}}</th>
                    <?php } ?>
                    <th class="text-center">PTS</th>
                    <th class="text-center">PAS</th>
                    <th class="text-center">Project</th>
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
                            <input type="number" min="0" max="100" name="kd{{$i}}[]" <?php if ($nilaipengetahuan[$key] && isset($nilaipengetahuan[$key]->nilaipengetahuandetail[$x])) echo 'value="' . $nilaipengetahuan[$key]->nilaipengetahuandetail[$x]->score . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                        </td>
                    <?php } ?>
                    <td>
                        <input type="number" min="0" max="100" name="pts[]" <?php if ($nilaipengetahuan[$key]) echo 'value="' . $nilaipengetahuan[$key]->pts . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                    </td>
                    <td>
                        <input type="number" min="0" max="100" name="pas[]" <?php if ($nilaipengetahuan[$key]) echo  'value="' . $nilaipengetahuan[$key]->pas . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                    </td>
                    <td>
                        <input type="number" min="0" max="100" name="project[]" <?php if ($nilaipengetahuan[$key]) echo  'value="' . $nilaipengetahuan[$key]->project . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                        <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}">
                    </td>
                    <td>
                        @php
                        $na = $nilaipengetahuan[$key] ? number_format((float)$nilaipengetahuan[$key]->score_knowledge, 0, ',', '') : 0;
                        @endphp
                        <input type="number" min="0" max="100" name="na[]" value="{{ $na }}" class="form-control {{ $kkm && ($na < $kkm->kkm) ? 'text-danger' : '' }}" style="width: 70px;" readonly>
                    </td>
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
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH dan Predikat Pengetahuan Belum Diatur!</td>
        </tr>
    </thead>
</table>
@elseif(!$jumlahkd && $rpd == 4)
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH Belum Diatur!</td>
        </tr>
    </thead>
</table>
@elseif($jumlahkd && $rpd < 4)
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Predikat Pengetahuan Belum Diatur!</td>
        </tr>
    </thead>
</table>
@else
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Ups, terjadi error!</td>
        </tr>
    </thead>
</table>
=======
<hr>
@if ($jumlahkd && $rpd == 4)
<form action="{{route('nilaipengetahuan.simpan')}}" method="POST">
    <input type="hidden" name="mapel_id" id="idmapel" />
    <input type="hidden" name="class_id" id="idkelas" />
    @if (isset($semester_id))
    <input type="hidden" name="semester_id" value="{{$semester_id}}" />
    <input type="hidden" name="kepsek" value="1" />
    @endif
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Presentase Penilaian Harian, PTS, PAS, Project</label>
                <div class="row">
                    <div class="col-md-3">
                        <input type="number" name="persenkd" placeholder="Penilaian Harian" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_kd . '"'; ?> class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="persenpts" placeholder="PTS" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_pts . '"'; ?> class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="persenpas" placeholder="PAS" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_pas . '"'; ?> class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="persenproject" placeholder="Project" <?php if ($persentase != FALSE) echo 'value="' . $persentase->precentage_project . '"'; ?> class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table class="table align-items-center table-sm" style="width:100%">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th>Nama</th>
                    <?php
                    $jumkd = $jumlahkd->kd;
                    for ($i = 1; $i <= $jumkd; $i++) {
                    ?>
                        <th class="text-center">NH{{$i}}</th>
                    <?php } ?>
                    <th class="text-center">PTS</th>
                    <th class="text-center">PAS</th>
                    <th class="text-center">Project</th>
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
                            <input type="number" min="0" max="100" name="kd{{$i}}[]" <?php if ($nilaipengetahuan[$key] && isset($nilaipengetahuan[$key]->nilaipengetahuandetail[$x])) echo 'value="' . $nilaipengetahuan[$key]->nilaipengetahuandetail[$x]->score . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                        </td>
                    <?php } ?>
                    <td>
                        <input type="number" min="0" max="100" name="pts[]" <?php if ($nilaipengetahuan[$key]) echo 'value="' . $nilaipengetahuan[$key]->pts . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                    </td>
                    <td>
                        <input type="number" min="0" max="100" name="pas[]" <?php if ($nilaipengetahuan[$key]) echo  'value="' . $nilaipengetahuan[$key]->pas . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                    </td>
                    <td>
                        <input type="number" min="0" max="100" name="project[]" <?php if ($nilaipengetahuan[$key]) echo  'value="' . $nilaipengetahuan[$key]->project . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo "readonly"; ?>>
                        <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}">
                    </td>
                    <td>
                        @php
                        $na = $nilaipengetahuan[$key] ? number_format((float)$nilaipengetahuan[$key]->score_knowledge, 0, ',', '') : 0;
                        @endphp
                        <input type="number" min="0" max="100" name="na[]" value="{{ $na }}" class="form-control {{ $kkm && ($na < $kkm->kkm) ? 'text-danger' : '' }}" style="width: 70px;" readonly>
                    </td>
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
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH dan Predikat Pengetahuan Belum Diatur!</td>
        </tr>
    </thead>
</table>
@elseif(!$jumlahkd && $rpd == 4)
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH Belum Diatur!</td>
        </tr>
    </thead>
</table>
@elseif($jumlahkd && $rpd < 4)
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Predikat Pengetahuan Belum Diatur!</td>
        </tr>
    </thead>
</table>
@else
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Ups, terjadi error!</td>
        </tr>
    </thead>
</table>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endif
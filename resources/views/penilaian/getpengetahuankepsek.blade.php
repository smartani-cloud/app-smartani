<hr>
@if ($jumlahkd)
<form action="{{route('npkepsek.simpan')}}" method="POST">
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
                            <input type="number" name="kd{{$i}}[]" <?php if ($nilaipengetahuan[$key] && isset($nilaipengetahuan[$key]->nilaipengetahuandetail[$x])) echo 'value="' . $nilaipengetahuan[$key]->nilaipengetahuandetail[$x]->score . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo ""; ?>>
                        </td>
                    <?php } ?>
                    <td>
                        <input type="number" name="pts[]" <?php if ($nilaipengetahuan[$key]) echo 'value="' . $nilaipengetahuan[$key]->pts . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo ""; ?>>
                    </td>
                    <td>
                        <input type="number" name="pas[]" <?php if ($nilaipengetahuan[$key]) echo  'value="' . $nilaipengetahuan[$key]->pas . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo ""; ?>>
                    </td>
                    <td>
                        <input type="number" name="project[]" <?php if ($nilaipengetahuan[$key]) echo  'value="' . $nilaipengetahuan[$key]->project . '"'; ?> class="form-control" style="width: 70px;" <?php if ($nilaipengetahuan[$key] && $nilaipengetahuan[$key]->rapor->report_status_id == 1) echo ""; ?>>
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
    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4">
            <div class="input-group mt-4">
                <input type="password" name="pwedit" class="form-control" placeholder="Password Verifikasi" required />
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
    @endif
</form>
@else
<table class="table align-items-center table-sm" style="width:100%">
    <thead class="bg-danger text-white">
        <tr>
            <td class="text-center">Jumlah NH Belum Diatur!</td>
        </tr>
    </thead>
</table>
@endif

<script src="{{ asset('js/password-visibility.js') }}"></script>
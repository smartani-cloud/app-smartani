<form action="{{route('hadits.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa_id}}">
    <input type="hidden" name="class_id" id="idkelas">
    <div id="hadits">
        <table class="table align-items-center table-flush">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th class="text-center">Nama Hadits</th>
                    <th class="text-center">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @if ($hadits && !empty($hadits->nilai))
                <tr class="text-right">
                    <td colspan="2">
                        <button type="button" class="btn btn-sm btn-success" id="tambahhadits"><i class="fa fa-plus"></i> Tambah Hadits</button>
                    </td>
                </tr>
                @foreach ($hadits->nilai as $key => $haditss)
                <tr>
                    <td width="50%"><input type="text" class="form-control" name="hadits[]" value="{{$haditss->hadits_doa}}" placeholder="Nama Hadits" required></td>
                    <td width="50%">
                        <div class="row">
                            <div class="col-md-11">
                                <select class="form-control" name="predikathadits[]" required>
                                    <option value="">== Pilih ==</option>
                                    <option value="A" <?php if ($haditss->predicate == "A") echo 'selected'; ?>>A</option>
                                    <option value="B" <?php if ($haditss->predicate == "B") echo 'selected'; ?>>B</option>
                                    <option value="C" <?php if ($haditss->predicate == "C") echo 'selected'; ?>>C</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" id="hapushadits" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr class="text-right">
                    <td colspan="2">
                        <button type="button" class="btn btn-sm btn-success" id="tambahhadits"><i class="fa fa-plus"></i> Tambah Hadits</button>
                    </td>
                </tr>
                <tr>
                    <td width="50%"><input type="text" class="form-control" name="hadits[]" placeholder="Nama Hadits" required></td>
                    <td width="50%">
                        <select class="form-control" name="predikathadits[]" required>
                            <option value="">== Pilih ==</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <hr>
    <div id="doa">
        <table class="table align-items-center table-flush">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th class="text-center">Nama Doa</th>
                    <th class="text-center">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @if ($doa && !empty($doa->nilai))
                <tr class="text-right">
                    <td colspan="2">
                        <button type="button" class="btn btn-sm btn-success" id="tambahdoa"><i class="fa fa-plus"></i> Tambah Doa</button>
                    </td>
                </tr>
                @foreach ($doa->nilai as $key => $haditss)
                <tr>
                    <td width="50%"><input type="text" class="form-control" name="doa[]" value="{{$haditss->hadits_doa}}" placeholder="Nama Doa" required></td>
                    <td width="50%">
                        <div class="row">
                            <div class="col-md-11">
                                <select class="form-control" name="predikatdoa[]" required>
                                    <option value="">== Pilih ==</option>
                                    <option value="A" <?php if ($haditss->predicate == "A") echo 'selected'; ?>>A</option>
                                    <option value="B" <?php if ($haditss->predicate == "B") echo 'selected'; ?>>B</option>
                                    <option value="C" <?php if ($haditss->predicate == "C") echo 'selected'; ?>>C</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" id="hapusdoa" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr class="text-right">
                    <td colspan="2">
                        <button type="button" class="btn btn-sm btn-success" id="tambahdoa"><i class="fa fa-plus"></i> Tambah Doa</button>
                    </td>
                </tr>
                <tr>
                    <td width="50%"><input type="text" class="form-control" name="doa[]" placeholder="Nama Doa" required></td>
                    <td width="50%">
                        <select class="form-control" name="predikatdoa[]" required>
                            <option value="">== Pilih ==</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <hr>
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


<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 10;

        var x = 1;
        var i = 1;
        $("#tambahhadits").click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $("#hadits table tbody").append('<tr><td><input type="text" class="form-control" name="hadits[]" placeholder="Nama Hadits" required></td><td><div class="row"><div class="col-md-11"><select class="form-control" name="predikathadits[]" required><option value="">== Pilih ==</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></div><div class="col-md-1"><button type="button" id="hapushadits" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });

        $("#tambahdoa").click(function(e) {
            e.preventDefault();
            if (i < max_fields) {
                i++;
                $("#doa table tbody").append('<tr><td><input type="text" class="form-control" name="doa[]" placeholder="Nama Doa" required></td><td><div class="row"><div class="col-md-11"><select class="form-control" name="predikatdoa[]" required><option value="">== Pilih ==</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></div><div class="col-md-1"><button type="button" id="hapusdoa" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });


        $("#hadits table tbody").on("click", "#hapushadits", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        });

        $("#doa table tbody").on("click", "#hapusdoa", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        });
    });
</script>
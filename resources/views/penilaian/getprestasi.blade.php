<form action="{{route('prestasi.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa->id}}">

    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th class="text-center">Prestasi</th>
                <th class="text-center">Deskripsi / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if ($prestasi)
            @foreach ($prestasi as $key => $prestasis)
            @if ($key == 0)
            <tr class="text-right">
                <td colspan="2">
                    <button type="button" class="btn btn-sm btn-success" id="tambahprestasi"><i class="fa fa-plus"></i> Tambah prestasi</button>
                </td>
            </tr>
            <tr>
                <td width="50%"><input type="text" class="form-control" name="prestasi[]" value="{{$prestasis->achievement_name}}" placeholder="Prestasi" required></td>
                <td width="50%">
                    <div class="row">
                        <div class="col-md-11">
                            <input type="text" name="deskripsi[]" class="form-control" value="{{$prestasis->description}}" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="hapusprestasi" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </td>
            </tr>
            @else
            <tr>
                <td width="50%"><input type="text" class="form-control" name="prestasi[]" value="{{$prestasis->achievement_name}}" placeholder="Prestasi" required></td>
                <td width="50%">
                    <div class="row">
                        <div class="col-md-11">
                            <input type="text" name="deskripsi[]" class="form-control" value="{{$prestasis->description}}" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="hapusprestasi" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
            @else
            <tr class="text-right">
                <td colspan="2">
                    <button type="button" class="btn btn-sm btn-success" id="tambahprestasi"><i class="fa fa-plus"></i> Tambah Prestasi</button>
                </td>
            </tr>
            <tr>
                <td width="50%"><input type="text" class="form-control" name="prestasi[]" placeholder="Prestasi" required></td>
                <td width="50%">
                    <input type="text" class="form-control" name="deskripsi[]" placeholder="Deskripsi" required>
                </td>
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


<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 4;
        var wrapper = $("table tbody");
        var add_button = $("#tambahprestasi");

        var x = 1;
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $(wrapper).append('<tr><td><input type="text" class="form-control" name="prestasi[]" placeholder="Prestasi" required></td><td><div class="row"><div class="col-md-11"><input type="text" name="deskripsi[]" class="form-control" placeholder="Deskripsi" required></div><div class="col-md-1"><button type="button" id="hapusprestasi" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });

        $(wrapper).on("click", "#hapusprestasi", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        })
    });
</script>
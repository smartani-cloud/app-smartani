<<<<<<< HEAD
<?php $rpd2 = $rpd; ?>
<form action="{{route('ekstra.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa_id}}">

    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th class="text-center">Ekstra</th>
                <th class="text-center">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @if ($ekstra)
            @foreach ($ekstra as $key => $ekstras)
            <input type="hidden" name="ekstra_id[]" value="{{$ekstras->id}}">
            @if ($key == 0)
            <tr class="text-right">
                <td colspan="2">
                    <button type="button" class="btn btn-sm btn-success" id="tambahekstra"><i class="fa fa-plus"></i> Tambah Ekstra</button>
                </td>
            </tr>
            <tr>
                <td width="30%"><input type="text" class="form-control" name="ekstra[]" value="{{$ekstras->extra_name}}" placeholder="Ekstrakurikuler" required></td>
                <td width="70%">
                    <div class="row">
                        <div class="col-md-11">
                            <select class="form-control" name="deskripsi[]" required>
                                <option value="">== Pilih ==</option>
                                @foreach ($rpd as $rpds)
                                <option value="{{$rpds->id}}" <?php if ($rpds->id == $ekstras->rpd_id) echo "selected"; ?>>{{$rpds->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="hapusekstra" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </td>
            </tr>
            @else
            <tr>
                <td width="30%"><input type="text" class="form-control" name="ekstra[]" value="{{$ekstras->extra_name}}" placeholder="Ekstrakurikuler" required></td>
                <td width="70%">
                    <div class="row">
                        <div class="col-md-11">
                            <select class="form-control" name="deskripsi[]" required>
                                <option value="">== Pilih ==</option>
                                @foreach ($rpd as $rpds)
                                <option value="{{$rpds->id}}" <?php if ($rpds->id == $ekstras->rpd_id) echo "selected"; ?>>{{$rpds->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="hapusekstra" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
            @else
            <tr class="text-right">
                <td colspan="2">
                    <button type="button" class="btn btn-sm btn-success" id="tambahekstra"><i class="fa fa-plus"></i> Tambah Ekstra</button>
                </td>
            </tr>
            <tr>
                <td width="30%"><input type="text" class="form-control" name="ekstra[]" placeholder="Ekstrakurikuler" required></td>
                <td width="70%">
                    <select class="form-control" name="deskripsi[]" required>
                        <option value="">== Pilih ==</option>
                        @foreach ($rpd as $rpd)
                        <option value="{{$rpd->id}}">{{$rpd->description}}</option>
                        @endforeach
                    </select>
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
        var add_button = $("#tambahekstra");

        var x = 1;
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $(wrapper).append('<tr><td><input type="text" class="form-control" name="ekstra[]" placeholder="Ekstrakurikuler" required></td><td><div class="row"><div class="col-md-11"><select class="form-control" name="deskripsi[]" required><option value="">== Pilih ==</option>@foreach ($rpd2 as $rpd2)<option value="{{$rpd2->id}}">{{$rpd2->description}}</option>@endforeach</select></div><div class="col-md-1"><button type="button" id="hapusekstra" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });

        $(wrapper).on("click", "#hapusekstra", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        })
    });
=======
<?php $rpd2 = $rpd; ?>
<form action="{{route('ekstra.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa_id}}">

    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th class="text-center">Ekstra</th>
                <th class="text-center">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @if ($ekstra)
            @foreach ($ekstra as $key => $ekstras)
            <input type="hidden" name="ekstra_id[]" value="{{$ekstras->id}}">
            @if ($key == 0)
            <tr class="text-right">
                <td colspan="2">
                    <button type="button" class="btn btn-sm btn-success" id="tambahekstra"><i class="fa fa-plus"></i> Tambah Ekstra</button>
                </td>
            </tr>
            <tr>
                <td width="30%"><input type="text" class="form-control" name="ekstra[]" value="{{$ekstras->extra_name}}" placeholder="Ekstrakurikuler" required></td>
                <td width="70%">
                    <div class="row">
                        <div class="col-md-11">
                            <select class="form-control" name="deskripsi[]" required>
                                <option value="">== Pilih ==</option>
                                @foreach ($rpd as $rpds)
                                <option value="{{$rpds->id}}" <?php if ($rpds->id == $ekstras->rpd_id) echo "selected"; ?>>{{$rpds->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="hapusekstra" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </td>
            </tr>
            @else
            <tr>
                <td width="30%"><input type="text" class="form-control" name="ekstra[]" value="{{$ekstras->extra_name}}" placeholder="Ekstrakurikuler" required></td>
                <td width="70%">
                    <div class="row">
                        <div class="col-md-11">
                            <select class="form-control" name="deskripsi[]" required>
                                <option value="">== Pilih ==</option>
                                @foreach ($rpd as $rpds)
                                <option value="{{$rpds->id}}" <?php if ($rpds->id == $ekstras->rpd_id) echo "selected"; ?>>{{$rpds->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="hapusekstra" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
            @else
            <tr class="text-right">
                <td colspan="2">
                    <button type="button" class="btn btn-sm btn-success" id="tambahekstra"><i class="fa fa-plus"></i> Tambah Ekstra</button>
                </td>
            </tr>
            <tr>
                <td width="30%"><input type="text" class="form-control" name="ekstra[]" placeholder="Ekstrakurikuler" required></td>
                <td width="70%">
                    <select class="form-control" name="deskripsi[]" required>
                        <option value="">== Pilih ==</option>
                        @foreach ($rpd as $rpd)
                        <option value="{{$rpd->id}}">{{$rpd->description}}</option>
                        @endforeach
                    </select>
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
        var add_button = $("#tambahekstra");

        var x = 1;
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $(wrapper).append('<tr><td><input type="text" class="form-control" name="ekstra[]" placeholder="Ekstrakurikuler" required></td><td><div class="row"><div class="col-md-11"><select class="form-control" name="deskripsi[]" required><option value="">== Pilih ==</option>@foreach ($rpd2 as $rpd2)<option value="{{$rpd2->id}}">{{$rpd2->description}}</option>@endforeach</select></div><div class="col-md-1"><button type="button" id="hapusekstra" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });

        $(wrapper).on("click", "#hapusekstra", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        })
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>
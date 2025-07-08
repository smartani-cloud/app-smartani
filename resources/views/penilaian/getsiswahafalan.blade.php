<div class="form-group row">
    <label for="rombel" class="col-sm-3 control-label">Nama Siswa</label>
    <div class="col-sm-9">
        <select name="rombel_id" class="form-control" id="rombel" onchange="getHafalan(this.value)" style="width:100%;" tabindex="-1" aria-hidden="true">
            <option value="">== Pilih ==</option>
            @if ($siswa)
            @foreach ($siswa as $siswas)
            <option value="{{$siswas->id}}">{{$siswas->identitas->student_name}}</option>
            @endforeach
            @endif
        </select>
    </div>
</div>
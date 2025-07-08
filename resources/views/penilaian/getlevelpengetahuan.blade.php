<label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
<div class="col-sm-4">
    <select name="kelas" id="idlevel" onchange="getdeskripsi(this.value)" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
        <option value="">== Pilih Tingkat Kelas ==</option>
        @foreach ($level as $levels)
        <option value="{{$levels->id}}">{{$levels->level}}</option>
        @endforeach
    </select>
</div>
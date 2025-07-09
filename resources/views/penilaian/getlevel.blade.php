<<<<<<< HEAD
<div class="form-group row">
    <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
    <div class="col-sm-9">
        @if($kelas->isEmpty())
        <select class="form-control" name="kelas" required>
            <option value="">Data Kosong</option>
        </select>
        @else
        <select class="form-control" name="kelas" onchange="getsiswa(this.value)" id="kelas" required>
            <option value="">== Pilih ==</option>
            @foreach($kelas as $kelases)
            <option value="{{$kelases->id}}">{{$kelases->level->level.' '.$kelases->namakelases->class_name}}</option>
            @endforeach
        </select>
        @endif
    </div>
=======
<div class="form-group row">
    <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
    <div class="col-sm-9">
        @if($kelas->isEmpty())
        <select class="form-control" name="kelas" required>
            <option value="">Data Kosong</option>
        </select>
        @else
        <select class="form-control" name="kelas" onchange="getsiswa(this.value)" id="kelas" required>
            <option value="">== Pilih ==</option>
            @foreach($kelas as $kelases)
            <option value="{{$kelases->id}}">{{$kelases->level->level.' '.$kelases->namakelases->class_name}}</option>
            @endforeach
        </select>
        @endif
    </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</div>
<<<<<<< HEAD
<div class="form-group row">
    <label for="predikat" class="col-sm-12 control-label">Aspek Perkembangan</label>
    <div class="col-sm-12">
        <input type="hidden" name="id" value="{{$data->id}}">
        <select class="form-control" name="aspek_id" id="ubahaspek" readonly>
            <option value="">== Pilih ==</option>
            @foreach ($aspek as $aspeks)
            <option value="{{$aspeks->id}}" <?php if ($data->development_aspect_id == $aspeks->id) echo "selected"; ?>>{{$aspeks->dev_aspect}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="predikat" class="col-sm-12 control-label">Predikat</label>
    <div class="col-sm-12">
        <select class="form-control" name="predikat" id="ubahpredikat" readonly>
            <option value="">== Pilih ==</option>
            <option value="A" <?php if ($data->predicate == "A") echo "selected"; ?>>A</option>
            <option value="B" <?php if ($data->predicate == "B") echo "selected"; ?>>B</option>
            <option value="C" <?php if ($data->predicate == "C") echo "selected"; ?>>C</option>
            <option value="D" <?php if ($data->predicate == "D") echo "selected"; ?>>D</option>
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="predikat" class="col-sm-12 control-label">Deskripsi</label>
    <div class="col-sm-12">
        <textarea rows="10" name="deskripsi" class="ckeditor form-control" id="ubahdesc" required>{{$data->description}}</textarea>
    </div>
=======
<div class="form-group row">
    <label for="predikat" class="col-sm-12 control-label">Aspek Perkembangan</label>
    <div class="col-sm-12">
        <input type="hidden" name="id" value="{{$data->id}}">
        <select class="form-control" name="aspek_id" id="ubahaspek" readonly>
            <option value="">== Pilih ==</option>
            @foreach ($aspek as $aspeks)
            <option value="{{$aspeks->id}}" <?php if ($data->development_aspect_id == $aspeks->id) echo "selected"; ?>>{{$aspeks->dev_aspect}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="predikat" class="col-sm-12 control-label">Predikat</label>
    <div class="col-sm-12">
        <select class="form-control" name="predikat" id="ubahpredikat" readonly>
            <option value="">== Pilih ==</option>
            <option value="A" <?php if ($data->predicate == "A") echo "selected"; ?>>A</option>
            <option value="B" <?php if ($data->predicate == "B") echo "selected"; ?>>B</option>
            <option value="C" <?php if ($data->predicate == "C") echo "selected"; ?>>C</option>
            <option value="D" <?php if ($data->predicate == "D") echo "selected"; ?>>D</option>
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="predikat" class="col-sm-12 control-label">Deskripsi</label>
    <div class="col-sm-12">
        <textarea rows="10" name="deskripsi" class="ckeditor form-control" id="ubahdesc" required>{{$data->description}}</textarea>
    </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</div>
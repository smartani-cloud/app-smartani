<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Jumlah NH Nilai Pengetahuan</label>
            @if($kd1)
            <input type="number" maxlength="2" name="kd1" value="{{$kd1}}" class="form-control" required>
            @else
            <input type="number" maxlength="2" name="kd1" class="form-control" required>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Jumlah NH Nilai Keterampilan</label>
            @if($kd2)
            <input type="number" maxlength="2" name="kd2" value="{{$kd2}}" class="form-control" required>
            @else
            <input type="number" maxlength="2" name="kd2" class="form-control" required>
            @endif
        </div>
    </div>
</div>
<div class="text-center mt-4">
    <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
</div>
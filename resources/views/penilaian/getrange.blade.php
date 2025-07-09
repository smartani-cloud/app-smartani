<<<<<<< HEAD
<hr>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat A <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_a)
            <input type="number" min="0" max="100" step="0.01" name="range_a" value="{{$range_a}}" class="form-control">
            @else
            <input type="number" min="0" max="100" step="0.01" name="range_a" class="form-control">
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat B <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_b)
            <input type="number" min="0" max="100" step="0.01" name="range_b" value="{{$range_b}}" class="form-control">
            @else
            <input type="number" min="0" max="100" step="0.01" name="range_b" class="form-control">
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat C <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_c)
            <input type="number" min="0" max="100" step="0.01" name="range_c" value="{{$range_c}}" class="form-control">
            @else
            <input type="number" min="0" max="100" step="0.01" name="range_c" class="form-control">
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat D <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_d || $range_d == 0)
            <input type="number" maxlength="2" min="0" max="100" name="range_d" value="{{$range_d}}" class="form-control">
            @else
            <input type="number" maxlength="2" min="0" max="100" name="range_d" class="form-control">
            @endif
        </div>
    </div>
</div>
<div class="text-center mt-4">
    <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
=======
<hr>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat A <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_a)
            <input type="number" min="0" max="100" step="0.01" name="range_a" value="{{$range_a}}" class="form-control">
            @else
            <input type="number" min="0" max="100" step="0.01" name="range_a" class="form-control">
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat B <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_b)
            <input type="number" min="0" max="100" step="0.01" name="range_b" value="{{$range_b}}" class="form-control">
            @else
            <input type="number" min="0" max="100" step="0.01" name="range_b" class="form-control">
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat C <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_c)
            <input type="number" min="0" max="100" step="0.01" name="range_c" value="{{$range_c}}" class="form-control">
            @else
            <input type="number" min="0" max="100" step="0.01" name="range_c" class="form-control">
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Range Nilai Predikat D <span class="badge badge-warning">(Lebih dari sama dengan)</span></label>
            @if($range_d || $range_d == 0)
            <input type="number" maxlength="2" min="0" max="100" name="range_d" value="{{$range_d}}" class="form-control">
            @else
            <input type="number" maxlength="2" min="0" max="100" name="range_d" class="form-control">
            @endif
        </div>
    </div>
</div>
<div class="text-center mt-4">
    <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</div>
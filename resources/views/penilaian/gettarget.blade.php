<form action="{{route('targettahfidz.simpan')}}" method="POST">
    @csrf
    @if ($tahfidz)
    <input type="hidden" name="tahfidz_id" value="{{$tahfidz->id}}" />
    @endif
    <input type="hidden" name="idlevel" id="idlevelsubmit" />
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Target Tahfidz</label>
                <input type="text" maxlength="250" name="target" placeholder="Target Tahfidz" <?php if ($tahfidz) echo 'value="' . $tahfidz->target . '"'; ?> class="form-control" required>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
</form>
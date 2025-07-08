<form action="{{route('tilawah.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="class_id" id="idkelas" />
    <div class="table-responsive">
        <table class="table align-items-center table-sm" style="width:100%">
            <thead class="bg-brand-green text-white">
                <tr>
                    <th class="align-middle">Nama</th>
                    <th class="text-center align-middle">Nama Huruf &<br/>Tanda Baca</th>
                    <th class="text-center align-middle">Tajwid Dasar</th>
                    <th class="text-center align-middle">Tajwid Lengkap</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($x = 1; $x <= 3; $x++) {
                ?>
                    <input type="hidden" name="fieldep[]" value="{{'ep'.$x}}">
                <?php } ?>
                @if($siswa)
                @foreach ($siswa as $key => $siswas)
                <tr>
                    <td><label style="width: 150px;">{{$siswas->identitas->student_name}}</label></td>
                    <td>
                        <select name="ep1[]" class="form-control" required>
                            <option value="">== Pilih ==</option>
                            <option value="A" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[0]) && $tilawah[$key]->nilaitilawah[0]->predicate == "A")) echo 'selected'; ?>>A</option>
                            <option value="B" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[0]) && $tilawah[$key]->nilaitilawah[0]->predicate == "B")) echo 'selected'; ?>>B</option>
                            <option value="C" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[0]) && $tilawah[$key]->nilaitilawah[0]->predicate == "C")) echo 'selected'; ?>>C</option>
                            <option value="-" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[0]) && $tilawah[$key]->nilaitilawah[0]->predicate == "-")) echo 'selected'; ?>>-</option>
                        </select>
                    </td>
                    <td>
                        <select name="ep2[]" class="form-control" required>
                            <option value="">== Pilih ==</option>
                            <option value="A" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[1]) && $tilawah[$key]->nilaitilawah[1]->predicate == "A")) echo 'selected'; ?>>A</option>
                            <option value="B" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[1]) && $tilawah[$key]->nilaitilawah[1]->predicate == "B")) echo 'selected'; ?>>B</option>
                            <option value="C" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[1]) && $tilawah[$key]->nilaitilawah[1]->predicate == "C")) echo 'selected'; ?>>C</option>
                            <option value="-" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[1]) && $tilawah[$key]->nilaitilawah[1]->predicate == "-")) echo 'selected'; ?>>-</option>
                        </select>
                    </td>
                    <td>
                        <select name="ep3[]" class="form-control" required>
                            <option value="">== Pilih ==</option>
                            <option value="A" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[2]) && $tilawah[$key]->nilaitilawah[2]->predicate == "A")) echo 'selected'; ?>>A</option>
                            <option value="B" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[2]) && $tilawah[$key]->nilaitilawah[2]->predicate == "B")) echo 'selected'; ?>>B</option>
                            <option value="C" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[2]) && $tilawah[$key]->nilaitilawah[2]->predicate == "C")) echo 'selected'; ?>>C</option>
                            <option value="-" <?php if ($tilawah[$key] && (isset($tilawah[$key]->nilaitilawah[2]) && $tilawah[$key]->nilaitilawah[2]->predicate == "-")) echo 'selected'; ?>>-</option>
                        </select>
                        <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}">
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="text-center">Data Kosong</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($siswa)
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
    @endif

</form>
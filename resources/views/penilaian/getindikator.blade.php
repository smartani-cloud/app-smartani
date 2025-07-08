    <a class="m-0 float-right btn btn-brand-green-dark btn-sm mb-2" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th>Aspek Perkembangan</th>
                    <th>Indikator</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($indikator->isEmpty())
                <tr>
                    <td colspan="3" class="text-center">Data Kosong</td>
                </tr>
                @else
                @foreach ($indikator as $indikators)
                <tr>
                    <td>{{$indikators->aspek->dev_aspect}}</td>
                    <td>{{$indikators->indicator}}</td>
                    <td class="text-right">
                        <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $indikators->id; ?>, '<?php echo $indikators->indicator; ?>', '<?php echo $indikators->development_aspect_id; ?>')"><i class="fas fa-pen"></i></a>
                        &nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $indikators->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
<div class="row">
    <div class="col-md-12">
        <a class="m-0 float-right btn btn-brand-green-dark btn-sm mb-2" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table align-items-center table-flush">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Deskripsi</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            @if ($rpd)
            @foreach ($rpd as $rpd)
            <tr>
                <td>{{$no}}</td>
                <td>{{$rpd->description}}</td>
                <td class="text-right">
                    <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $rpd->id; ?>, '<?php echo $rpd->description; ?>')"><i class="fas fa-pen"></i></a>
                    &nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $rpd->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php $no++; ?>
            @endforeach
            @else
            <tr>
                <td colspan="3" class="text-center">Data Kosong</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
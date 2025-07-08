<div class="row">
    <div class="col-md-12">
        <a class="m-0 float-right btn btn-brand-green-dark btn-sm mb-2" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
    </div>
</div>
<table class="table align-items-center table-flush" id="tabelaspek">
    <thead class="thead-light">
        <tr>
            <th>Aspek Perkembangan</th>
            <th>Predikat</th>
            <th>Deskripsi</th>
            <th class="text-right">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($desc->isEmpty())
        <tr>
            <td colspan="3" class="text-center">Data Kosong</td>
        </tr>
        @else
        @foreach ($desc as $descs)
        <tr>
            <td>
                {{$descs->aspek->dev_aspect}}
            </td>
            <td>
                {{$descs->predicate}}
            </td>
            <td>
                {{$descs->description}}
            </td>
            <td class="text-right">
                <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $descs->id; ?>)"><i class="fas fa-pen"></i></a>
                &nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $descs->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
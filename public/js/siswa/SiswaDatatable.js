$(document).ready(function()
{
    
    $('#dataTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "sDom": 'lfrtip',
        buttons: [
            'excelHtml5',
        ],
        ajax: {
            url: '/kependidikan/kbm/siswa/test',
            dataSrc: 'data'
        },
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 1 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 2 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 3 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 4 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 8 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 9 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 12 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 13 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 14 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 15 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 16 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 17 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 18 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 19 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 20 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 21 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 22 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 23 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 24 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 25 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 26 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 27 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 28 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 29 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 30 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 31 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 32 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 33 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 34 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 35 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 36 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 37 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 38 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 39 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 40 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 41 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 42 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 43 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 44 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 45 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 46 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 47 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 48 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 49 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 50 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 51 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 52 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 53 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 54 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 55 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 56 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 57 ],
                "visible": false,
                "searchable": false
            },
        ],
        columns: [
            { "data": 'id' },
            { "data": 'no_pendaftaran' },
            { "data": 'program' },
            { "data": 'tanggal_daftar' },
            { "data": 'tahun_ajaran' },
            { "data": 'nipd' },
            { "data": 'nisn' },
            { "data": 'nama' },
            { "data": 'nama_panggilan' },
            { "data": 'tempat_lahir' },
            { "data": 'tanggal_lahir' },
            { "data": 'jenis_kelamin' },
            { "data": 'agama' },
            { "data": 'anak_ke' },
            { "data": 'status_anak' },
            { "data": 'alamat' },
            { "data": 'no' },
            { "data": 'rt' },
            { "data": 'rw' },
            { "data": 'wilayah' },
            { "data": 'nama_ayah' },
            { "data": 'nik_ayah' },
            { "data": 'hp_ayah' },
            { "data": 'email_ayah' },
            { "data": 'pekerjaan_ayah' },
            { "data": 'jabatan_ayah' },
            { "data": 'telp_kantor_ayah' },
            { "data": 'alamat_kantor_ayah' },
            { "data": 'gaji_ayah' },
            { "data": 'nama_ibu' },
            { "data": 'nik_ibu' },
            { "data": 'hp_ibu' },
            { "data": 'email_ibu' },
            { "data": 'pekerjaan_ibu' },
            { "data": 'jabatan_ibu' },
            { "data": 'telp_kantor_ibu' },
            { "data": 'alamat_kantor_ibu' },
            { "data": 'gaji_ibu' },
            { "data": 'nip_ortu' },
            { "data": 'alamat_ortu' },
            { "data": 'hp_alternatif' },
            { "data": 'nama_wali' },
            { "data": 'nik_wali' },
            { "data": 'hp_wali' },
            { "data": 'email_wali' },
            { "data": 'pekerjaan_wali' },
            { "data": 'jabatan_wali' },
            { "data": 'telp_kantor_wali' },
            { "data": 'alamat_kantor_wali' },
            { "data": 'gaji_wali' },
            { "data": 'alamat_wali' },
            { "data": 'asal_sekolah' },
            { "data": 'saudara_kandung' },
            { "data": 'nama_saudara' },
            { "data": 'info_dari' },
            { "data": 'info_dari_nama' },
            { "data": 'info_dari_posisi' },
            { "data": 'class_id' },
            {
                "mData": null,
                "bSortable": false,
                "mRender": function(data, type, full) {
                    return '<a class="btn btn-info btn-sm" href=#/' + full['id'] + '>' + 'Edit' + '</a>';
                  }
            }
        ]
    });
});
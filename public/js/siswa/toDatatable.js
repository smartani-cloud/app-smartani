function datatableFunc() {
    var mytable = $('#dataTable').DataTable({
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
    });
}
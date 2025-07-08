
<script type="text/javascript">
    $(document).ready(function(){
        filterData();

        $('#filter').click(function (){
            filterData();
        });

        // var today = new Date().toISOString().split('T')[0];
        // $('input[name="end_date"]')[0].setAttribute('max', today);
        // $('input[name="start_date"]')[0].setAttribute('max', $('input[name="end_date"]')[0].val());
        $('tr[class="clickable-row"]').click(function() {
            console.log('cuk')
            window.location = $(this).data("href");
        });
    });

    function filterData(){
        $('#datatable').DataTable().destroy();
        var uri = '{{url("/kependidikan/kbm/siswa/siswa-datatables")}}'
        $('#datatable').DataTable(
            {
                "processing": true,
                "serverSide": true,
                "ajax": uri,
            }
        );
    }
</script>
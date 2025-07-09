	<script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }],
                order: [[ 1, 'asc' ]]
            });
        });
    </script>

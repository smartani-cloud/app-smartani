	<script>
    $(document).ready(function () {		
		var table = $('#dataTable').DataTable({
            "language": {
                "decimal": ",",
                "thousands": "."
            }
        }); // ID From dataTable
      
		new $.fn.dataTable.Buttons( table, {
			buttons: [
				{ extend: 'excelHtml5', className: 'btn-success btn-sm mb-3', text: '<i class="fas fa-file-excel mr-2"></i>Ekspor' }
			]
		});
	 
		table.buttons(0, null).container().prependTo('#dataTable_wrapper');
	});
    </script>

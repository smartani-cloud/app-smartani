<<<<<<< HEAD
	<script>
    function datatablesExportable(columns,msgTop = null,msgBottom = null){
      var titleHtml = document.getElementsByTagName('title')[0].innerHTML.replace(/(\r\n|\n|\r)/gm," ");
      var cols = columns;
      var buttonCommon = {
        exportOptions: {
          format: {
            body: function ( data, row, column, node ) {
              return cols.includes(column) ?
                  data.replace( /[.]/g, '' ) :
                  data;
            }
          }
        }
      };
      var table = $('#dataTable').DataTable({
              "language": {
                  "decimal": ",",
                  "thousands": "."
              }
          }); // ID From dataTable
        
      new $.fn.dataTable.Buttons( table, {
        buttons: [
          $.extend( true, {}, buttonCommon, {
            extend: 'excelHtml5',
            title: titleHtml,
            messageTop: msgTop,
            messageBottom: msgBottom,
            className: 'btn-success btn-sm mb-3',
            text: '<i class="fas fa-file-excel mr-2"></i>Ekspor',
            exportOptions: {
              columns: 'th:not(.action-col)'
            }
          })
        ]
      });
     
      table.buttons(0, null).container().prependTo('#dataTable_wrapper');
    }
  </script>
=======
	<script>
    function datatablesExportable(columns,msgTop = null,msgBottom = null){
      var titleHtml = document.getElementsByTagName('title')[0].innerHTML.replace(/(\r\n|\n|\r)/gm," ");
      var cols = columns;
      var buttonCommon = {
        exportOptions: {
          format: {
            body: function ( data, row, column, node ) {
              return cols.includes(column) ?
                  data.replace( /[.]/g, '' ) :
                  data;
            }
          }
        }
      };
      var table = $('#dataTable').DataTable({
              "language": {
                  "decimal": ",",
                  "thousands": "."
              }
          }); // ID From dataTable
        
      new $.fn.dataTable.Buttons( table, {
        buttons: [
          $.extend( true, {}, buttonCommon, {
            extend: 'excelHtml5',
            title: titleHtml,
            messageTop: msgTop,
            messageBottom: msgBottom,
            className: 'btn-success btn-sm mb-3',
            text: '<i class="fas fa-file-excel mr-2"></i>Ekspor',
            exportOptions: {
              columns: 'th:not(.action-col)'
            }
          })
        ]
      });
     
      table.buttons(0, null).container().prependTo('#dataTable_wrapper');
    }
  </script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

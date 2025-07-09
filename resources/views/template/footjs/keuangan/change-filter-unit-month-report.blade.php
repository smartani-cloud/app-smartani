<script>
$(document).ready(function()
{
    $('#filter_submit').click(function(){
        getData();
    });
    getData();
})
</script>
<script>
function getData(){
    var year = $('#year').val();
    var month = $('#month').val();
    var unit_id = $('#unit_id').val();
    // var level_id = $('#level').val();

    console.log (
        year,
        month,
        unit_id,
        // level_id,
    );
    
    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            year : year,
            month : month,
            unit_id : unit_id,
            // level_id : level_id,
        },
        beforeSend  : function() {
            $('#filter_submit').prop('disabled',true);
            $('.table-responsive').hide();
            $('.table-load').show();
            $('#dataTable').DataTable().destroy();
            $('#tbody').empty();
        },
        complete    : function() {
        }, 
        success: function async(response){
            console.log(response);
            response[0].map((item, index) => {
                let row = null;
                if(item[4]){
                    row = '<tr>+'+
                            '<td>'+item[0]+'</td>'+
                            '<td>'+item[1]+'</td>'+
                            '<td>'+item[2]+'</td>'+
                            '<td>'+item[3]+'</td>'+
                            '<td>'+item[4]+'</td>'+
                        '</tr>';
                    }
                else{
                    row = '<tr>+'+
                        '<td>'+item[0]+'</td>'+
                        '<td>'+item[1]+'</td>'+
                        '<td>'+item[2]+'</td>'+
                        '<td>'+item[3]+'</td>'+
                    '</tr>';
                }
                $('#tbody').append(row);
            });
            if(!$.fn.dataTable.isDataTable('#dataTable')){
                datatablesExportable([3],null,'Diekspor per '+getTodayDate());
            }
            $('.table-load').hide();
            $('.table-responsive').show();
            $('#filter_submit').prop('disabled',false);
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
</script>
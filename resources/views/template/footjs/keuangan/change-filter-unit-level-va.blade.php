<<<<<<< HEAD
<script>
$(document).ready(function()
{
    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());
    $('#filter_submit').click(function(){
        getData();
    });
    getData();
});

function getData(){
    var unit_id = $('#unit_id').val();
    var level_id = $('#level').val();

    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            unit_id : unit_id,
            level_id : level_id,
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
                let row = '<tr>+'+
                        '<td>'+item[0]+'</td>'+
                        '<td>'+item[1]+'</td>'+
                        '<td>'+item[2]+'</td>'+
                        '<td>'+item[3]+'</td>'+
                        '<td>'+item[4]+'</td>'+
                    '</tr>';
                $('#tbody').append(row);
            });
            //Require dataTables-button.js
            initDatatablesButton();
            $('.table-load').hide();
            $('.table-responsive').show();
            $('#filter_submit').prop('disabled',false);
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
=======
<script>
$(document).ready(function()
{
    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());
    $('#filter_submit').click(function(){
        getData();
    });
    getData();
});

function getData(){
    var unit_id = $('#unit_id').val();
    var level_id = $('#level').val();

    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            unit_id : unit_id,
            level_id : level_id,
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
                let row = '<tr>+'+
                        '<td>'+item[0]+'</td>'+
                        '<td>'+item[1]+'</td>'+
                        '<td>'+item[2]+'</td>'+
                        '<td>'+item[3]+'</td>'+
                        '<td>'+item[4]+'</td>'+
                    '</tr>';
                $('#tbody').append(row);
            });
            //Require dataTables-button.js
            initDatatablesButton();
            $('.table-load').hide();
            $('.table-responsive').show();
            $('#filter_submit').prop('disabled',false);
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>
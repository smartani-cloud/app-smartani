$(document).ready(function()
{
    console.log('Load data guru');
    jQuery('select[id="mapel_dipilih"]').on('change',function(){
        $('select[id="guru"]').prop("disabled", true);
        console.log('Mapel berubah');
        var mapel_id = jQuery(this).val();
        if(mapel_id)
        {
            jQuery.ajax({
                url : '/guru/'+mapel_id,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('option[id="guru"]').remove();
                    $('select[id="guru"]').append('<option value="" id="guru"></option>');
                    jQuery.each(data,function(key,value){
                        $('select[id="guru"]').append('<option value="'+key+'" id="guru">'+value+'</option>');
                    });
                }
            });
            $('select[id="guru"]').prop("disabled", false);
        }
        else
        {
            jQuery.ajax({
                url : '/guru',
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('option[id="guru"]').remove();
                    $('select[id="guru"]').append('<option value="" id="guru"></option>');
                    jQuery.each(data,function(key,value){
                        $('select[id="guru"]').append('<option value="'+key+'" id="guru">'+value+'</option>');
                    });
                }
            });
            $('select[id="guru"]').prop("disabled", false);
        }
    });

});
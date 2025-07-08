$(document).ready(function()
{
    console.log('Load data posisi');
    $('select[name="unit"]').on('change',function(){
        console.log('Ubah unit');
        var unitId = $(this).val();
        if(unitId)
        {
            $('select[name="position"]').prop("disabled", true);
            $('select[name="position"]').children("option:first-child").html("Memuat...");
            $.ajax({
                url : '/unit/jabatan/'+unitId,
                type : "GET",
                dataType : "json",
                success:function(data)
                {

                    $('select[name="position"]').prop("disabled", false);
                    $('select[name="position"]').children("option").not(':first').remove();
                    $('select[name="position"]').children("option:first-child").html("Pilih salah satu");
                    $.each(data,function(key,value){
                        $('select[name="position"]').append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('select[name="position"]').children("option").not(':first').remove();
        }
    });
});
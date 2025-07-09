<<<<<<< HEAD
$(document).ready(function()
{
    console.log('Load data wilayah');
    $('select[name="kecamatan"]').on('change',function(){
        console.log('Kecamatan diganti');
        var kecamatanId = $(this).val();
        if(kecamatanId)
        {
            $('select[name="desa"]').prop("disabled", true);
            $('select[name="desa"]').children("option:first-child").prop("selected", true);
            var pilih = $('select[name="desa"]').children("option:first-child").html();
            $('select[name="desa"]').children("option:first-child").html("Memuat...");
            $.ajax({
                url : '/wilayah/desa/'+kecamatanId,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('select[name="desa"]').prop("disabled", false);
                    $('select[name="desa"]').children("option").not(':first').remove();
                    $('select[name="desa"]').children("option:first-child").html(pilih);
                    $.each(data,function(key,value){
                        $('select[name="desa"]').append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('select[name="desa"]').children("option").not(':first').remove();
            $('select[name="desa"]').prop("disabled", true);
        }
    });
=======
$(document).ready(function()
{
    console.log('Load data wilayah');
    $('select[name="kecamatan"]').on('change',function(){
        console.log('Kecamatan diganti');
        var kecamatanId = $(this).val();
        if(kecamatanId)
        {
            $('select[name="desa"]').prop("disabled", true);
            $('select[name="desa"]').children("option:first-child").prop("selected", true);
            var pilih = $('select[name="desa"]').children("option:first-child").html();
            $('select[name="desa"]').children("option:first-child").html("Memuat...");
            $.ajax({
                url : '/wilayah/desa/'+kecamatanId,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('select[name="desa"]').prop("disabled", false);
                    $('select[name="desa"]').children("option").not(':first').remove();
                    $('select[name="desa"]').children("option:first-child").html(pilih);
                    $.each(data,function(key,value){
                        $('select[name="desa"]').append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('select[name="desa"]').children("option").not(':first').remove();
            $('select[name="desa"]').prop("disabled", true);
        }
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
});
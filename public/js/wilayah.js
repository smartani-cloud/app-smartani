$(document).ready(function()
{
    console.log('Load data wilayah');
    $('select[name="provinsi"]').on('change',function(){
        console.log('Provinsi diganti');
        var provinsiId = $(this).val();
        if(provinsiId)
        {
            $('select[name="kabupaten"]').prop("disabled", true);
            $('select[name="kecamatan"]').prop("disabled", true);
            $('select[name="desa"]').prop("disabled", true);
            $('select[name="kabupaten"]').children("option:first-child").prop("selected", true);
            $('select[name="kecamatan"]').children("option:first-child").prop("selected", true);
            $('select[name="desa"]').children("option:first-child").prop("selected", true);
            var pilih = $('select[name="kabupaten"]').children("option:first-child").html();
            $('select[name="kabupaten"]').children("option:first-child").html("Memuat...");
            $.ajax({
                url : '/wilayah/kabupaten/'+provinsiId,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('select[name="kabupaten"]').prop("disabled", false);
                    $('select[name="kabupaten"]').children("option").not(':first').remove();
                    $('select[name="kecamatan"]').children("option").not(':first').remove();
                    $('select[name="desa"]').children("option").not(':first').remove();
                    $('select[name="kabupaten"]').children("option:first-child").html(pilih);
                    $.each(data,function(key,value){
                        $('select[name="kabupaten"]').append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('select[name="kabupaten"]').children("option").not(':first').remove();
            $('select[name="kecamatan"]').children("option").not(':first').remove();
            $('select[name="desa"]').children("option").not(':first').remove();
            $('select[name="kabupaten"]').prop("disabled", true);
            $('select[name="kecamatan"]').prop("disabled", true);
            $('select[name="desa"]').prop("disabled", true);
        }
    });

    $('select[name="kabupaten"]').on('change',function(){
        console.log('Kabupaten diganti');
        var kabupatenId = $(this).val();
        if(kabupatenId)
        {
            $('select[name="kecamatan"]').prop("disabled", true);
            $('select[name="desa"]').prop("disabled", true);
            $('select[name="kecamatan"]').children("option:first-child").prop("selected", true);
            $('select[name="desa"]').children("option:first-child").prop("selected", true);
            var pilih = $('select[name="kecamatan"]').children("option:first-child").html();
            $('select[name="kecamatan"]').children("option:first-child").html("Memuat...");
            $.ajax({
                url : '/wilayah/kecamatan/'+kabupatenId,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('select[name="kecamatan"]').prop("disabled", false);
                    $('select[name="kecamatan"]').children("option").not(':first').remove();
                    $('select[name="desa"]').children("option").not(':first').remove();
                    $('select[name="kecamatan"]').children("option:first-child").html(pilih);
                    $.each(data,function(key,value){
                        $('select[name="kecamatan"]').append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('select[name="kecamatan"]').children("option").not(':first').remove();
            $('select[name="desa"]').children("option").not(':first').remove();
            $('select[name="kecamatan"]').prop("disabled", true);
            $('select[name="desa"]').prop("disabled", true);
        }
    });

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
});
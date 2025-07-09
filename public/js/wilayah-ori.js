$(document).ready(function()
{
    console.log('Load data Wilayah');
    jQuery('select[name="provinsi"]').on('change',function(){
        console.log('Provinsi Ganti');
        var provinsiID = jQuery(this).val();
        if(provinsiID)
        {
            jQuery.ajax({
                url : '/wilayah/kabupaten/'+provinsiID,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('option[id="kabupaten"]').remove();
                    $('option[id="kecamatan"]').remove();
                    $('option[id="desa"]').remove();
                    $('select[name="kecamatan"]').append('<option value="" id="kecamatan">== Pilih Kecamatan ==</option>');
                    $('select[name="kabupaten"]').append('<option value="" id="kabupaten">== Pilih Kabupaten/Kota ==</option>');
                    $('select[name="desa"]').append('<option value="" id="desa">== Pilih Desa/Kelurahan ==</option>');
                    jQuery.each(data,function(key,value){
                        $('select[name="kabupaten"]').append('<option value="'+key+'" id="kabupaten">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('option[id="kabupaten"]').remove();
            $('option[id="kecamatan"]').remove();
            $('option[id="desa"]').remove();
            $('select[name="kabupaten"]').append('<option value="" id="kabupaten">== Pilih Kabupaten/Kota ==</option>');
            $('select[name="kecamatan"]').append('<option value="" id="kecamatan">== Pilih Kecamatan ==</option>');
            $('select[name="desa"]').append('<option value="" id="desa">== Pilih Desa/Kelurahan ==</option>');
        }
    });
    jQuery('select[name="kabupaten"]').on('change',function(){
        console.log('Kabupaten Ganti');
        var kabupatenID = jQuery(this).val();
        if(kabupatenID)
        {
            jQuery.ajax({
                url : '/wilayah/kecamatan/'+kabupatenID,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('option[id="kecamatan"]').remove();
                    $('option[id="desa"]').remove();
                    $('select[name="kecamatan"]').append('<option value="" id="kecamatan">== Pilih Kecamatan ==</option>');
                    $('select[name="desa"]').append('<option value="" id="desa">== Pilih Desa/Kelurahan ==</option>');
                    jQuery.each(data,function(key,value){
                        $('select[name="kecamatan"]').append('<option value="'+key+'" id="kecamatan">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('option[id="kecamatan"]').remove();
            $('option[id="desa"]').remove();
            $('select[name="desa"]').append('<option value="" id="desa">== Pilih Desa/Kelurahan ==</option>');
            $('select[name="kecamatan"]').append('<option value="" id="kecamatan">== Pilih Kecamatan</option> ==');
        }
    });
    jQuery('select[name="kecamatan"]').on('change',function(){
        console.log('Kecamatan Ganti');
        var kecamatanID = jQuery(this).val();
        if(kecamatanID)
        {
            jQuery.ajax({
                url : '/wilayah/desa/'+kecamatanID,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    $('option[id="desa"]').remove();
                    $('select[name="desa"]').append('<option value="" id="desa">== Pilih Desa/Kelurahan ==</option>');
                    jQuery.each(data,function(key,value){
                        $('select[name="desa"]').append('<option value="'+key+'" id="desa">'+value+'</option>');
                    });
                }
            });
        }
        else
        {
            $('option[id="desa"]').remove();
            $('select[name="desa"]').append('<option value="" id="desa">== Pilih Desa/Kelurahan ==</option>');
        }
    });
});
<script>
function fetchCities(provinceId,cityId = null)
{
    if(provinceId)
    {
        $('select[name="city"]').prop("disabled", true);
        $('select[name="subdistrict"]').prop("disabled", true);
        $('select[name="village"]').prop("disabled", true);
        $('select[name="city"]').children("option:first-child").prop("selected", true);
        $('select[name="subdistrict"]').children("option:first-child").prop("selected", true);
        $('select[name="village"]').children("option:first-child").prop("selected", true);
        var selectCity = $('select[name="city"]').children("option:first-child").html();
        $('select[name="city"]').children("option:first-child").html("Memuat...");
        $.ajax({
            url: '{{ url('api/fetch-cities') }}',
            type: "POST",
            data: {
                province: provinceId,
                _token: $('meta[name=csrf-token]').attr('content')
            },
            dataType: "json",
            success:function(result)
            {
                $('select[name="city"]').prop("disabled", false);
                $('select[name="city"]').children("option").not(':first').remove();
                $('select[name="subdistrict"]').children("option").not(':first').remove();
                $('select[name="village"]').children("option").not(':first').remove();
                $('select[name="city"]').children("option:first-child").html(selectCity);
                $.each(result.cities,function(key,value){
                    $('select[name="city"]').append('<option value="'+value.code+'">'+value.name+'</option>');
                });
                if(cityId){
                    $('select[name="city"]').val(cityId);
                    @if(old('subdistrict'))
                    fetchSubdistricts(cityId,'{{ old('subdistrict') }}');
                    @else
                    fetchSubdistricts(cityId);
                    @endif
                }
            }
        });
    }
    else
    {
        $('select[name="city"]').children("option").not(':first').remove();
        $('select[name="subdistrict"]').children("option").not(':first').remove();
        $('select[name="village"]').children("option").not(':first').remove();
        $('select[name="city"]').prop("disabled", true);
        $('select[name="subdistrict"]').prop("disabled", true);
        $('select[name="village"]').prop("disabled", true);
    }
}
function fetchSubdistricts(cityId,subdistrictId = null)
{
    if(cityId)
    {
        $('select[name="subdistrict"]').prop("disabled", true);
        $('select[name="village"]').prop("disabled", true);
        $('select[name="subdistrict"]').children("option:first-child").prop("selected", true);
        $('select[name="village"]').children("option:first-child").prop("selected", true);
        var selectSubdistrict = $('select[name="subdistrict"]').children("option:first-child").html();
        $('select[name="subdistrict"]').children("option:first-child").html("Memuat...");
        $.ajax({
            url: '{{ url('api/fetch-subdistricts') }}',
            type: "POST",
            data: {
                city: cityId,
                _token: $('meta[name=csrf-token]').attr('content')
            },
            dataType: "json",
            success:function(result)
            {
                $('select[name="subdistrict"]').prop("disabled", false);
                $('select[name="subdistrict"]').children("option").not(':first').remove();
                $('select[name="village"]').children("option").not(':first').remove();
                $('select[name="subdistrict"]').children("option:first-child").html(selectSubdistrict);
                $.each(result.subdistricts,function(key,value){
                    $('select[name="subdistrict"]').append('<option value="'+value.code+'">'+value.name+'</option>');
                });
                if(subdistrictId){
                    $('select[name="subdistrict"]').val(subdistrictId);
                    @if(old('village'))
                    fetchVillages(subdistrictId,'{{ old('village') }}');
                    @else
                    fetchVillages(subdistrictId);
                    @endif
                }
            }
        });
    }
    else
    {
        $('select[name="subdistrict"]').children("option").not(':first').remove();
        $('select[name="village"]').children("option").not(':first').remove();
        $('select[name="subdistrict"]').prop("disabled", true);
        $('select[name="village"]').prop("disabled", true);
    }
}
function fetchVillages(subdistrictId,villageId = null)
{
    if(subdistrictId)
    {
        $('select[name="village"]').prop("disabled", true);
        $('select[name="village"]').children("option:first-child").prop("selected", true);
        var selectVillage = $('select[name="village"]').children("option:first-child").html();
        $('select[name="village"]').children("option:first-child").html("Memuat...");
        $.ajax({
            url: '{{ url('api/fetch-villages') }}',
            type: "POST",
            data: {
                subdistrict: subdistrictId,
                _token: $('meta[name=csrf-token]').attr('content')
            },
            dataType: "json",
            success:function(result)
            {
                $('select[name="village"]').prop("disabled", false);
                $('select[name="village"]').children("option").not(':first').remove();
                $('select[name="village"]').children("option:first-child").html(selectVillage);
                $.each(result.villages,function(key,value){
                    $('select[name="village"]').append('<option value="'+value.code+'">'+value.name+'</option>');
                });
                if(villageId){
                    $('select[name="village"]').val(villageId);
                }
            }
        });
    }
    else
    {
        $('select[name="village"]').children("option").not(':first').remove();
        $('select[name="village"]').prop("disabled", true);
    }
}
$(document).ready(function()
{
    @if(old('province'))
    @if(old('city'))
    fetchCities('{{ old('province') }}','{{ old('city') }}');
    @else
    fetchCities('{{ old('province') }}');
    @endif
    @endif
    console.log('Loading regions');
    $('select[name="province"]').on('change',function(){
        console.log('Province has been changed');
        fetchCities(this.value);
    });

    $('select[name="city"]').on('change',function(){
        console.log('City has been changed');
        fetchSubdistricts(this.value);
    });

    $('select[name="subdistrict"]').on('change',function(){
        console.log('Subdistrict has been changed');
        fetchVillages(this.value);
        
    });
});
</script>
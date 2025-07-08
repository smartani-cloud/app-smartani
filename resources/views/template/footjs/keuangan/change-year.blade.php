<script>
    $(document).ready(function () {
        if($('#yearOpt').val()){
            var originalHref = $('#btn-select-year').data('href');
            $('#btn-select-year').attr('href',originalHref+'/'+$('#yearOpt').val());
        }
        else{
            if($('#btn-select-year').hasClass("disabled") == false)
                $('#btn-select-year').addClass("disabled");
            if($('#btn-select-year').hasClass("btn-brand-purple")){
                $('#btn-select-year').removeClass("btn-brand-purple");
                $('#btn-select-year').addClass("btn-secondary");
            }
            $('#btn-select-year').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#yearOpt').on('change',function(){
            if($('#yearOpt').val()){
                var originalHref = $('#btn-select-year').data('href');
                $('#btn-select-year').removeClass("disabled");
                if($('#btn-select-year').hasClass("btn-secondary")){
                    $('#btn-select-year').removeClass("btn-secondary");
                    $('#btn-select-year').addClass("btn-brand-purple");
                }
                $('#btn-select-year').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-year').prop('disabled',false);
            }
            else{
                if($('#btn-select-year').hasClass("btn-brand-purple")){
                    $('#btn-select-year').removeClass("btn-brand-purple");
                    $('#btn-select-year').addClass("btn-secondary");
                }
                $('#btn-select-year').prop('aria-disabled',true);
            }
        });
    });
</script>

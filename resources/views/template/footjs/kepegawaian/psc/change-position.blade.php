<script>
    $(document).ready(function () {
        if($('#positionOpt').val()){
            var originalHref = $('#btn-select-position').data('href');
            $('#btn-select-position').attr('href',originalHref+'/'+$('#positionOpt').val());
        }
        else{
            if($('#btn-select-position').hasClass("disabled") == false)
                $('#btn-select-position').addClass("disabled");
            if($('#btn-select-position').hasClass("btn-brand-purple")){
                $('#btn-select-position').removeClass("btn-brand-purple");
                $('#btn-select-position').addClass("btn-secondary");
            }
            $('#btn-select-position').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#positionOpt').on('change',function(){
            if($('#positionOpt').val()){
                var originalHref = $('#btn-select-position').data('href');
                $('#btn-select-position').removeClass("disabled");
                if($('#btn-select-position').hasClass("btn-secondary")){
                    $('#btn-select-position').removeClass("btn-secondary");
                    $('#btn-select-position').addClass("btn-brand-purple");
                }
                $('#btn-select-position').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-position').prop('disabled',false);
            }
            else{
                if($('#btn-select-position').hasClass("btn-brand-purple")){
                    $('#btn-select-position').removeClass("btn-brand-purple");
                    $('#btn-select-position').addClass("btn-secondary");
                }
                $('#btn-select-position').prop('aria-disabled',true);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        if($('#classOpt').val()){
            var originalHref = $('#btn-select-class').data('href');
            $('#btn-select-class').attr('href',originalHref+'/'+$('#classOpt').val());
        }
        else{
            if($('#btn-select-class').hasClass("disabled") == false)
                $('#btn-select-class').addClass("disabled");
            if($('#btn-select-class').hasClass("btn-brand-purple")){
                $('#btn-select-class').removeClass("btn-brand-purple");
                $('#btn-select-class').addClass("btn-secondary");
            }
            $('#btn-select-class').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#classOpt').on('change',function(){
            if($('#classOpt').val()){
                var originalHref = $('#btn-select-class').data('href');
                $('#btn-select-class').removeClass("disabled");
                if($('#btn-select-class').hasClass("btn-secondary")){
                    $('#btn-select-class').removeClass("btn-secondary");
                    $('#btn-select-class').addClass("btn-brand-purple");
                }
                $('#btn-select-class').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-class').prop('disabled',false);
            }
            else{
                if($('#btn-select-class').hasClass("btn-brand-purple")){
                    $('#btn-select-class').removeClass("btn-brand-purple");
                    $('#btn-select-class').addClass("btn-secondary");
                }
                $('#btn-select-class').prop('aria-disabled',true);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        if($('#levelOpt').val()){
            var originalHref = $('#btn-select-level').data('href');
            $('#btn-select-level').attr('href',originalHref+'/'+$('#levelOpt').val());
        }
        else{
            if($('#btn-select-level').hasClass("disabled") == false)
                $('#btn-select-level').addClass("disabled");
            if($('#btn-select-level').hasClass("btn-brand-purple")){
                $('#btn-select-level').removeClass("btn-brand-purple");
                $('#btn-select-level').addClass("btn-secondary");
            }
            $('#btn-select-level').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#levelOpt').on('change',function(){
            if($('#levelOpt').val()){
                var originalHref = $('#btn-select-level').data('href');
                $('#btn-select-level').removeClass("disabled");
                if($('#btn-select-level').hasClass("btn-secondary")){
                    $('#btn-select-level').removeClass("btn-secondary");
                    $('#btn-select-level').addClass("btn-brand-purple");
                }
                $('#btn-select-level').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-level').prop('disabled',false);
            }
            else{
                if($('#btn-select-level').hasClass("btn-brand-purple")){
                    $('#btn-select-level').removeClass("btn-brand-purple");
                    $('#btn-select-level').addClass("btn-secondary");
                }
                $('#btn-select-level').prop('aria-disabled',true);
            }
        });
    });
</script>

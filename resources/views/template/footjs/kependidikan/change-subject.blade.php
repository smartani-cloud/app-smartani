<script>
    $(document).ready(function () {
        if($('#subjectOpt').val()){
            var originalHref = $('#btn-select-subject').data('href');
            $('#btn-select-subject').attr('href',originalHref+'/'+$('#subjectOpt').val());
        }
        else{
            if($('#btn-select-subject').hasClass("disabled") == false)
                $('#btn-select-subject').addClass("disabled");
            if($('#btn-select-subject').hasClass("btn-brand-purple")){
                $('#btn-select-subject').removeClass("btn-brand-purple");
                $('#btn-select-subject').addClass("btn-secondary");
            }
            $('#btn-select-subject').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#subjectOpt').on('change',function(){
            if($('#subjectOpt').val()){
                var originalHref = $('#btn-select-subject').data('href');
                $('#btn-select-subject').removeClass("disabled");
                if($('#btn-select-subject').hasClass("btn-secondary")){
                    $('#btn-select-subject').removeClass("btn-secondary");
                    $('#btn-select-subject').addClass("btn-brand-purple");
                }
                $('#btn-select-subject').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-subject').prop('disabled',false);
            }
            else{
                if($('#btn-select-subject').hasClass("btn-brand-purple")){
                    $('#btn-select-subject').removeClass("btn-brand-purple");
                    $('#btn-select-subject').addClass("btn-secondary");
                }
                $('#btn-select-subject').prop('aria-disabled',true);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        if($('#semesterOpt').val()){
            var originalHref = $('#btn-select-semester').data('href');
            $('#btn-select-semester').attr('href',originalHref+'&semester='+$('#semesterOpt').val());
        }
        else{
            if($('#btn-select-semester').hasClass("disabled") == false)
                $('#btn-select-semester').addClass("disabled");
            if($('#btn-select-semester').hasClass("btn-brand-purple")){
                $('#btn-select-semester').removeClass("btn-brand-purple");
                $('#btn-select-semester').addClass("btn-secondary");
            }
            $('#btn-select-semester').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    });
    $(function() {
        $('#semesterOpt').on('change',function(){
            if($('#semesterOpt').val()){
                var originalHref = $('#btn-select-semester').data('href');
                $('#btn-select-semester').removeClass("disabled");
                if($('#btn-select-semester').hasClass("btn-secondary")){
                    $('#btn-select-semester').removeClass("btn-secondary");
                    $('#btn-select-semester').addClass("btn-brand-purple");
                }
                $('#btn-select-semester').attr('href',originalHref+'&semester='+$(this).val());
                $('#btn-select-semester').prop('disabled',false);
            }
            else{
                if($('#btn-select-semester').hasClass("btn-brand-purple")){
                    $('#btn-select-semester').removeClass("btn-brand-purple");
                    $('#btn-select-semester').addClass("btn-secondary");
                }
                $('#btn-select-semester').prop('aria-disabled',true);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        if($('#unitOpt').val()){
            var originalHref = $('#btn-select-unit').data('href');
            $('#btn-select-unit').attr('href',originalHref+'/'+$('#unitOpt').val());
        }
        else{
            if($('#btn-select-unit').hasClass("disabled") == false)
                $('#btn-select-unit').addClass("disabled");
            if($('#btn-select-unit').hasClass("btn-brand-purple")){
                $('#btn-select-unit').removeClass("btn-brand-purple");
                $('#btn-select-unit').addClass("btn-secondary");
            }
            $('#btn-select-unit').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    });
    $(function() {
        $('#unitOpt').on('change',function(){
            if($('#unitOpt').val()){
                var originalHref = $('#btn-select-unit').data('href');
                $('#btn-select-unit').removeClass("disabled");
                if($('#btn-select-unit').hasClass("btn-secondary")){
                    $('#btn-select-unit').removeClass("btn-secondary");
                    $('#btn-select-unit').addClass("btn-brand-purple");
                }
                $('#btn-select-unit').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-unit').prop('disabled',false);
            }
            else{
                if($('#btn-select-unit').hasClass("btn-brand-purple")){
                    $('#btn-select-unit').removeClass("btn-brand-purple");
                    $('#btn-select-unit').addClass("btn-secondary");
                }
                $('#btn-select-unit').prop('aria-disabled',true);
            }
        });
    });
</script>

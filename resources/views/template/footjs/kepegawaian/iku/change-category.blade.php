<script>
    $(document).ready(function () {
        if($('#categoryOpt').val()){
            var originalHref = $('#btn-select-category').data('href');
            $('#btn-select-category').attr('href',originalHref+'/'+$('#categoryOpt').val());
        }
        else{
            if($('#btn-select-category').hasClass("disabled") == false)
                $('#btn-select-category').addClass("disabled");
            if($('#btn-select-category').hasClass("btn-brand-purple")){
                $('#btn-select-category').removeClass("btn-brand-purple");
                $('#btn-select-category').addClass("btn-secondary");
            }
            $('#btn-select-category').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    });
    $(function() {
        $('#categoryOpt').on('change',function(){
            if($('#categoryOpt').val()){
                var originalHref = $('#btn-select-category').data('href');
                $('#btn-select-category').removeClass("disabled");
                if($('#btn-select-category').hasClass("btn-secondary")){
                    $('#btn-select-category').removeClass("btn-secondary");
                    $('#btn-select-category').addClass("btn-brand-purple");
                }
                $('#btn-select-category').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-category').prop('disabled',false);
            }
            else{
                if($('#btn-select-category').hasClass("btn-brand-purple")){
                    $('#btn-select-category').removeClass("btn-brand-purple");
                    $('#btn-select-category').addClass("btn-secondary");
                }
                $('#btn-select-category').prop('aria-disabled',true);
            }
        });
    });
</script>

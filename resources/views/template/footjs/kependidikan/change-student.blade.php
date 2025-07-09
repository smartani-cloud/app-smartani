<<<<<<< HEAD
<script>
    $(document).ready(function () {
        if($('#studentOpt').val()){
            var originalHref = $('#btn-select-student').data('href');
            $('#btn-select-student').attr('href',originalHref+'/'+$('#studentOpt').val());
        }
        else{
            if($('#btn-select-student').hasClass("disabled") == false)
                $('#btn-select-student').addClass("disabled");
            if($('#btn-select-student').hasClass("btn-brand-purple")){
                $('#btn-select-student').removeClass("btn-brand-purple");
                $('#btn-select-student').addClass("btn-secondary");
            }
            $('#btn-select-student').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#studentOpt').on('change',function(){
            if($('#studentOpt').val()){
                var originalHref = $('#btn-select-student').data('href');
                $('#btn-select-student').removeClass("disabled");
                if($('#btn-select-student').hasClass("btn-secondary")){
                    $('#btn-select-student').removeClass("btn-secondary");
                    $('#btn-select-student').addClass("btn-brand-purple");
                }
                $('#btn-select-student').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-student').prop('disabled',false);
            }
            else{
                if($('#btn-select-student').hasClass("btn-brand-purple")){
                    $('#btn-select-student').removeClass("btn-brand-purple");
                    $('#btn-select-student').addClass("btn-secondary");
                }
                $('#btn-select-student').prop('aria-disabled',true);
            }
        });
    });
</script>
=======
<script>
    $(document).ready(function () {
        if($('#studentOpt').val()){
            var originalHref = $('#btn-select-student').data('href');
            $('#btn-select-student').attr('href',originalHref+'/'+$('#studentOpt').val());
        }
        else{
            if($('#btn-select-student').hasClass("disabled") == false)
                $('#btn-select-student').addClass("disabled");
            if($('#btn-select-student').hasClass("btn-brand-purple")){
                $('#btn-select-student').removeClass("btn-brand-purple");
                $('#btn-select-student').addClass("btn-secondary");
            }
            $('#btn-select-student').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    
    });
    $(function() {
        $('#studentOpt').on('change',function(){
            if($('#studentOpt').val()){
                var originalHref = $('#btn-select-student').data('href');
                $('#btn-select-student').removeClass("disabled");
                if($('#btn-select-student').hasClass("btn-secondary")){
                    $('#btn-select-student').removeClass("btn-secondary");
                    $('#btn-select-student').addClass("btn-brand-purple");
                }
                $('#btn-select-student').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-student').prop('disabled',false);
            }
            else{
                if($('#btn-select-student').hasClass("btn-brand-purple")){
                    $('#btn-select-student').removeClass("btn-brand-purple");
                    $('#btn-select-student').addClass("btn-secondary");
                }
                $('#btn-select-student').prop('aria-disabled',true);
            }
        });
    });
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

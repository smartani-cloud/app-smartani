<script>
    $(document).ready(function () {
        if($('#chartOpt').val()){
            var originalHref = $('#btn-select-chart').data('href');
            $('#btn-select-chart').attr('href',originalHref+'/?chart='+$('#chartOpt').val());
        }
        else{
            if($('#btn-select-chart').hasClass("disabled") == false)
                $('#btn-select-chart').addClass("disabled");
            if($('#btn-select-chart').hasClass("btn-brand-purple")){
                $('#btn-select-chart').removeClass("btn-brand-purple");
                $('#btn-select-chart').addClass("btn-secondary");
            }
            $('#btn-select-chart').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    });
    $(function() {
        $('#chartOpt').on('change',function(){
            if($('#chartOpt').val()){
                var originalHref = $('#btn-select-chart').data('href');
                $('#btn-select-chart').removeClass("disabled");
                if($('#btn-select-chart').hasClass("btn-secondary")){
                    $('#btn-select-chart').removeClass("btn-secondary");
                    $('#btn-select-chart').addClass("btn-brand-purple");
                }
                $('#btn-select-chart').attr('href',originalHref+'/?chart='+$(this).val());
                $('#btn-select-chart').prop('disabled',false);
            }
            else{
                if($('#btn-select-chart').hasClass("btn-brand-purple")){
                    $('#btn-select-chart').removeClass("btn-brand-purple");
                    $('#btn-select-chart').addClass("btn-secondary");
                }
                $('#btn-select-chart').prop('aria-disabled',true);
            }
        });
    });
</script>

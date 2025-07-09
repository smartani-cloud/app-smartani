<<<<<<< HEAD
<script>
    $(document).ready(function () {
        if($('#ledgerOpt').val()){
            var originalHref = $('#btn-select-ledger').data('href');
            $('#btn-select-ledger').attr('href',originalHref+'/'+$('#ledgerOpt').val());
        }
        else{
            if($('#btn-select-ledger').hasClass("disabled") == false)
                $('#btn-select-ledger').addClass("disabled");
            if($('#btn-select-ledger').hasClass("btn-brand-purple")){
                $('#btn-select-ledger').removeClass("btn-brand-purple");
                $('#btn-select-ledger').addClass("btn-secondary");
            }
            $('#btn-select-ledger').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    });
    $(function() {
        $('#ledgerOpt').on('change',function(){
            if($('#ledgerOpt').val()){
                var originalHref = $('#btn-select-ledger').data('href');
                $('#btn-select-ledger').removeClass("disabled");
                if($('#btn-select-ledger').hasClass("btn-secondary")){
                    $('#btn-select-ledger').removeClass("btn-secondary");
                    $('#btn-select-ledger').addClass("btn-brand-purple");
                }
                $('#btn-select-ledger').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-ledger').prop('disabled',false);
            }
            else{
                if($('#btn-select-ledger').hasClass("btn-brand-purple")){
                    $('#btn-select-ledger').removeClass("btn-brand-purple");
                    $('#btn-select-ledger').addClass("btn-secondary");
                }
                $('#btn-select-ledger').prop('aria-disabled',true);
            }
        });
    });
</script>
=======
<script>
    $(document).ready(function () {
        if($('#ledgerOpt').val()){
            var originalHref = $('#btn-select-ledger').data('href');
            $('#btn-select-ledger').attr('href',originalHref+'/'+$('#ledgerOpt').val());
        }
        else{
            if($('#btn-select-ledger').hasClass("disabled") == false)
                $('#btn-select-ledger').addClass("disabled");
            if($('#btn-select-ledger').hasClass("btn-brand-purple")){
                $('#btn-select-ledger').removeClass("btn-brand-purple");
                $('#btn-select-ledger').addClass("btn-secondary");
            }
            $('#btn-select-ledger').attr('href','javascript:void(0)').prop('aria-disabled',true);
        }
    });
    $(function() {
        $('#ledgerOpt').on('change',function(){
            if($('#ledgerOpt').val()){
                var originalHref = $('#btn-select-ledger').data('href');
                $('#btn-select-ledger').removeClass("disabled");
                if($('#btn-select-ledger').hasClass("btn-secondary")){
                    $('#btn-select-ledger').removeClass("btn-secondary");
                    $('#btn-select-ledger').addClass("btn-brand-purple");
                }
                $('#btn-select-ledger').attr('href',originalHref+'/'+$(this).val());
                $('#btn-select-ledger').prop('disabled',false);
            }
            else{
                if($('#btn-select-ledger').hasClass("btn-brand-purple")){
                    $('#btn-select-ledger').removeClass("btn-brand-purple");
                    $('#btn-select-ledger').addClass("btn-secondary");
                }
                $('#btn-select-ledger').prop('aria-disabled',true);
            }
        });
    });
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

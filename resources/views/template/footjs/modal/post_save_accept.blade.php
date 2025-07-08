<script>
    $(document).ready(function () {
        $('#saveAcceptBtn').click(function(){
			var form = $(this).data('form');
			$('input[name="validate"]').val('validate');
			$('#'+form).submit();
		});
    });
</script>

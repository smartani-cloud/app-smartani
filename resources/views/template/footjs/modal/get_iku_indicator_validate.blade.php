<script>
    function validateModal(name, validate_url)
    {
    	$('#validate-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#validate-confirm .name").text(name);
    	$('#validate-link').attr("action" , validate_url);
    }
</script>

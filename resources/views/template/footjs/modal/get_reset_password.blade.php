<script>
    function resetModal(name, reset_url)
    {
    	$('#reset-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#reset-confirm .name").text(name);
    	$('#reset-link').attr("action" , reset_url);
    }
</script>

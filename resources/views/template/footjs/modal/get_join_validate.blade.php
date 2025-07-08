<script>
    function joinModal(name, validate_url)
    {
    	$('#join-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#join-confirm .name").text(name);
    	$('#join-link').attr("action" , validate_url);
    }
	function disjoinModal(name, validate_url)
    {
    	$('#disjoin-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#disjoin-confirm .name").text(name);
    	$('#disjoin-link').attr("action" , validate_url);
    }
</script>

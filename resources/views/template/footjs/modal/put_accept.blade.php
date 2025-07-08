<script>
    function acceptModal(title, item, url)
    {
    	$('#accept-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#accept-confirm .title").text(title);
    	$("#accept-confirm .item").text(item);
    	$('#accept-link').attr("action" , url);
    }
</script>

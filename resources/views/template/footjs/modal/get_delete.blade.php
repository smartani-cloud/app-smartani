<script>
    function deleteModal(title, item, delete_url)
    {
    	$('#delete-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#delete-confirm .title").text(title);
    	$("#delete-confirm .item").text(item);
    	$('#delete-link').attr("action" , delete_url);
    }
</script>

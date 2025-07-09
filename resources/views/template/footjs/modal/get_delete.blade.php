<<<<<<< HEAD
<script>
    function deleteModal(title, item, delete_url)
    {
    	$('#delete-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#delete-confirm .title").text(title);
    	$("#delete-confirm .item").text(item);
    	$('#delete-link').attr("action" , delete_url);
    }
</script>
=======
<script>
    function deleteModal(title, item, delete_url)
    {
    	$('#delete-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#delete-confirm .title").text(title);
    	$("#delete-confirm .item").text(item);
    	$('#delete-link').attr("action" , delete_url);
    }
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

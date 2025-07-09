<<<<<<< HEAD
<script>
    function excludeModal(title, item, exclude_url)
    {
    	$('#exclude-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#exclude-confirm .title").text(title);
    	$("#exclude-confirm .item").text(item);
    	$('#exclude-link').attr("action" , exclude_url);
    }
</script>
=======
<script>
    function excludeModal(title, item, exclude_url)
    {
    	$('#exclude-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#exclude-confirm .title").text(title);
    	$("#exclude-confirm .item").text(item);
    	$('#exclude-link').attr("action" , exclude_url);
    }
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

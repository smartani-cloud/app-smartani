<<<<<<< HEAD
<script>
    function detailModal(route,id,modal = null) {
		var modalId = '#detailModal'; 
		if(modal) modalId = modal;
		
        $(modalId+' .modal-load').show();
        $(modalId+' .modal-body').hide();
            
        $.post(route,
        {
           '_token': $('meta[name=csrf-token]').attr('content'),
            id: id
        },
        function(response) {
            $(modalId+' .modal-body').html(response);
            $(modalId+' .modal-load').hide();
            $(modalId+' .modal-body').show();
        });
    }
</script>
=======
<script>
    function detailModal(route,id,modal = null) {
		var modalId = '#detailModal'; 
		if(modal) modalId = modal;
		
        $(modalId+' .modal-load').show();
        $(modalId+' .modal-body').hide();
            
        $.post(route,
        {
           '_token': $('meta[name=csrf-token]').attr('content'),
            id: id
        },
        function(response) {
            $(modalId+' .modal-body').html(response);
            $(modalId+' .modal-load').hide();
            $(modalId+' .modal-body').show();
        });
    }
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

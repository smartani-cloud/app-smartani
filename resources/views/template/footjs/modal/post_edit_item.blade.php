<<<<<<< HEAD
<script>
    function editModal(route,id) {
        $('#edit-form .modal-load').show();
        $('#edit-form .modal-body').hide();
            
        $.post(route,
        {
           '_token': $('meta[name=csrf-token]').attr('content'),
            id: id
        },
        function(response) {
            $('#edit-form .modal-body').html(response);
            $('#edit-form .modal-load').hide();
            $('#edit-form .modal-body').show();
        });
    }
</script>
=======
<script>
    function editModal(route,id) {
        $('#edit-form .modal-load').show();
        $('#edit-form .modal-body').hide();
            
        $.post(route,
        {
           '_token': $('meta[name=csrf-token]').attr('content'),
            id: id
        },
        function(response) {
            $('#edit-form .modal-body').html(response);
            $('#edit-form .modal-load').hide();
            $('#edit-form .modal-body').show();
        });
    }
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

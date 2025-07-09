<script>
    function editModal(route,id) {
        $('.modal-load').show();
        $('.modal-body').hide();
            
        $.post(route,
        {
           '_token': $('meta[name=csrf-token]').attr('content'),
            id: id
        },
        function(response) {
            $('#edit-form .modal-body').html(response);
            $('.modal-load').hide();
            $('.modal-body').show();
        });
    }
</script>

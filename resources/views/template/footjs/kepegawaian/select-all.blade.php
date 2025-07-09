<<<<<<< HEAD
	<script>
	$(function() {
        $('#wrapper').on("click",'.btn-select-all',function(e){
			var target = $(this).attr('data-target');
			$('#'+target).select2('destroy').find('option').prop('selected', 'selected').end().select2({
				theme: 'bootstrap4'
			});
		});
    });
    </script>
=======
	<script>
	$(function() {
        $('#wrapper').on("click",'.btn-select-all',function(e){
			var target = $(this).attr('data-target');
			$('#'+target).select2('destroy').find('option').prop('selected', 'selected').end().select2({
				theme: 'bootstrap4'
			});
		});
    });
    </script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

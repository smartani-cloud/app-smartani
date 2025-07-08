<script>
	$(function() {
        $('#ppaDetail').on("click",'.btn-edit',function(e){
			e.preventDefault();
			
			$('.modal-load').show();
			$('.modal-body').hide();
			
            $.fn.getParent = function(num){
                var last = this[0];
                for(var i = 0; i < num; i++){
                    last = last.parentNode;
                }
                return $(last);
            };

            var rowId = $(this).getParent(2).attr('id');
            var detailId = rowId.split("-")[1];
			
			var colBtn = $(this).getParent(1);
			var colAccount = colBtn.siblings(".detail-account");
			var colNote = colBtn.siblings(".detail-note");
			var colValue = colBtn.siblings(".detail-value");
			
			var dataAccount = colAccount.html();
			var dataNote = colNote.html();
			var dataValue = colValue.children('input[name="value-'+detailId+'"]').first().val();
			
			$('input[name="editId"]').val(rowId);
			$('input[name="editAccount"]').val(dataAccount);
			$('textarea[name="editNote"]').html(dataNote);
			$('input[name="editValue"]').val(dataValue);
			
            $('.modal-load').hide();
            $('.modal-body').show();
        });	
    });
</script>

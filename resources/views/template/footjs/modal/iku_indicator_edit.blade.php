<script>
	$(function() {
        $('#ikuIndicatorTable').on("click",'.btn-edit',function(e){
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
			var colAspect = colBtn.siblings(".detail-aspect");
			var colName = colBtn.siblings(".detail-name");
			var colObject = colBtn.siblings(".detail-object");
			var colMt = colBtn.siblings(".detail-mt");
			var colTarget = colBtn.siblings(".detail-target");
			
			var aspectId = colAspect.attr('data-aspect');
			var dataName = colName.html();
			var dataObject = colObject.html();
			var dataMt = colMt.html();
			var dataTarget = colTarget.html();
			
			$('input[name="editId"]').val(detailId);
			$('select[name="editAspect"]').val(aspectId);
			$('input[name="editName"]').val(dataName);
			$('input[name="editObject"]').val(dataObject);
			$('input[name="editMt"]').val(dataMt);
			$('input[name="editTarget"]').val(dataTarget);
			
            $('.modal-load').hide();
            $('.modal-body').show();
        });	
    });
</script>

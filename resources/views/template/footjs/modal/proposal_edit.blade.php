<script>
	$(function() {
        $('#proposalDetails').on("click",'.btn-edit',function(e){
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
			var colDesc = colBtn.siblings(".detail-desc");
			var colPrice = colBtn.siblings(".detail-price");
			var colQty = colBtn.siblings(".detail-qty");
			
			//alert(colDesc.children('input[name="desc-'+detailId+'"]').length);
			if(colDesc.children('input[name="desc-'+detailId+'"]').length){
				var dataDesc = colDesc.children('input[name="desc-'+detailId+'"]').first().val();
			}
			else{
				var dataDesc = colDesc.html();
			}
			var dataPrice = colPrice.children('input[name="price-'+detailId+'"]').first().val();
			var dataQty = colQty.children('input[name="qty-'+detailId+'"]').first().val();
			
			$('input[name="editId"]').val(rowId);
			$('input[name="editDesc"]').val(dataDesc);
			$('input[name="editPrice"]').val(dataPrice);
			$('input[name="editQty"]').val(dataQty);
			
            $('.modal-load').hide();
            $('.modal-body').show();
        });	
    });
</script>

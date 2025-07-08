<script>
    $(function() {
        $('#bbkList').on("click",'.btn-disburse',function(e){
			e.preventDefault();
			
			$.fn.getParent = function(num){
                var last = this[0];
                for(var i = 0; i < num; i++){
                    last = last.parentNode;
                }
                return $(last);
            };
			
			var number = $(this).getParent(1).siblings(".bbk-number").html();
			var action = $(this).data('action');
			
			$('#disburseConfirm').modal('show', {backdrop: 'static', keyboard :false});
			$("#disburseConfirm .number").text(number);
			$('#disburseForm').attr("action" , action);
		});
	});
</script>

	<script>
		$(document).ready(function () {
			$('input[type=checkbox]').on('click',function(){
				var checked = $("input[type=checkbox]:checked").length;
				
				if(checked < 1){
				  $("input[type=checkbox]").prop('required',true);
				}
				else{
				  $("input[type=checkbox]").prop('required',false);
				}
			});
		});
	</script>
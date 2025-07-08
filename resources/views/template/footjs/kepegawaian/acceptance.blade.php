	<script>
		$(document).ready(function () {
			$('input[name="acceptance_status"]').on('change',function(){
				var acceptance_status = $(this).val();
				if(acceptance_status == 1){
					var checked = $('input[name^="unit"]:checked').length;
					if(checked < 1){
					  $('input[name^="unit"]').prop('required',true);
					}
					else{
					  $('input[name^="unit"]').prop('required',false);
					}
					$('input[name="employee_status"]').prop("required", true);
					$('input[name="period_start"]').prop("required", true);
					$('input[name="period_end"]').prop("required", true);
					$('#unitRow').fadeIn('normal');
					$('#positionRow').fadeIn('normal');
					$('#statusRow').fadeIn('normal');
					$('#periodRow').fadeIn('normal');
				}else{
					$('input[name^="unit"]').prop("required", false);
					$('input[name="employee_status"]').prop("required", false);
					$('input[name="period_start"]').prop("required", false);
					$('input[name="period_end"]').prop("required", false);
					$('#unitRow').fadeOut('normal');
					$('#positionRow').fadeOut('normal');
					$('#statusRow').fadeOut('normal');
					$('#periodRow').fadeOut('normal');
				}
			});
		});
	</script>

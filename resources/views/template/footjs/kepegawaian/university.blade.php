	<script>
		$(document).ready(function () {
			$('select[name="recent_education"]').on('change',function(){
				var degrees = ["D1","D2","D3","S1","S2","S3"];
				var recent_education = $(this).children("option:selected").html();
				if(degrees.includes(recent_education)){
					$('select[name="university"]').prop("required", true);
					$('#universityRow').fadeIn('normal');
				}else{
					$('select[name="university"]').prop("required", false);
					$('#universityRow').fadeOut('normal');
				}
			});
		});
	</script>

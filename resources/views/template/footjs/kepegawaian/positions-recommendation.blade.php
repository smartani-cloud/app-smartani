	<script>
	$(document).ready(function () {
		$('input[name="unit[]"]').on('change',function(){
		  var units = [];
		  $('input[name="unit[]"]:checked').each(function() {
			units.push($(this).val());
		  });
		  console.log('Units: '+units);
		  if(units.length < 1){
			$('select[name="position[]"]').prop('disabled',true);
		  }
		  else{
			$('select[name="position[]"]').prop('disabled',false);
		  }
		  $('select[name="position[]"]').find('option:selected').removeAttr("selected");
		  $('select[name="position[]"]').val(null).trigger("change"); 
		  $('select[name="position[]"] option').prop('disabled',false).removeClass('bg-gray-300');
		  var str = '';
		  $.each(units,function(key,item){
			str += '[data-unit!='+item+']';
		  });
		  $('select[name="position[]"] option'+ str).prop('disabled',true).addClass('bg-gray-300');
		});
	});
	</script>

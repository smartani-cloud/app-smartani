<<<<<<< HEAD
<script>
    function validateModal(name, acceptance, unit, position, status, period, validate_url)
    {
		$('#validate-confirm #unitRecommendation').hide();
		$('#validate-confirm #positionRecommendation').hide();
		$('#validate-confirm #statusRecommendation').hide();
		$('#validate-confirm #periodRecommendation').hide();
    	$('#validate-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#validate-confirm .name").text(name);
		$("#validate-confirm .acceptance").text(acceptance);
		if(unit != '-'){
			$('#validate-confirm #unitRecommendation').show();
			$("#validate-confirm .unit").text(unit);
		}
		if(position != '-'){
			$('#validate-confirm #positionRecommendation').show();
			$("#validate-confirm .position").text(position);
		}
		if(status != '-'){
			$('#validate-confirm #statusRecommendation').show();
			$("#validate-confirm .status").text(status);
		}
		if(period != '-'){
			$('#validate-confirm #periodRecommendation').show();
			$("#validate-confirm .period").text(period);
		}
    	$('#validate-link').attr("action" , validate_url);
    }
</script>
=======
<script>
    function validateModal(name, acceptance, unit, position, status, period, validate_url)
    {
		$('#validate-confirm #unitRecommendation').hide();
		$('#validate-confirm #positionRecommendation').hide();
		$('#validate-confirm #statusRecommendation').hide();
		$('#validate-confirm #periodRecommendation').hide();
    	$('#validate-confirm').modal('show', {backdrop: 'static', keyboard :false});
    	$("#validate-confirm .name").text(name);
		$("#validate-confirm .acceptance").text(acceptance);
		if(unit != '-'){
			$('#validate-confirm #unitRecommendation').show();
			$("#validate-confirm .unit").text(unit);
		}
		if(position != '-'){
			$('#validate-confirm #positionRecommendation').show();
			$("#validate-confirm .position").text(position);
		}
		if(status != '-'){
			$('#validate-confirm #statusRecommendation').show();
			$("#validate-confirm .status").text(status);
		}
		if(period != '-'){
			$('#validate-confirm #periodRecommendation').show();
			$("#validate-confirm .period").text(period);
		}
    	$('#validate-link').attr("action" , validate_url);
    }
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

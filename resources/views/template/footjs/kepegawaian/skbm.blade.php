	<script>
    $('input[name="students"]').TouchSpin({
        min: 0,
        max: 100,                
        boostat: 5,
        maxboostedstep: 10,        
        initval: 1,
		buttondown_class: "btn btn-brand-green-dark bootstrap-touchspin-down",
		buttonup_class: "btn btn-brand-green-dark bootstrap-touchspin-up"
    });
	$('input[name="teaching_load"]').TouchSpin({
        min: 0,
        max: 100,                
        boostat: 5,
        maxboostedstep: 10,        
        initval: 1,
		buttondown_class: "btn btn-brand-green-dark bootstrap-touchspin-down",
		buttonup_class: "btn btn-brand-green-dark bootstrap-touchspin-up"
    });
    </script>
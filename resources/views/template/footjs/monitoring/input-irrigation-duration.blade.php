	<script>
    $('#inputIrrigationDuration').TouchSpin({
        min: 0,
        max: 3000,
        boostat: 50,
        maxboostedstep: 100,
        initval: {!! old('irrigation_duration', $data ? $data->irrigation_duration_seconds : 1) !!},
		buttondown_class: "btn btn-brand-green-dark bootstrap-touchspin-down",
		buttonup_class: "btn btn-brand-green-dark bootstrap-touchspin-up"
    });
    </script>
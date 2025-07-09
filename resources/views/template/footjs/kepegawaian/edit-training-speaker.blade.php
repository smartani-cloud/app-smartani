<<<<<<< HEAD
	<script>
		$(document).ready(function () {
			$('#editSpeaker').select2({
				placeholder: "Pilih salah satu",
				theme: 'bootstrap4',
				allowClear: true
			});
			$('#pelatihan-form input[name="speaker_category"]').on('change',function(){
				var speaker = $(this).val();
				if(speaker == 1){
					$('#pelatihan-form select[name="speaker"]').prop("required", true);
					$('#pelatihan-form input[name="speaker_name"]').prop("required", false);
					$('#editSpeakerNameCol').hide();
					$('#editSpeakerIdCol').show();
				}else{
					$('#pelatihan-form select[name="speaker"]').prop("required", false);
					$('#pelatihan-form input[name="speaker_name"]').prop("required", true);
					$('#editSpeakerIdCol').hide();
					$('#editSpeakerNameCol').show();
				}
			});
		});
	</script>
=======
	<script>
		$(document).ready(function () {
			$('#editSpeaker').select2({
				placeholder: "Pilih salah satu",
				theme: 'bootstrap4',
				allowClear: true
			});
			$('#pelatihan-form input[name="speaker_category"]').on('change',function(){
				var speaker = $(this).val();
				if(speaker == 1){
					$('#pelatihan-form select[name="speaker"]').prop("required", true);
					$('#pelatihan-form input[name="speaker_name"]').prop("required", false);
					$('#editSpeakerNameCol').hide();
					$('#editSpeakerIdCol').show();
				}else{
					$('#pelatihan-form select[name="speaker"]').prop("required", false);
					$('#pelatihan-form input[name="speaker_name"]').prop("required", true);
					$('#editSpeakerIdCol').hide();
					$('#editSpeakerNameCol').show();
				}
			});
		});
	</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

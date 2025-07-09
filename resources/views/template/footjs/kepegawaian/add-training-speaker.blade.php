<<<<<<< HEAD
	<script>
		$(document).ready(function () {
			$('#selectSpeaker').select2({
				placeholder: "Pilih salah satu",
				theme: 'bootstrap4',
				allowClear: true
			});
			$('#add-form input[name="speaker_category"]').on('change',function(){
				var speaker = $(this).val();
				if(speaker == 1){
					$('#add-form select[name="speaker"]').prop("required", true);
					$('#add-form input[name="speaker_name"]').prop("required", false);
					$('#speakerNameCol').hide();
					$('#speakerIdCol').show();
				}else{
					$('#add-form select[name="speaker"]').prop("required", false);
					$('#add-form input[name="speaker_name"]').prop("required", true);
					$('#speakerIdCol').hide();
					$('#speakerNameCol').show();
				}
			});
		});
	</script>
=======
	<script>
		$(document).ready(function () {
			$('#selectSpeaker').select2({
				placeholder: "Pilih salah satu",
				theme: 'bootstrap4',
				allowClear: true
			});
			$('#add-form input[name="speaker_category"]').on('change',function(){
				var speaker = $(this).val();
				if(speaker == 1){
					$('#add-form select[name="speaker"]').prop("required", true);
					$('#add-form input[name="speaker_name"]').prop("required", false);
					$('#speakerNameCol').hide();
					$('#speakerIdCol').show();
				}else{
					$('#add-form select[name="speaker"]').prop("required", false);
					$('#add-form input[name="speaker_name"]').prop("required", true);
					$('#speakerIdCol').hide();
					$('#speakerNameCol').show();
				}
			});
		});
	</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

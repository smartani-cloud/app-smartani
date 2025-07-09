	<script>
	$(function() {
		$('#training-info').on("click",'.btn-end',function(e){
			var date = $('#date-col').text().trim();
			var place = $('#place-col').text().trim();
			if(date == 'Pilih tanggal' || place == 'Tambahkan tempat'){
				$(this).popover('dispose');
				$(this).popover({
					content: function(){
						var date = $('#date-col').text().trim();
						var place = $('#place-col').text().trim();
						var content = '<i class="fa fa-exclamation-circle text-warning mr-1"></i>';
						if(date == 'Pilih tanggal' && place == 'Tambahkan tempat'){
							content = content+'Mohon pilih tanggal dan tambahkan tempat terlebih dahulu';
						}
						else if(date == 'Pilih tanggal'){
							content = content+'Mohon pilih tanggal terlebih dahulu';
						}
						else if(place == 'Tambahkan tempat'){
							content = content+'Mohon tambahkan tempat terlebih dahulu';
						}
						return content;
					},
					html: true
				});
				$(this).popover('show');
				console.log('*'+date+'*'+place+'*'+(date == 'Pilih tanggal')+'*'+(place == 'Tambahkan tempat')+'*');
			}
			else{
				console.log('*'+date+'*'+place+'*'+(date == 'Pilih tanggal')+'*'+(place == 'Tambahkan tempat')+'*');
				var date_col = $('#date-col').attr('data-date');
				var today_date = Date.now();
				var end_date = Date.parse(date_col);
				console.log(today_date+' >= '+end_date);
				if(today_date >= end_date){
					$(this).popover('dispose');
					$('#end-confirm').modal('show', {backdrop: 'static', keyboard :false});
					$("#end-confirm .modal-date").text(date);
					$("#end-confirm .modal-place").text(place);
					$('#end-link').attr("action" , $(this).data('href'));
				}
				else{
					$(this).popover('dispose');
					$(this).popover({
						content: '<i class="fa fa-exclamation-circle text-warning mr-1"></i>Mohon periksa kembali tanggal pelatihan',
						html: true
					});
					$(this).popover('show');
				}
			}
        });
    });
	</script>

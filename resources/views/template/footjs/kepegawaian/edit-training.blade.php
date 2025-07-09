<<<<<<< HEAD
<script>
    $(function() {
        $('#training-info').on("click",'.btn-edit',function(e){
			e.preventDefault();
			$('#training-info .btn-end').popover('update');
			var href = $(this).data('href');
			var name = href.split("-")[1];
			$('#'+name+'-col').hide();
			$(href).show();
        });
		$('#training-info').on("click",'.btn-cancel',function(e){
			e.preventDefault();
			$('#training-info .btn-end').popover('update');
			var href = $(this).data('dismiss');
			var name = href.split("-")[1];
			var place = $('#place-col').text().trim();
			if(name === 'place' && place !== 'Tambahkan tempat'){
				$('input[name="'+name+'"]').val(place);
			}
			$('#'+name+'-col').show();
			$(href).hide();
        });
		$('#training-info').on("click",'.btn-submit',function(e){
			e.preventDefault();
			var id = $(this).attr('id');
			var idBtn = '#'+id;
			var name = id.split("-")[1];
			var value = $('input[name="'+name+'"]').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('pelatihan.materi.perbarui.atribut') }}",
                data: {
                    '_method': 'PUT',
                    'id': {{ $pelatihan->id }},
                    'name': name,
                    'value': value
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function(xhr){
                  $(idBtn).attr('disabled', true);
				  $(idBtn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Menyimpan...');
                },
                success: function(response){
                    if(response.status == 'success'){
						bootoast.toast({
							message: response.message,
							position: 'top-center',
							type: response.status
						});
						if(name == 'date' && response.date != null){
							$('#'+name+'-col').empty();
							$('#'+name+'-col').attr("data-"+name,response.date);
							if($('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').removeClass('mb-2');
							$('#'+name+'-col').html(response.date_id);
							$('#'+name+'-col').append('<button type="button" data-href="#edit-date-form" class="btn btn-sm btn-light btn-edit ml-2"><i class="fas fa-pen"></i></button>');
							
						}
						else if(name == 'date' && response.date == null){
							$('#'+name+'-col').empty();
							if(!$('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').addClass('mb-2');
							$('#'+name+'-col').append('<button type="button" data-href="#edit-date-form" class="btn btn-sm btn-brand-purple-dark btn-edit"><i class="fas fa-plus-circle mr-1"></i>Pilih tanggal</button>');
						}
						if(name == 'place' && value.length > 0){
							$('#'+name+'-col').empty();
							if($('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').removeClass('mb-2');
							$('#'+name+'-col').html(value);
							$('#'+name+'-col').append('<button type="button" data-href="#edit-place-form" class="btn btn-sm btn-light btn-edit ml-2"><i class="fas fa-pen"></i></button>');
						}
						else if(name == 'place' && value.length <= 0){
							$('#'+name+'-col').empty();
							if(!$('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').addClass('mb-2');
							$('#'+name+'-col').append('<button type="button" data-href="#edit-place-form" class="btn btn-sm btn-brand-purple-dark btn-edit"><i class="fas fa-plus-circle mr-1"></i>Tambahkan tempat</button>');
						}
						$(idBtn).attr('disabled', false);
						$(idBtn).html('Simpan');
						$('#'+name+'-col').show();
						$('#edit-'+name+'-form').hide();
						$('#training-info .btn-end').popover('update');
                    }
                    else{
						$(idBtn).attr('disabled', false);
						$(idBtn).html('Simpan');
						$('#training-info .btn-end').popover('update');
                   }
                },
                error: function(xhr, textStatus, thrownError){
					$(idBtn).attr('disabled', false);
					$(idBtn).html('Simpan');
					$('#training-info .btn-end').popover('update');
                    bootoast.toast({
                        message: 'Perubahan data pelatihan gagal disimpan<br>'+textStatus+' - '+thrownError,
                        position: 'top-center',
                        type: 'danger'
                    });
                }
            });
        });
    });
</script>
=======
<script>
    $(function() {
        $('#training-info').on("click",'.btn-edit',function(e){
			e.preventDefault();
			$('#training-info .btn-end').popover('update');
			var href = $(this).data('href');
			var name = href.split("-")[1];
			$('#'+name+'-col').hide();
			$(href).show();
        });
		$('#training-info').on("click",'.btn-cancel',function(e){
			e.preventDefault();
			$('#training-info .btn-end').popover('update');
			var href = $(this).data('dismiss');
			var name = href.split("-")[1];
			var place = $('#place-col').text().trim();
			if(name === 'place' && place !== 'Tambahkan tempat'){
				$('input[name="'+name+'"]').val(place);
			}
			$('#'+name+'-col').show();
			$(href).hide();
        });
		$('#training-info').on("click",'.btn-submit',function(e){
			e.preventDefault();
			var id = $(this).attr('id');
			var idBtn = '#'+id;
			var name = id.split("-")[1];
			var value = $('input[name="'+name+'"]').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('pelatihan.materi.perbarui.atribut') }}",
                data: {
                    '_method': 'PUT',
                    'id': {{ $pelatihan->id }},
                    'name': name,
                    'value': value
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function(xhr){
                  $(idBtn).attr('disabled', true);
				  $(idBtn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Menyimpan...');
                },
                success: function(response){
                    if(response.status == 'success'){
						bootoast.toast({
							message: response.message,
							position: 'top-center',
							type: response.status
						});
						if(name == 'date' && response.date != null){
							$('#'+name+'-col').empty();
							$('#'+name+'-col').attr("data-"+name,response.date);
							if($('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').removeClass('mb-2');
							$('#'+name+'-col').html(response.date_id);
							$('#'+name+'-col').append('<button type="button" data-href="#edit-date-form" class="btn btn-sm btn-light btn-edit ml-2"><i class="fas fa-pen"></i></button>');
							
						}
						else if(name == 'date' && response.date == null){
							$('#'+name+'-col').empty();
							if(!$('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').addClass('mb-2');
							$('#'+name+'-col').append('<button type="button" data-href="#edit-date-form" class="btn btn-sm btn-brand-purple-dark btn-edit"><i class="fas fa-plus-circle mr-1"></i>Pilih tanggal</button>');
						}
						if(name == 'place' && value.length > 0){
							$('#'+name+'-col').empty();
							if($('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').removeClass('mb-2');
							$('#'+name+'-col').html(value);
							$('#'+name+'-col').append('<button type="button" data-href="#edit-place-form" class="btn btn-sm btn-light btn-edit ml-2"><i class="fas fa-pen"></i></button>');
						}
						else if(name == 'place' && value.length <= 0){
							$('#'+name+'-col').empty();
							if(!$('#'+name+'-col').hasClass('mb-2')) $('#'+name+'-col').addClass('mb-2');
							$('#'+name+'-col').append('<button type="button" data-href="#edit-place-form" class="btn btn-sm btn-brand-purple-dark btn-edit"><i class="fas fa-plus-circle mr-1"></i>Tambahkan tempat</button>');
						}
						$(idBtn).attr('disabled', false);
						$(idBtn).html('Simpan');
						$('#'+name+'-col').show();
						$('#edit-'+name+'-form').hide();
						$('#training-info .btn-end').popover('update');
                    }
                    else{
						$(idBtn).attr('disabled', false);
						$(idBtn).html('Simpan');
						$('#training-info .btn-end').popover('update');
                   }
                },
                error: function(xhr, textStatus, thrownError){
					$(idBtn).attr('disabled', false);
					$(idBtn).html('Simpan');
					$('#training-info .btn-end').popover('update');
                    bootoast.toast({
                        message: 'Perubahan data pelatihan gagal disimpan<br>'+textStatus+' - '+thrownError,
                        position: 'top-center',
                        type: 'danger'
                    });
                }
            });
        });
    });
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7

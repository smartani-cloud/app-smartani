<script>
    $(function() {
        $('#training-presence').on("click",'.btn-check',function(e){
			e.preventDefault();
            $.fn.getParent = function(num){
                var last = this[0];
                for(var i = 0; i < num; i++){
                    last = last.parentNode;
                }
                return $(last);
            };

            var tr_id = $(this).getParent(2).attr('id');
            var employee_id = tr_id.split("-")[1];

            var td_action = $(this).getParent(1);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('pelatihan.kehadiran.hadir') }}",
                data: {
                    '_method': 'PUT',
                    'id': {{ $pelatihan->id }},
                    'employee_id': employee_id,
                    'status': 'hadir'
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function(xhr){
                  $('#tr-'+employee_id+' button').attr('disabled', true);
                },
                success: function(response){
                    bootoast.toast({
                        message: response.message,
                        position: 'top-center',
                        type: response.status
                    });
                    if(response.status == 'success'){
                        td_action.empty();
						var faUndo = $('<i></i>').addClass('fas fa-undo-alt');
						var cancelBtn = $('<button></button>').addClass('btn btn-sm btn-light btn-cancel').html(faUndo);
                        td_action.html(cancelBtn);
						var badgeSuccess = $('<span></span>').addClass('badge badge-success font-weight-normal').attr('data-toggle','tooltip').attr('data-original-title',response.acc_time).html('Hadir');
						td_action.prev().html(badgeSuccess);
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                    else{
                       $('#tr-'+employee_id+' button').attr('disabled', false);
                   }
                },
                error: function(xhr, textStatus, thrownError){
                    $('#tr-'+employee_id+' button').attr('disabled', false);
                    bootoast.toast({
                        message: 'Kehadiran pegawai gagal disimpan<br>'+textStatus+' - '+thrownError,
                        position: 'top-center',
                        type: 'danger'
                    });
                }
            });
        });
		$('#training-presence').on("click",'.btn-times',function(e){
			e.preventDefault();
            $.fn.getParent = function(num) {
                var last = this[0];
                for (var i = 0; i < num; i++) {
                    last = last.parentNode;
                }
                return $(last);
            };

            var tr_id = $(this).getParent(2).attr('id');
            var employee_id = tr_id.split("-")[1];

            var td_action = $(this).getParent(1);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('pelatihan.kehadiran.hadir') }}",
                data: {
                    '_method': 'PUT',
                    'id': {{ $pelatihan->id }},
                    'employee_id': employee_id,
                    'status': 'absen'
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function(xhr){
                  $('#tr-'+employee_id+' button').attr('disabled', true);
                },
                success: function(response){
                    bootoast.toast({
                        message: response.message,
                        position: 'top-center',
                        type: response.status
                    });
                    if(response.status == 'success'){
                        td_action.empty();
						var faUndo = $('<i></i>').addClass('fas fa-undo-alt');
						var cancelBtn = $('<button></button>').addClass('btn btn-sm btn-light btn-cancel').html(faUndo);
                        td_action.html(cancelBtn);
						var badgeDanger = $('<span></span>').addClass('badge badge-danger font-weight-normal').attr('data-toggle','tooltip').attr('data-original-title',response.acc_time).html('Absen');
						td_action.prev().html(badgeDanger);
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                    else{
                       $('#tr-'+employee_id+' button').attr('disabled', false);
                   }
                },
                error: function(xhr, textStatus, thrownError){
                    $('#tr-'+employee_id+' button').attr('disabled', false);
                    bootoast.toast({
                        message: 'Kehadiran pegawai gagal disimpan<br>'+textStatus+' - '+thrownError,
                        position: 'top-center',
                        type: 'danger'
                    });
                }
            });
        });
		$('#training-presence').on("click",'.btn-cancel',function(e){
			e.preventDefault();
            $.fn.getParent = function(num) {
                var last = this[0];
                for (var i = 0; i < num; i++) {
                    last = last.parentNode;
                }
                return $(last);
            };

            var tr_id = $(this).getParent(2).attr('id');
            var employee_id = tr_id.split("-")[1];

            var td_action = $(this).getParent(1);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('pelatihan.kehadiran.batal') }}",
                data: {
                    '_method': 'PUT',
                    'id': {{ $pelatihan->id }},
                    'employee_id': employee_id
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function(xhr){
                  $('#tr-'+employee_id+' button').attr('disabled', true);
                },
                success: function(response){
                    bootoast.toast({
                        message: response.message,
                        position: 'top-center',
                        type: response.status
                    });
                    if(response.status == 'success'){
                        td_action.empty();
						var faCheck = $('<i></i>').addClass('fas fa-check');
						var checkBtn = $('<button></button>').addClass('btn btn-sm btn-success btn-check').html(faCheck);
						var faTimes = $('<i></i>').addClass('fas fa-times');
						var timesBtn = $('<button></button>').addClass('btn btn-sm btn-danger btn-times').html(faTimes);
                        td_action.html(checkBtn);
						td_action.children("button").after(timesBtn).after('&nbsp;');
						td_action.prev().html('-');
                    }
                    else{
                       $('#tr-'+employee_id+' button').attr('disabled', false);
                   }
                },
                error: function(xhr, textStatus, thrownError){
                    $('#tr-'+employee_id+' button').attr('disabled', false);
                    bootoast.toast({
                        message: 'Kehadiran pegawai gagal dibatalkan<br>'+textStatus+' - '+thrownError,
                        position: 'top-center',
                        type: 'danger'
                    });
                }
            });
			
        });
    });
</script>

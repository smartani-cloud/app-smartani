<script>
    $(document).ready(function () {
        $('#master').on('click', function(e){
            if($(this).is(':checked',true)){
                $(".sub-chk").prop('checked', true);  
            }
            else{  
                $(".sub-chk").prop('checked',false);  
            }  
        });

        $('.btn-update-all').on('click', function(e){
            var allVals = [];
            $(".sub-chk:checked").each(function() {  
                allVals.push($(this).attr('data-id'));
            });

            if(allVals.length <= 0){  
                alert("Mohon pilih data yang akan diatur");  
            }
            else{  
                var check = confirm("Apakah Anda yakin ingin mengatur data terpilih sekaligus?");  
                if(check == true){
                    var join_selected_values = allVals.join(",");
                    var period_start = $('#inputPeriodStart').val();
                    var period_end = $('#inputPeriodEnd').val();

                    $.ajax({
                        url: $(this).data('url'),
                        type: 'POST',
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {
                            '_method': 'PUT',
                            'ids': join_selected_values,
                            'period_start': period_start,
                            'period_end': period_end
                        },
                        success: function(data){
                            bootoast.toast({
                                message: data.message,
                                position: 'top-center',
                                type: data.status
                            });
                            if(data.status == 'success'){
                                $(".sub-chk:checked").each(function(){  
                                    $(this).parents("tr").remove();
                                });
                            }
                            else if(data.status == 'danger'){
                                alert(data.message);
                            }
                            else {
                                alert('Ups, ada yang bermasalah!');
                            }
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });

                    $.each(allVals, function(index, value) {
                      $('table tr').filter("[data-row-id='"+value+"']").remove();
                  });
                }  
            }  
        });

        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            onConfirm: function (event, element) {
                element.trigger('confirm');
            }
        });

        $(document).on('confirm', function (e) {
            var ele = e.target;
            e.preventDefault();


            $.ajax({
                url: ele.href,
                type: 'PUT',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) {
                    if (data['success']) {
                        $("#" + data['tr']).slideUp("slow");
                        alert(data['success']);
                    } else if (data['error']) {
                        alert(data['error']);
                    } else {
                        alert('Whoops Something went wrong!!');
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                }
            });


            return false;
        });
    });
</script>

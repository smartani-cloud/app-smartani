<script>
$(document).ready(function()
{
    const unitnya = $('#unit_split').val();
    getSiswaList(unitnya);

    $('#ubahKategori').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id');
        var name = button.data('name');
        var total = button.data('total');
        var student_id = button.data('student_id');
        var is_student = button.data('siswa');
        var modal = $(this);
        modal.find('input[name="id"]').val(id);
        $('#is_student').val(is_student);
        $('#nama_siswa').val(name);
        $('#student_id').val(student_id);
        $('#total').val(total);
        $('#nominal_siswa').val(total);
        $('#nominal_split').val(0);
        $('#refund').val(0);
        $('#split').val(0);
        $('.split-siswa').hide();
        hitungSemua();
        modal.find('p[id="name"]').text('Apakah Anda yakin akan mengubah kategori transaksi '+name+'?');
    });

    // $('.select2-hidden-accessible').select2({
    //     theme: 'bootstrap4'
    // });

    $('#nominal_siswa').on('change', function() {
        hitungSemua();
    });
    $('#nominal_split').on('change', function() {
        hitungSemua();
    });
    $('#refund').on('change', function() {
        hitungSemua();
    });
    $('#split').on('change', function() {
        var value = this.value;
        $('#nominal_split').val(0);
        hitungSemua();
        if(value == 1){
            $('.split-siswa').show();
        }else{
            $('.split-siswa').hide();
        }
    });
    $('#category_split').on('change', function() {
        var str = this.value;
        if(this.value == 'calon') str += ' Siswa';
        str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
        });
        $('#jenis_pembayaran_split option[value="2"]').prop('disabled',false);
        if($('#jenis_pembayaran_split option[value="2"]').hasClass('bg-gray-300'))
            $('#jenis_pembayaran_split option[value="2"]').removeClass('bg-gray-300');
        $('#jenis_pembayaran_split').removeAttr('readonly');
        if(this.value == 'calon'){
            $('#jenis_pembayaran_split').val(1);
            $('#jenis_pembayaran_split option[value="2"]').prop('disabled',true);
            $('#jenis_pembayaran_split option[value="2"]').addClass('bg-gray-300');
            $('#jenis_pembayaran_split').attr('readonly','readonly');
        }
        $('label[for="unit_split"]').html('Unit '+str);
        $('label[for="siswa_split"]').html('Pilih '+str);
        getSiswaList($('#unit_split').val());
    });

    $('#unit_split').on('change', function() {
        getSiswaList(this.value);
    });

    $('#jenis_pembayaran').on('change', function() {
        getSiswaList($('#unit_split').val());
    });
    $('#jenis_pembayaran_split').on('change', function() {
        getSiswaList($('#unit_split').val());
    });
})

function hitungSemua(){
    var total = parseInt($('#total').val().replace(/\./g, ""));
    var nominal_siswa = parseInt($('#nominal_siswa').val().replace(/\./g, ""));
    var nominal_split = parseInt($('#nominal_split').val().replace(/\./g, ""));
    var refund = total - (nominal_siswa + nominal_split);

    $('#refund').val(numberWithCommas(refund));
    
    if(nominal_siswa > total){
        $('#nominal_siswa').val(numberWithCommas(total-nominal_split));
        $('#refund').val(0);
    }else if(refund < 0){
        $('#nominal_split').val(numberWithCommas(total-nominal_siswa));
        console.log(total-nominal_siswa);
        $('#refund').val(0);
    }
}

function getSiswaList(unit){
    const jenis_split = $('#jenis_pembayaran_split').val();
    const jenis = $('#jenis_pembayaran').val();
    const student_id = $('#student_id').val();
    var category = $('#category_split').val();
    if(category == 'calon'){
        jQuery.ajax({
            url: "{{ route('spp.list-calon') }}/"+unit,
            type : "GET",
            beforeSend  : function() {
                $('#siswa_split').prop('disabled',true);
            },
            success:function(data)
            {
                $('.option-siswa').remove();
                data.map((item, index) => {
                    if(item[0] == student_id && jenis == jenis_split){

                    }else{
                        const valuenya = '<option class="option-siswa" value="'+item[0]+'" selected>'+item[1] + ' - ' + item[2]+'</option>';
                        $('#siswa_split').append(valuenya);
                    }
                });
                $('#siswa_split').prop('disabled',false);
            }
        });
    }
    else{
        jQuery.ajax({
            url: "{{ route('spp.list-siswa') }}/"+unit,
            type : "GET",
            beforeSend  : function() {
                $('#siswa_split').prop('disabled',true);
            },
            success:function(data)
            {
                $('.option-siswa').remove();
                data.map((item, index) => {
                    if(item[0] == student_id && jenis == jenis_split){

                    }else{
                        const valuenya = '<option class="option-siswa" value="'+item[0]+'" selected>'+item[1] + ' - ' + item[2]+'</option>';
                        $('#siswa_split').append(valuenya);
                    }
                });
                $('#siswa_split').prop('disabled',false);
            }
        });
    }
}
</script>

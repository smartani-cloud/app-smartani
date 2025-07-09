<<<<<<< HEAD
<script type="text/javascript">
    $(document).ready(function() {
        //var max_fields = 12;
        var max_fields = 999;
        var wrapper = $("table tbody");
        var add_button = $("#tambahhafalan");

        var x = 1;
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                var tr = $('<tr></tr>').addClass('new-surah');
                var td_1 = $('<td></td>').attr('width','20%');
                var selectJenis = $('<select></select>').addClass('form-control').attr('name','jenis[]').prop("required", true);
                var jenis = {"surat":"Surat","juz":"Juz"};
                $.each(jenis,function(key,value){
                    var option = $('<option></option>').attr('value',key).html(value);
                    selectJenis.append(option);
                });
                td_1.html(selectJenis);
                var td_2 = $('<td></td>').attr('width','30%');
                var selectSurat = $('<select></select>').addClass('form-control').attr('name','surat[]').prop("required", true);
                var surat = {!! $surat->pluck('surahNumberPrefix','id')->toJson() !!};
                if(Object.keys(surat).length > 0){
                    $.each(surat,function(key,value){
                        var option = $('<option></option>').attr('value',key).html(value);
                        selectSurat.append(option);
                    });
                }
                else{
                    selectSurat.prop("disabled", true);
                }
                var selectJuz = $('<select></select>').addClass('form-control').attr({name: 'juz[]',style: 'display: none'});
                var juz = {!! $juz->pluck('juz','id')->toJson() !!};
                if(Object.keys(juz).length > 0){
                    $.each(juz,function(key,value){
                        var option = $('<option></option>').attr('value',key).html(value);
                        selectJuz.prepend(option);
                    });
                }
                else{
                    selectJuz.prop("disabled", true);
                }
                selectJuz.val(null);
                selectJuz.children().first().prop("selected", true);
                td_2.append(selectSurat, selectJuz);
                var td_3 = $('<td></td>').attr('width','25%');
                var selectStatus = $('<select></select>').addClass('form-control').attr('name','status[]').prop("required", true);
                var status = {!! $status->pluck('status','id')->toJson() !!};
                if(Object.keys(status).length > 0){
                    $.each(status,function(key,value){
                        var option = $('<option></option>').attr('value',key).html(value);
                        selectStatus.prepend(option);
                    });
                }
                else{
                    selectStatus.prop("disabled", true);
                }                
                selectStatus.val(null);
                selectStatus.children().first().prop("selected", true);
                td_3.html(selectStatus);
                var td_4 = $('<td></td>').attr('width','25%');
                var selectPredikat = $('<select></select>').addClass('form-control').attr('name','predikat[]').prop("required", true);
                var predikat = {"A":"A","B":"B","C":"C"};
                var option = $('<option></option>').attr('value','').html('== Pilih ==');
                selectPredikat.html(option);
                $.each(predikat,function(key,value){
                    var option = $('<option></option>').attr('value',key).html(value);
                    selectPredikat.append(option);
                });
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var deleteBtn = $('<button></button>').addClass('btn btn-sm btn-danger h-100').attr({
                    id: "hapushafalan",
                    type: "button",

                }).html(faTimes);
                var col9 = $('<div></div>').addClass('col-9').html(selectPredikat);
                var col3 = $('<div></div>').addClass('col-3').html(deleteBtn);
                var row = $('<div></div>').addClass('row').append(col9, col3);
                td_4.html(row);
                tr.append(td_1, td_2, td_3, td_4);
                $(wrapper).append(tr);
                // $(wrapper).append('<tr><td><input type="text" class="form-control" name="hafalan[]" placeholder="Nama Surah" required></td><td><div class="row"><div class="col-md-11"><select class="form-control" name="predikat[]" required><option value="">== Pilih ==</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></div><div class="col-md-1"><button type="button" id="hapushafalan" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });

        $(wrapper).on("click", "#hapushafalan", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        });

        $(wrapper).on('change',"select[name^='jenis']",function(){
            if($(this).val() == 'surat'){
                var nextTd = $(this).parent().next();
                nextTd.children('select').first().prop("required", true);
                nextTd.children('select').first().show();
                nextTd.children('select').last().prop("required", false);
                nextTd.children('select').last().hide();
            }
            else if($(this).val() == 'juz'){
                var nextTd = $(this).parent().next();
                nextTd.children('select').first().prop("required", false);
                nextTd.children('select').first().hide();
                nextTd.children('select').last().prop("required", true);
                nextTd.children('select').last().show();
            }
        });
    });
=======
<script type="text/javascript">
    $(document).ready(function() {
        //var max_fields = 12;
        var max_fields = 999;
        var wrapper = $("table tbody");
        var add_button = $("#tambahhafalan");

        var x = 1;
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                var tr = $('<tr></tr>').addClass('new-surah');
                var td_1 = $('<td></td>').attr('width','20%');
                var selectJenis = $('<select></select>').addClass('form-control').attr('name','jenis[]').prop("required", true);
                var jenis = {"surat":"Surat","juz":"Juz"};
                $.each(jenis,function(key,value){
                    var option = $('<option></option>').attr('value',key).html(value);
                    selectJenis.append(option);
                });
                td_1.html(selectJenis);
                var td_2 = $('<td></td>').attr('width','30%');
                var selectSurat = $('<select></select>').addClass('form-control').attr('name','surat[]').prop("required", true);
                var surat = {!! $surat->pluck('surahNumberPrefix','id')->toJson() !!};
                if(Object.keys(surat).length > 0){
                    $.each(surat,function(key,value){
                        var option = $('<option></option>').attr('value',key).html(value);
                        selectSurat.append(option);
                    });
                }
                else{
                    selectSurat.prop("disabled", true);
                }
                var selectJuz = $('<select></select>').addClass('form-control').attr({name: 'juz[]',style: 'display: none'});
                var juz = {!! $juz->pluck('juz','id')->toJson() !!};
                if(Object.keys(juz).length > 0){
                    $.each(juz,function(key,value){
                        var option = $('<option></option>').attr('value',key).html(value);
                        selectJuz.prepend(option);
                    });
                }
                else{
                    selectJuz.prop("disabled", true);
                }
                selectJuz.val(null);
                selectJuz.children().first().prop("selected", true);
                td_2.append(selectSurat, selectJuz);
                var td_3 = $('<td></td>').attr('width','25%');
                var selectStatus = $('<select></select>').addClass('form-control').attr('name','status[]').prop("required", true);
                var status = {!! $status->pluck('status','id')->toJson() !!};
                if(Object.keys(status).length > 0){
                    $.each(status,function(key,value){
                        var option = $('<option></option>').attr('value',key).html(value);
                        selectStatus.prepend(option);
                    });
                }
                else{
                    selectStatus.prop("disabled", true);
                }                
                selectStatus.val(null);
                selectStatus.children().first().prop("selected", true);
                td_3.html(selectStatus);
                var td_4 = $('<td></td>').attr('width','25%');
                var selectPredikat = $('<select></select>').addClass('form-control').attr('name','predikat[]').prop("required", true);
                var predikat = {"A":"A","B":"B","C":"C"};
                var option = $('<option></option>').attr('value','').html('== Pilih ==');
                selectPredikat.html(option);
                $.each(predikat,function(key,value){
                    var option = $('<option></option>').attr('value',key).html(value);
                    selectPredikat.append(option);
                });
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var deleteBtn = $('<button></button>').addClass('btn btn-sm btn-danger h-100').attr({
                    id: "hapushafalan",
                    type: "button",

                }).html(faTimes);
                var col9 = $('<div></div>').addClass('col-9').html(selectPredikat);
                var col3 = $('<div></div>').addClass('col-3').html(deleteBtn);
                var row = $('<div></div>').addClass('row').append(col9, col3);
                td_4.html(row);
                tr.append(td_1, td_2, td_3, td_4);
                $(wrapper).append(tr);
                // $(wrapper).append('<tr><td><input type="text" class="form-control" name="hafalan[]" placeholder="Nama Surah" required></td><td><div class="row"><div class="col-md-11"><select class="form-control" name="predikat[]" required><option value="">== Pilih ==</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></div><div class="col-md-1"><button type="button" id="hapushafalan" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button></div></div></td></tr>');
            }
        });

        $(wrapper).on("click", "#hapushafalan", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            x--;
        });

        $(wrapper).on('change',"select[name^='jenis']",function(){
            if($(this).val() == 'surat'){
                var nextTd = $(this).parent().next();
                nextTd.children('select').first().prop("required", true);
                nextTd.children('select').first().show();
                nextTd.children('select').last().prop("required", false);
                nextTd.children('select').last().hide();
            }
            else if($(this).val() == 'juz'){
                var nextTd = $(this).parent().next();
                nextTd.children('select').first().prop("required", false);
                nextTd.children('select').first().hide();
                nextTd.children('select').last().prop("required", true);
                nextTd.children('select').last().show();
            }
        });
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>
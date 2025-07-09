<<<<<<< HEAD
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 999;
        var wrapper = $("table tbody");
        var add_button = $("#tambahhafalan");

        var x = {{ isset($capaian[$siswa->id]['quran']) && $capaian[$siswa->id]['quran'] > 0 ? $capaian[$siswa->id]['quran'] : 1 }};
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
				//console.log('Total input(s): '+x);
                var tr = $('<tr></tr>').addClass('new-surah');
                var td_1 = $('<td></td>').attr('width','20%');
                var selectJenis = $('<select></select>').addClass('form-control').attr('name','jenis[]').prop("required", true);
                var jenis = {"surat":"Surat","juz":"Juz"};
                $.each(jenis,function(key,value){
                    var option = $('<option></option>').attr('value',key).html(value);
                    selectJenis.append(option);
                });
                td_1.html(selectJenis);
                var td_2 = $('<td></td>').attr('width','55%');
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
				var inputAyat = $('<input></input>').addClass('form-control').attr({type: 'text',name: 'ayat[]',maxlength: '15',placeholder: 'Ayat'}).prop("required", true);
                var selectStatus = $('<select></select>').addClass('form-control').attr({name: 'status[]',style: 'display: none'}).prop("required", true);
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
                td_3.append(inputAyat, selectStatus);
                var td_4 = $('<td></td>').attr('width','5%');
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var deleteBtn = $('<button></button>').addClass('btn btn-sm btn-danger h-100').attr({
                    id: "hapushafalan",
                    type: "button",

                }).html(faTimes);
                td_4.html(deleteBtn);
                tr.append(td_1, td_2, td_3, td_4);
                $(wrapper).append(tr);
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
				var ayatTd = $(this).parent().nextAll().slice(1, 2);
                ayatTd.children('input').prop("required", true);
                ayatTd.children('input').show();
                ayatTd.children('select').prop("required", false);
                ayatTd.children('select').hide();
            }
            else if($(this).val() == 'juz'){
                var nextTd = $(this).parent().next();
                nextTd.children('select').first().prop("required", false);
                nextTd.children('select').first().hide();
                nextTd.children('select').last().prop("required", true);
                nextTd.children('select').last().show();
				var ayatTd = $(this).parent().nextAll().slice(1, 2);
                ayatTd.children('input').prop("required", false);
                ayatTd.children('input').hide();
                ayatTd.children('select').prop("required", true);
                ayatTd.children('select').show();
            }
        });
    });
=======
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 999;
        var wrapper = $("table tbody");
        var add_button = $("#tambahhafalan");

        var x = {{ isset($capaian[$siswa->id]['quran']) && $capaian[$siswa->id]['quran'] > 0 ? $capaian[$siswa->id]['quran'] : 1 }};
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
				//console.log('Total input(s): '+x);
                var tr = $('<tr></tr>').addClass('new-surah');
                var td_1 = $('<td></td>').attr('width','20%');
                var selectJenis = $('<select></select>').addClass('form-control').attr('name','jenis[]').prop("required", true);
                var jenis = {"surat":"Surat","juz":"Juz"};
                $.each(jenis,function(key,value){
                    var option = $('<option></option>').attr('value',key).html(value);
                    selectJenis.append(option);
                });
                td_1.html(selectJenis);
                var td_2 = $('<td></td>').attr('width','55%');
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
				var inputAyat = $('<input></input>').addClass('form-control').attr({type: 'text',name: 'ayat[]',maxlength: '15',placeholder: 'Ayat'}).prop("required", true);
                var selectStatus = $('<select></select>').addClass('form-control').attr({name: 'status[]',style: 'display: none'}).prop("required", true);
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
                td_3.append(inputAyat, selectStatus);
                var td_4 = $('<td></td>').attr('width','5%');
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var deleteBtn = $('<button></button>').addClass('btn btn-sm btn-danger h-100').attr({
                    id: "hapushafalan",
                    type: "button",

                }).html(faTimes);
                td_4.html(deleteBtn);
                tr.append(td_1, td_2, td_3, td_4);
                $(wrapper).append(tr);
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
				var ayatTd = $(this).parent().nextAll().slice(1, 2);
                ayatTd.children('input').prop("required", true);
                ayatTd.children('input').show();
                ayatTd.children('select').prop("required", false);
                ayatTd.children('select').hide();
            }
            else if($(this).val() == 'juz'){
                var nextTd = $(this).parent().next();
                nextTd.children('select').first().prop("required", false);
                nextTd.children('select').first().hide();
                nextTd.children('select').last().prop("required", true);
                nextTd.children('select').last().show();
				var ayatTd = $(this).parent().nextAll().slice(1, 2);
                ayatTd.children('input').prop("required", false);
                ayatTd.children('input').hide();
                ayatTd.children('select').prop("required", true);
                ayatTd.children('select').show();
            }
        });
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>
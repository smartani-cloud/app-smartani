<<<<<<< HEAD
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 999;
        var haditsWrapper = $("table.table-hadits tbody");
		var doaWrapper = $("table.table-doa tbody");

        var totalHadits = {{ isset($capaian[$siswa->id]['quran']) && $capaian[$siswa->id]['quran'] > 0 ? $capaian[$siswa->id]['quran'] : 1 }};
		var totalDoa = {{ isset($capaian[$siswa->id]['quran']) && $capaian[$siswa->id]['quran'] > 0 ? $capaian[$siswa->id]['quran'] : 1 }};
        $("#addHadits").click(function(e) {
            e.preventDefault();
            if (totalHadits < max_fields) {
                totalHadits++;
				//console.log('Total input(s): '+totalHadits);
                var tr = $('<tr></tr>').addClass('new-hadits');
                var td_1 = $('<td></td>').attr('width','95%');
				var inputHadits = $('<input></input>').addClass('form-control').attr({type: 'text',name: 'hadits[]',maxlength: '100',placeholder: 'Nama Hadits'}).prop("required", true);
                td_1.html(inputHadits);
                var td_2 = $('<td></td>').attr('width','5%');
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var removeBtn = $('<button></button>').addClass('btn btn-sm btn-danger btn-remove h-100').attr({
                    type: "button",
                }).html(faTimes);
                td_2.html(removeBtn);
                tr.append(td_1, td_2);
                $(haditsWrapper).append(tr);
            }
        });		
        $("#addDoa").click(function(e) {
            e.preventDefault();
            if (totalDoa < max_fields) {
                totalDoa++;
				//console.log('Total input(s): '+totalDoa);
                var tr = $('<tr></tr>').addClass('new-doa');
                var td_1 = $('<td></td>').attr('width','95%');
				var inputDoa = $('<input></input>').addClass('form-control').attr({type: 'text',name: 'doa[]',maxlength: '100',placeholder: 'Nama Doa'}).prop("required", true);
                td_1.html(inputDoa);
                var td_2 = $('<td></td>').attr('width','5%');
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var removeBtn = $('<button></button>').addClass('btn btn-sm btn-danger btn-remove h-100').attr({
                    type: "button",
                }).html(faTimes);
                td_2.html(removeBtn);
                tr.append(td_1, td_2);
                $(doaWrapper).append(tr);
            }
        });

        $(haditsWrapper).on("click", ".btn-remove", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            totalHadits--;
        });

        $(doaWrapper).on("click", ".btn-remove", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            totalDoa--;
        });
    });
=======
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 999;
        var haditsWrapper = $("table.table-hadits tbody");
		var doaWrapper = $("table.table-doa tbody");

        var totalHadits = {{ isset($capaian[$siswa->id]['quran']) && $capaian[$siswa->id]['quran'] > 0 ? $capaian[$siswa->id]['quran'] : 1 }};
		var totalDoa = {{ isset($capaian[$siswa->id]['quran']) && $capaian[$siswa->id]['quran'] > 0 ? $capaian[$siswa->id]['quran'] : 1 }};
        $("#addHadits").click(function(e) {
            e.preventDefault();
            if (totalHadits < max_fields) {
                totalHadits++;
				//console.log('Total input(s): '+totalHadits);
                var tr = $('<tr></tr>').addClass('new-hadits');
                var td_1 = $('<td></td>').attr('width','95%');
				var inputHadits = $('<input></input>').addClass('form-control').attr({type: 'text',name: 'hadits[]',maxlength: '100',placeholder: 'Nama Hadits'}).prop("required", true);
                td_1.html(inputHadits);
                var td_2 = $('<td></td>').attr('width','5%');
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var removeBtn = $('<button></button>').addClass('btn btn-sm btn-danger btn-remove h-100').attr({
                    type: "button",
                }).html(faTimes);
                td_2.html(removeBtn);
                tr.append(td_1, td_2);
                $(haditsWrapper).append(tr);
            }
        });		
        $("#addDoa").click(function(e) {
            e.preventDefault();
            if (totalDoa < max_fields) {
                totalDoa++;
				//console.log('Total input(s): '+totalDoa);
                var tr = $('<tr></tr>').addClass('new-doa');
                var td_1 = $('<td></td>').attr('width','95%');
				var inputDoa = $('<input></input>').addClass('form-control').attr({type: 'text',name: 'doa[]',maxlength: '100',placeholder: 'Nama Doa'}).prop("required", true);
                td_1.html(inputDoa);
                var td_2 = $('<td></td>').attr('width','5%');
                var faTimes = $('<i></i>').addClass('fa fa-times')
                var removeBtn = $('<button></button>').addClass('btn btn-sm btn-danger btn-remove h-100').attr({
                    type: "button",
                }).html(faTimes);
                td_2.html(removeBtn);
                tr.append(td_1, td_2);
                $(doaWrapper).append(tr);
            }
        });

        $(haditsWrapper).on("click", ".btn-remove", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            totalHadits--;
        });

        $(doaWrapper).on("click", ".btn-remove", function(e) {
            e.preventDefault();
            $(this).parents("tr").remove();
            totalDoa--;
        });
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>
<<<<<<< HEAD
<script type="text/javascript">
	function checkSelect(item){
		if(item.val() == 'surat'){
			var nextTd = item.parent().next();
			nextTd.children('select').first().prop("required", true).show();
			nextTd.children('select').last().prop("required", false).hide();
			var ayatTd = item.parent().nextAll().slice(1, 2);
			ayatTd.children('input').prop("required", true).show();
			ayatTd.children('select').prop("required", false).hide();
		}
		else if(item.val() == 'juz'){
			var nextTd = item.parent().next();
			nextTd.children('select').first().prop("required", false).hide();
			nextTd.children('select').last().prop("required", true).show();
			var ayatTd = item.parent().nextAll().slice(1, 2);
			ayatTd.children('input').prop("required", false).hide();
			ayatTd.children('select').prop("required", true).show();
		}
	}
    $(document).ready(function() {
		$('select[name="type"]').on('change',function(){
			if($(this).val() == 1){
			  $('.quran-row').show();
			  $('.quran-row td:first-child select').each(function(){
				checkSelect($(this));
			  });
			  $('.book-row').hide();
			  $('select[name="buku"]').prop("required", false);
			}else{
			  $('.quran-row').hide();
			  $('.quran-row > td > select, input').prop("required", false);
			  $('.book-row').show();
			  $('select[name="buku"]').prop("required", true);
			}
		});

        $(wrapper).on('change',"select[name^='jenis']",function(){
			checkSelect($(this));
        });
    });
=======
<script type="text/javascript">
	function checkSelect(item){
		if(item.val() == 'surat'){
			var nextTd = item.parent().next();
			nextTd.children('select').first().prop("required", true).show();
			nextTd.children('select').last().prop("required", false).hide();
			var ayatTd = item.parent().nextAll().slice(1, 2);
			ayatTd.children('input').prop("required", true).show();
			ayatTd.children('select').prop("required", false).hide();
		}
		else if(item.val() == 'juz'){
			var nextTd = item.parent().next();
			nextTd.children('select').first().prop("required", false).hide();
			nextTd.children('select').last().prop("required", true).show();
			var ayatTd = item.parent().nextAll().slice(1, 2);
			ayatTd.children('input').prop("required", false).hide();
			ayatTd.children('select').prop("required", true).show();
		}
	}
    $(document).ready(function() {
		$('select[name="type"]').on('change',function(){
			if($(this).val() == 1){
			  $('.quran-row').show();
			  $('.quran-row td:first-child select').each(function(){
				checkSelect($(this));
			  });
			  $('.book-row').hide();
			  $('select[name="buku"]').prop("required", false);
			}else{
			  $('.quran-row').hide();
			  $('.quran-row > td > select, input').prop("required", false);
			  $('.book-row').show();
			  $('select[name="buku"]').prop("required", true);
			}
		});

        $(wrapper).on('change',"select[name^='jenis']",function(){
			checkSelect($(this));
        });
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>
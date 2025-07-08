	<script>
    $('#periode_mulai').change(function(){
		if($('#periode_mulai').val()) $("#periode_selesai").removeAttr("disabled");
		$("#periode_selesai").attr("min",$('#periode_mulai').val());
	  });
    </script>

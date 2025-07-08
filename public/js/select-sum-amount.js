	function selectSumAmount(selectId,amountAttr,inputId){
		$(selectId).on('change', function(e){
			var value = 0;
			$("option:selected",this).each(function(){
				var input = $(this).attr(amountAttr);
				input = input.replace(/\./g, '');
				var thisValueInput = parseInt(input,10);
				value += thisValueInput;
				console.log('+'+thisValueInput);
			});
			$(inputId).val(numberWithCommas(value));
		});
	}
	
<script>
	function getTodayDate(){
		var today = new Date();
		var yyyy = today.getFullYear();
		let mm = today.getMonth() + 1; // Months start at 0!
		let dd = today.getDate();

		if (dd < 10) dd = '0' + dd;
		if (mm < 10) mm = '0' + mm;

		var today = dd + '-' + mm + '-' + yyyy;
		
		return today;
	}
</script>

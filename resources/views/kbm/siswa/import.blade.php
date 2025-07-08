<!DOCTYPE html>
<html>
<head>
	<title>Siswa Import Excel</title>
</head>
<body>

<form method="post" action="/siswa/import" enctype="multipart/form-data">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Siswa Import Excel</h5>
		</div>
		<div class="modal-body">

			@csrf
			{{bcrypt('password')}} <br>
			<label>Pilih file excel</label>
			<div class="form-group">
				<input type="file" name="file" required="required">
			</div>

		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary">Import</button>
		</div>
	</div>
</form>
<br>
<br>
<br>
<form method="post" action="{{route('ortu-ubah')}}">
	@csrf
	<label for="old">Old</label>
	<input type="text" name="old" id="old">
	<br>
	<label for="new">New</label>
	<input type="text" name="new" id="new">
	<br>
	<button type="submit" class="btn btn-primary">Ubah</button>
</form>
<form method="post" action="/siswa/va" enctype="multipart/form-data">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Va Import Excel</h5>
		</div>
		<div class="modal-body">

			@csrf

			<label>Pilih file excel</label>
			<div class="form-group">
				<input type="file" name="file" required="required">
			</div>

		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary">Import</button>
		</div>
	</div>
</form>
<br>
<br>
<a href="{{route('bms-generator.reset')}}">click to reset bms calon</a>
<a href="{{route('bms-generator.generate')}}">click to generate bms calon</a>
<br>
@if (session()->has('success'))
{{ session()->get('success') }}
@endif
</body>
</html>
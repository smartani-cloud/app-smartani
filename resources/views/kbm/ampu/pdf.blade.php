<<<<<<< HEAD
<!DOCTYPE html>
<html>
<head>
	<title>PDF</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>{{$kelas->unit->name}} ISLAM TERPADU AULIYA</h4>
	</center>


    <h6>Nama Kelas : {{$kelas->level->level}} {{$kelas->namakelases->class_name}}</h6>
    <h6>Wali Kelas : {{$kelas->walikelas->name}}</h6>
    <h6>Tahun Pelajaran : {{$kelas->tahunajaran->academic_year}}</h6>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th><h6>No</h6></th>
				<th><h6>Nama</h6></th>
				<th><h6>NISN</h6></th>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($siswas as $index => $siswa)
			<tr>
				<td><h6>{{ $index+1 }}</h6></td>
				<td><h6>{{$siswa->identitas->student_name}}</h6></td>
				<td><h6>{{$siswa->identitas->student_nisn}}</h6></td>
			</tr>
			@endforeach
		</tbody>
	</table>
 
</body>
=======
<!DOCTYPE html>
<html>
<head>
	<title>PDF</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>{{$kelas->unit->name}} ISLAM TERPADU AULIYA</h4>
	</center>


    <h6>Nama Kelas : {{$kelas->level->level}} {{$kelas->namakelases->class_name}}</h6>
    <h6>Wali Kelas : {{$kelas->walikelas->name}}</h6>
    <h6>Tahun Pelajaran : {{$kelas->tahunajaran->academic_year}}</h6>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th><h6>No</h6></th>
				<th><h6>Nama</h6></th>
				<th><h6>NISN</h6></th>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($siswas as $index => $siswa)
			<tr>
				<td><h6>{{ $index+1 }}</h6></td>
				<td><h6>{{$siswa->identitas->student_name}}</h6></td>
				<td><h6>{{$siswa->identitas->student_nisn}}</h6></td>
			</tr>
			@endforeach
		</tbody>
	</table>
 
</body>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</html>
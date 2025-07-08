@foreach($desa as $d)
{{ $d->code }} : {{ $d->name }}, {{ $d->kecamatanName() }}, {{ $d->kabupatenName() }}, {{ $d->provinsiName() }}<br>
@endforeach
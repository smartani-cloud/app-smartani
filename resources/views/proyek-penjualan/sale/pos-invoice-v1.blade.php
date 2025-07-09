<<<<<<< HEAD
@extends('template.print.A4.master')

@section('title')
Invoice - {{ $data->name }}
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/invoice.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/invoice.css') }}" rel="stylesheet" type="text/css" media="print">
<style>
body {
	-webkit-print-color-adjust: exact;
}
</style>
@endsection

@section('content')
<div class="watermark">
	<div class="page">
		<div class="subpage">
			<div id="invoiceHeader">
				<table class="table-header page-break-auto">
					<tr>
						<td>
							<img src="{{ asset('img/logo/logo.png') }}" alt="logo" width="124" height="124">
						</td>
						<td>
							<span class="d-block lh-1-5 text-uppercase font-weight-bold fs-brand">Slow Blow</span>
							<span class="d-block lh-1-5">Gedung Graha Krama Yudha Lantai 4 Unit B, Duren Tiga, Kec. Pancoran, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12540</span>
							<span class="d-block lh-1-5">08558824806</span>
							<span class="d-block lh-1-5">muda@incomso.com</span>
						</td>
					</tr>
				</table>
			</div>
			<hr class="header-separator m-b-20">
			<p class="text-center text-uppercase font-weight-bold fs-title">INVOICE</p>
			<div id="invoiceData" class="m-t-36">
				<table>
					<tr>
						<td class="align-top" style="width: 67%">
							<span class="d-block lh-1-4 font-weight-bold">Bill To</span>
							<span class="d-block lh-1-4 font-weight-bold">{{ $data->buyer->billing_to }}</span>
							<span class="d-block lh-1-4">Nama Penerima : {{ $data->buyer->shipping_to }}</span>
							<span class="d-block lh-1-4">Alamat Penerima : {{ $data->buyer->shippingAddress }}
							<span class="d-block lh-1-4">Kode Pos {{ $data->buyer->shipping_postal_code }}</span>
							<span class="d-block lh-1-4">Nomor Telepon Penerima : {{ $data->buyer->shippingPhoneNumberLocal }}</span>
							<span class="d-block lh-1-4">{{ $data->buyer->billingPhoneNumberId }}</span>
						</td>
						<td class="align-top text-right font-weight-bold text-brand-primary" style="width: 33%">
							<span class="d-block fs-number">#{{ $data->date ? $data->name : null }}</span>
							<span class="d-block fs-date">{!! $data->date ? $data->date : '&nbsp;' !!}</span>
						</td>
					</tr>
				</table>
			</div>
			<div id="invoiceDetails" class="m-t-32">
				<div class="m-t-16 m-b-16">
					<table class="table-details page-break-auto">
						<tr>
							<th class="text-center" style="width: 5%;white-space: nowrap !important;">
								Sr no.
							</th>
							<th class="text-left" style="width: 50%">
								Product
							</th>
							<th class="text-right" style="width: 15%">
								Qty
							</th>
							<th class="text-right" style="width: 15%">
								Rate
							</th>
							<th class="text-right" style="width: 15%">
								Amount
							</th>
						</tr>
			            @php
			            $no = 1;
			            @endphp
			            @foreach($data->details as $d)
			            <tr>
			                <td class="text-center">{{ $no++ }}</td>
			                <td>{{ $d->productSalesType->product->name }}</td>
			                <td class="text-right">{{ $d->quantityWithSeparator }}</td>
			                <td class="text-right">{{ $d->priceWithSeparator }}</td>
			                <td class="text-right">{{ $d->subtotalWithSeparator }}</td>
						</tr>
			             @endforeach
					</table>
				</div>
				<div id="invoiceTotal" class="m-t-13">
					<table class="table-total">
						<tr>
							<th class="text-left" style="width: 53%">
								<b>Please Note</b>
							</th>
							<td style="width: 5%">
								&nbsp;
							</td>
							<th class="text-left" style="width: 21%">
								<b>Total</b>
							</th>
							<th class="text-right" style="width: 21%">
								<b>{{ 'Rp '.$data->totalAmountWithSeparator }}</b> 
							</th>
						</tr>
					</table>
				</div>
				<div id="invoiceGrandTotal" class="m-t-3">
					<table class="table-grand-total">
						<tr>
							<td style="width: 53%">
								&nbsp;
							</td>
							<td style="width: 5%">
								&nbsp;
							</td>
							<td class="text-white" style="width: 21%;border-bottom: 0.2mm solid black;background-color: var(--brand-primary);">
								Grand Total
							</td>
							<td class="text-right text-white" style="width: 21%;border-bottom: 0.2mm solid black;background-color: var(--brand-primary);">
								{{ 'Rp '.$data->totalAmountWithSeparator }}
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								Balance
							</td>
							<td class="text-right">
								{{ 'Rp '.$data->totalAmountWithSeparator }}
							</td>
						</tr>
					</table>
				</div>
				<div id="invoiceSignature" class="m-t-105">
					<table class="table-signature">
						<tr>
							<td>
								<span class="d-block lh-1-3">Yongki Kurniawan</span>
								<span class="d-block lh-1-3 font-weight-bold">Signature</span>
							</td>
						</tr>
					</table>
				</div>
				<div id="invoiceBanking" class="m-t-52">
					<table class="table-banking">
						<tr>
							<td style="width: 50%">
								<span class="d-block lh-1-5 font-weight-bold">Payable To</span>
								<span class="d-block lh-1-5">Yongki Kurniawan</span>
							</td>
							<td style="width: 50%">
								<span class="d-block lh-1-5 font-weight-bold">Banking Details</span>
								<span class="d-block lh-1-5">BCA 0887054626</span>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
=======
@extends('template.print.A4.master')

@section('title')
Invoice - {{ $data->name }}
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/invoice.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/invoice.css') }}" rel="stylesheet" type="text/css" media="print">
<style>
body {
	-webkit-print-color-adjust: exact;
}
</style>
@endsection

@section('content')
<div class="watermark">
	<div class="page">
		<div class="subpage">
			<div id="invoiceHeader">
				<table class="table-header page-break-auto">
					<tr>
						<td>
							<img src="{{ asset('img/logo/logo.png') }}" alt="logo" width="124" height="124">
						</td>
						<td>
							<span class="d-block lh-1-5 text-uppercase font-weight-bold fs-brand">Slow Blow</span>
							<span class="d-block lh-1-5">Gedung Graha Krama Yudha Lantai 4 Unit B, Duren Tiga, Kec. Pancoran, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12540</span>
							<span class="d-block lh-1-5">08558824806</span>
							<span class="d-block lh-1-5">muda@incomso.com</span>
						</td>
					</tr>
				</table>
			</div>
			<hr class="header-separator m-b-20">
			<p class="text-center text-uppercase font-weight-bold fs-title">INVOICE</p>
			<div id="invoiceData" class="m-t-36">
				<table>
					<tr>
						<td class="align-top" style="width: 67%">
							<span class="d-block lh-1-4 font-weight-bold">Bill To</span>
							<span class="d-block lh-1-4 font-weight-bold">{{ $data->buyer->billing_to }}</span>
							<span class="d-block lh-1-4">Nama Penerima : {{ $data->buyer->shipping_to }}</span>
							<span class="d-block lh-1-4">Alamat Penerima : {{ $data->buyer->shippingAddress }}
							<span class="d-block lh-1-4">Kode Pos {{ $data->buyer->shipping_postal_code }}</span>
							<span class="d-block lh-1-4">Nomor Telepon Penerima : {{ $data->buyer->shippingPhoneNumberLocal }}</span>
							<span class="d-block lh-1-4">{{ $data->buyer->billingPhoneNumberId }}</span>
						</td>
						<td class="align-top text-right font-weight-bold text-brand-primary" style="width: 33%">
							<span class="d-block fs-number">#{{ $data->date ? $data->name : null }}</span>
							<span class="d-block fs-date">{!! $data->date ? $data->date : '&nbsp;' !!}</span>
						</td>
					</tr>
				</table>
			</div>
			<div id="invoiceDetails" class="m-t-32">
				<div class="m-t-16 m-b-16">
					<table class="table-details page-break-auto">
						<tr>
							<th class="text-center" style="width: 5%;white-space: nowrap !important;">
								Sr no.
							</th>
							<th class="text-left" style="width: 50%">
								Product
							</th>
							<th class="text-right" style="width: 15%">
								Qty
							</th>
							<th class="text-right" style="width: 15%">
								Rate
							</th>
							<th class="text-right" style="width: 15%">
								Amount
							</th>
						</tr>
			            @php
			            $no = 1;
			            @endphp
			            @foreach($data->details as $d)
			            <tr>
			                <td class="text-center">{{ $no++ }}</td>
			                <td>{{ $d->productSalesType->product->name }}</td>
			                <td class="text-right">{{ $d->quantityWithSeparator }}</td>
			                <td class="text-right">{{ $d->priceWithSeparator }}</td>
			                <td class="text-right">{{ $d->subtotalWithSeparator }}</td>
						</tr>
			             @endforeach
					</table>
				</div>
				<div id="invoiceTotal" class="m-t-13">
					<table class="table-total">
						<tr>
							<th class="text-left" style="width: 53%">
								<b>Please Note</b>
							</th>
							<td style="width: 5%">
								&nbsp;
							</td>
							<th class="text-left" style="width: 21%">
								<b>Total</b>
							</th>
							<th class="text-right" style="width: 21%">
								<b>{{ 'Rp '.$data->totalAmountWithSeparator }}</b> 
							</th>
						</tr>
					</table>
				</div>
				<div id="invoiceGrandTotal" class="m-t-3">
					<table class="table-grand-total">
						<tr>
							<td style="width: 53%">
								&nbsp;
							</td>
							<td style="width: 5%">
								&nbsp;
							</td>
							<td class="text-white" style="width: 21%;border-bottom: 0.2mm solid black;background-color: var(--brand-primary);">
								Grand Total
							</td>
							<td class="text-right text-white" style="width: 21%;border-bottom: 0.2mm solid black;background-color: var(--brand-primary);">
								{{ 'Rp '.$data->totalAmountWithSeparator }}
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								Balance
							</td>
							<td class="text-right">
								{{ 'Rp '.$data->totalAmountWithSeparator }}
							</td>
						</tr>
					</table>
				</div>
				<div id="invoiceSignature" class="m-t-105">
					<table class="table-signature">
						<tr>
							<td>
								<span class="d-block lh-1-3">Yongki Kurniawan</span>
								<span class="d-block lh-1-3 font-weight-bold">Signature</span>
							</td>
						</tr>
					</table>
				</div>
				<div id="invoiceBanking" class="m-t-52">
					<table class="table-banking">
						<tr>
							<td style="width: 50%">
								<span class="d-block lh-1-5 font-weight-bold">Payable To</span>
								<span class="d-block lh-1-5">Yongki Kurniawan</span>
							</td>
							<td style="width: 50%">
								<span class="d-block lh-1-5 font-weight-bold">Banking Details</span>
								<span class="d-block lh-1-5">BCA 0887054626</span>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection
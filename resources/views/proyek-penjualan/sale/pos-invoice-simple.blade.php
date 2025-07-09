<html>
<head>
	<title>Order Details</title>
	<link href="{{ asset('css/docs/util.css') }}" rel="stylesheet" type="text/css">
	<style>
		:root{
			--brand-primary:#5487ea;
		}
		body {
			-webkit-print-color-adjust: exact;
		}

		.d-block {display: block;}

		.fs-title {font-size: 22pt;}
		.fs-number {font-size: 17pt;}
		.fs-date {font-size: 11.5pt;}

		.text-brand-primary {color: var(--brand-primary);}

		hr.header-separator {
			border: 1px solid black;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			page-break-inside: auto
		}

		tr { page-break-inside: avoid; page-break-after: auto }

		table tr td {
			padding: 5px 0;
		}

		table.table-header td, table.table-header th {
			border: 0;
		}

		table.table-header tr td, table.table-header tr th {
			vertical-align: top
		}

		table.table-details td, table.table-details th {
			border: 0.2mm solid #c9c9c9;
			font-weight: 700!important
		}

		table.table-details tr th {
			color: white;
			background-color: var(--brand-primary);
		}

		table.table-details tr td, table.table-details tr th {
			padding: 5px;
			vertical-align: top
		}

		table.table-total th {
			border: 0.2mm solid var(--brand-primary);
			border-left-style: none;
			border-right-style: none;
		}

		table.table-total tr td, table.table-total tr th {
			padding: 5px;
			vertical-align: top
		}

		table.table-grand-total td, table.table-grand-total th {
			border: 0;
		}

		table.table-grand-total tr td, table.table-grand-total tr th {
			padding: 5px;
			vertical-align: top
		}

		table.table-signature {
			width: auto;
			border-collapse: collapse;
			margin-left: auto; 
			margin-right: 0;
		}

		table.table-signature td, table.table-signature th {
			border: 0;
		}

		table.table-signature tr td, table.table-signature tr th {
			padding: 0;
			text-align: center;
			vertical-align: middle
		}

		table.table-banking td, table.table-banking th {
			border: 0.2mm solid #c9c9c9;
		}

		table.table-banking tr td, table.table-banking tr th {
			padding: 8px;
			text-align: left;
			vertical-align: middle
		}
	</style>
</head>
<body>
	<div style="font-family:sans-serif; font-size:8.5pt; width:900px; margin:0 auto; ">
		<div>
			<div id="invoiceHeader">
				<table class="table-header page-break-auto">
					<tr>
						<td>
							<img src="{{ asset('img/logo/logo.png') }}" alt="logo" width="112" height="112">
						</td>
						<td>
							<span class="d-block lh-1-5 text-uppercase font-weight-bold fs-number">Slow Blow</span>
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
						<table class="table-details">
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
							@for($i=1;$i<45;$i++)
							@foreach($data->details as $d)
							<tr>
								<td class="text-center">{{ $no++ }}</td>
								<td>{{ $d->productSalesType->product->name }}</td>
								<td class="text-right">{{ $d->quantityWithSeparator }}</td>
								<td class="text-right">{{ $d->priceWithSeparator }}</td>
								<td class="text-right">{{ $d->subtotalWithSeparator }}</td>
							</tr>
							@endforeach
							@endfor
						</table>
					</div>
					<div id="invoiceTotal" class="m-t-13">
						<table class="table-total page-break-auto">
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
						<table class="table-grand-total page-break-auto">
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
						<table class="table-signature page-break-auto">
							<tr>
								<td>
									<span class="d-block lh-1-3">Yongki Kurniawan</span>
									<span class="d-block lh-1-3 font-weight-bold">Signature</span>
								</td>
							</tr>
						</table>
					</div>
					<div id="invoiceBanking" class="m-t-52">
						<table class="table-banking page-break-auto">
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
</body>
</html>
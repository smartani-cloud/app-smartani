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
						<td class="text-center" style="margin-bottom: 2px;">
						    <img src="{{ asset('img/logo/logo-black.png') }}" alt="logo" width="30" height="30">
							<span class="d-block lh-1-5 text-uppercase font-weight-bold fs-brand">DIGIYOK</span>
							<span class="d-block lh-1-5">Gedung Graha Krama Yudha Lantai 4 Unit B, Duren Tiga, Kec. Pancoran, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12540</span>
							<span class="d-block lh-1-5">08558824806</span>
							<span class="d-block lh-1-5">muda@incomso.com</span>
						</td>
					</tr>
						
				</table>
			</div>
			<hr class="header-separator" class="m-b-20">
			<div id="invoiceData" class="m-t-8">
				<table>
					<tr>
					    <td class="align-top" style="width: 33%">
                            <span class="text-left lh-1-4"><b>Invoice No :</b></span>
                            <span class="text-left lh-1-4"><b>Order To  :</b></span>
                            <span class="text-left lh-1-4"><b>Date  :</b></span>
                        </td>

						<td class="align-top lh-1-4" style="width: 67%">
						    <span class="text-right d-block fs-number">Inv{{ $data->id }}</span>
							<span class="text-right d-block">{{ $data->buyer->billing_to }}</span>
							<span class="text-right d-block fs-date">{!! $data->date ? $data->datedmY : '&nbsp;' !!}</span>
						</td>
					</tr>
				</table>
			</div>
			<hr class="header-separator" class="m-b-20">
			<div id="invoiceDetails" class="m-t-8">
				<div class="m-t-8 m-b-8">
					<table>
					    @php
			            $no = 1;
			            @endphp
			            @foreach($data->details as $d)
					    <tr>
                        <td class="invoice-table-purchase">
                            <span class="text-left">{{ $d->quantityWithSeparator }}</span>
                            <span class="text-left">{{ $d->productSalesType->product->name}}</span>
                            <span class="text-right">{{ $d->priceWithSeparator }}</span>
                            <span class="text-right">{{ $d->subtotalWithSeparator }}</span>
                        </td>
                        </tr>
                        
			             @endforeach
					</table>
				</div>
				<hr class="header-separator" class="m-b-20">
				<div id="invoiceTotal" class="m-t-8">
					<table>
						<tr>
							<td class="invoice-total-1">
                                <span class="text-left lh-1-4"><b>Total</b></span>
                                <span class="text-right lh-1-4 d-block font-weight-bold">{{ 'Rp '.$data->totalAmountWithSeparator}}</span>
                            </td>
                            
                            <td class="invoice-total-2">
                                <span class="text-left lh-1-4"><b>Grand Total</b></span>
                                <span class="text-right lh-1-4 d-block font-weight-bold">{{ 'Rp '.$data->totalAmountWithSeparator}}</span>
							</td>
						</tr>
					</table>
				</div>
				
				<div id="invoiceTotal" class="m-t-8">
					<table>
						@if($data->paymentType && $data->paymentType->name == "Berkala")
						@foreach($data->payments()->orderBy('date')->get() as $d)
						<tr>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								(-) Paid ({{$d->datedmY}})
							</td>
							<td class="text-right">
								{{ 'Rp '.$d->valueWithSeparator }}
							</td>
						</tr>
						@endforeach
						@endif
						<tr>
							</td>
							<td class="balance"><b>Balance</b></td>
							<td class="balance text-right d-block font-weight-bold">
								@php
								$balance = $data->paymentType && $data->paymentType->name == "Berkala" ? $data->billWithSeparator : $data->totalAmountWithSeparator;
								@endphp
								{{ 'Rp '.$balance }}
							</td>
						</tr>
						@if($data->refund)
						<tr>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
							<td class="balance"><b>Deposit</b></td>
							<td class="balance text-right d-block font-weight-bold">
								{{ 'Rp '.$data->refundWithSeparator }}
							</td>
						</tr>
						@endif
					</table>
				</div>
			<hr class="header-separator" class="m-b-20">			
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
@endsection
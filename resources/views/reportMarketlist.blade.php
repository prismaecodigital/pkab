<!DOCTYPE html>
<html>
<head>
	<title>Report Pendaftar</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
	<style>
        body p {
            font-size: 12px;
            
            margin-bottom: 0px;
        }
        body h6 {
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 5px;
        }
		.table > thead > th , .table > tbody > tr > td {
			font-size: 12px;
		}
        .table.no-border,
        .table.no-border td {
        border: none;
        }
	</style>
</head>
<body>

	<div style="text-align: right; padding-bottom:10px">
		{{-- <img src="" width="30%" alt="logo UKDW"> --}}
	</div>


	<center style="padding-bottom: 19px">
		<h4>Data Marketlist</h4>
		<p style="font-size: 12px; margin-bottom: 0px">{{ $bu }} - {{ $site }}</p>
		<p style="font-size: 12px; margin-bottom: 0px">{{ $startDate }} - {{ $endDate }}</p>
        <p style="font-size: 10px">Dokumen ini dibuat pada : {{ $now }}</p>
	</center>

    <hr>

@foreach ($marketlists as $index => $marketlist)
<h4> {{$index+1}}. Marketlist {{$marketlist->code}}</h4>
<div class="table-responsive">
    <table class="table no-border">
        <tr>
            <td> <h6>Tanggal dibuat : {{$marketlist->created_at}}</h6> </td>
            <td> <h6>Event: {{$marketlist->event}}</h6> </td>
            <td> <h6>User : {{$marketlist->user->name}}</h6> </td>
            <td> <h6>Status : {{$marketlist->status_label}}</h6> </td>
        </tr>
    </table>
</div>

<div class="table-responsive">
    <table class="table no-border">
        <thead>
            <th>Item</th>
            <th>Tanggal dibutuhkan</th>
            <th>Qty</th>
            <th>Satuan</th>
            <!-- <th>Tanggal diterima</th>
            <th>Qty diterima</th> -->
        </thead>
        <tbody>
            @foreach ($marketlist->items as $item)
            <tr>
                <td>{{$item->item->name}}</td>
                <td>{{$item->required_date_dfy}}</td>
                <td>{{$item->qty}}</td>
                <td>{{$item->satuan}}</td>
                <!-- <td>{{$item->approved_date_dfy ?? '-'}}</td>
                <td>{{$item->approved_qty ?? '-'}}</td> -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="page-break-after: always;"></div>
@endforeach

	<br>
	{{-- Some text --}}
	<b></b> 
	<p style="font-size: 10pt"></p>

	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>
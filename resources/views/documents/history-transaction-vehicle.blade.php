<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>History Transaction Vehicle</title>
    <style type="text/css">
        table.page_header { 
            width: 100%; 
            border: none;
            background-color: #F8F8FF;
            border-bottom: solid 1mm #696969;
            padding: 1mm;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        header { 
            position: fixed; 
            top: -20px; 
            left: 0px; 
            right: 0px;  
            height: 50px; 
        }
        footer { 
            border-top: solid 1mm #696969;
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px;  
            height: 50px; 
            text-align: center; 
            color: #808080;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 12px;
        }
        main {
            margin-top: 85px;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        main h4 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .table-content {
            width: 100%;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            border-collapse: collapse; 
        }
        .table-content th {
            background-color: #dcd8d8;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 12px;
            height: 30px;
            vertical-align: middle;
        }
        .table-content tr {
            border: solid 1px #dcd8d8;
            border-collapse: collapse; 
        }
        .table-content td {
            padding: 5px 5px;
            border: solid 1px #dcd8d8;
            border-collapse: collapse; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 11px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <header>
        <table class="page_header">
            <tr>
                <td style="text-align: left; width: 10%; margin-right:0px;">
                    <img src="{{ public_path('image/logo-png.png') }}" style="width: 70px;height: 70px">
                </td>
                <td style="text-align: left; width: 65%; margin-left: 0px;">
                    <b style="font-size: 14px">INT AUTOCARE</b><br>
                    <span style="font-size: 9px">
                        Jl. Gandu<br>
                        Gandu, Sendangtirto<br>
                        Berbah, Sleman<br>
                        D.I Yogyakarta<br>
                        0821-3782-5012<br>
                    </span>
                </td>
                <td style="text-align: right; width: 25%; font-size: 16px"><b>HISTORY SERVICE</b></td>
            </tr>
        </table>
    </header>
    <footer>
        <p>Copyright © {{ date('Y') }} - INT AUTOCARE</p>
    </footer>
    <main>
        <h4>HISTORY SERVICE<br>{{ $vehicle->license_plate.' / '.$carType->name }}</h4>
        <table class="table-content">
            <tr>
                <th>Tanggal</th>
                <th>No Invoice</th>
                <th>KM</th>
                <th>Keluhan</th>
                <th>Solusi</th>
                <th>Sparepart</th>
                <th>Catatan</th>
            </tr>
            @if ($vehicle->workOrder->count() > 0)
                @foreach ($vehicle->workOrder as $key => $item)
                    <tr>
                        <td>{{ ($item->serviceInvoice) ? Carbon\Carbon::parse($item->serviceInvoice->created_at)->format('d F Y') : '' }}</td>
                        <td>{{ ($item->serviceInvoice) ? $item->serviceInvoice->transaction_code : ''}}</td>
                        <td>{{ $item->km }}</td>
                        <td style="padding-left:20px">
                            @if ($item->serviceRequest->count() > 0)
                                @foreach ($item->serviceRequest as $request)
                                    <li>{{ $request->request }}</li>
                                @endforeach
                            @endif
                        </td>
                        <td style="padding-left:20px">
                            @if ($item->serviceRequest->count() > 0)
                                @foreach ($item->serviceRequest as $solution)
                                    <li>{{ $solution->solution }}</li>
                                @endforeach
                            @endif
                        </td>
                        <td style="padding-left:20px">
                            @if ($item->sellSparepartDetail->count() > 0)
                                @foreach ($item->sellSparepartDetail as $part)
                                    <li>{{ ($part->sparepart) ? $part->sparepart->name : '' }}</li>
                                @endforeach
                            @endif
                        </td>
                        <td>{!! $item->remark !!}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    </main>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hand Over Document</title>
    <style type="text/css">
        table.page_header { 
            width: 100%; 
            border: none;
            background-color: #F8F8FF;
            border-bottom: solid 1mm #696969;
            padding: 1mm;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .table-header { 
            border-collapse: collapse; 
            border-spacing: 0; 
            width: 100%; 
            /* border: 1px solid #ddd;  */
            vertical-align: top; 
            margin-top: 85px;
        }
		.table-header th { 
            padding: 8px 5px; 
            font-size: 11px; 
            background-color: #D3D3D3; 
            text-align: center; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
		.table-header td { 
            padding: 5px 5px; 
            font-size: 11px; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .request { 
            width: 100%;
            border: 1px;
            font-size: 11px;
            border-collapse: collapse;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .request tbody {
            page-break-after: auto; 
            page-break-before: auto; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .request tbody:last-child { 
            page-break-after: never; 
        }
        .request th {
            border: 1px; 
            height: 20px; 
            text-align: center;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .request td {
            border: 1px; 
            height: 15px;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .line {
            width: 100%;
            border-top: 1px solid #000;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        .page {
            /* page-break-after: auto; */
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
        .table-ttd {
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <table class="page_header">
            <tr>
                <td style="text-align: left; width: 10%;">
                    <img src="{{ public_path('image/logo-png.png') }}" style="width: 60px;height: 60px">
                </td>
                <td style="text-align: left; width: 65%;">
                    <b style="font-size: 14px">INT AUTOCARE</b><br>
                    <span style="font-size: 9px">
                        Jl. Gandu<br>
                        Gandu, Sendangtirto<br>
                        Berbah, Sleman<br>
                        D.I Yogyakarta<br>
                        0821-3782-5012<br>
                    </span>
                </td>
                <td style="text-align: right; width: 25%; font-size: 14px"><b>Bukti Penyerahan Mobil</b></td>
            </tr>
        </table>
    </header>
    <footer>
        <p>Copyright © {{ date('Y') }} - INT AUTOCARE</p>
    </footer>
    <main>
        <table class="table-header">
            <tr>
                <td><b>Kepada :</b></td>
                <td colspan="2"><b>Detail Kendaraan :</b></td>
                <td align="right"><b>Tgl Bukti : {{ Carbon\Carbon::parse($handOver->created_at)->format('d-M-Y') }}</b></td>
            </tr>
            <tr>
                <td style="width: 34%">
                    <b>{{ ($handOver->vehicle) ? (($handOver->vehicle->customer) ? $handOver->vehicle->customer->name : '') : ''}}</b><br>
                    {{ ($handOver->vehicle) ? (($handOver->vehicle->customer) ? $handOver->vehicle->customer->address : '') : ''}}<br>
                    {{ ($handOver->vehicle) ? (($handOver->vehicle->customer) ? $handOver->vehicle->customer->phone : '') : ''}}<br>
                </td>
                <td style="width: 11%">
                    Jenis Mobil<br>
                    Transmisi<br>
                    No. Polisi<br>
                    No. Rangka <br>
                    No. Mesin <br>
                    Warna
                </td>
                @if ($handOver->vehicle)
                    <td style="width: 22%" colspan="2">
                        :  {{ ($handOver->vehicle->carType) ? (($handOver->vehicle->carType->carBrand) ? $handOver->vehicle->carType->carBrand->name : '') : '' }} - {{ ($handOver->vehicle->carType) ? $handOver->vehicle->carType->name : '' }}<br>
                        : {{ $handOver->vehicle->transmission }}<br>
                        : {{ $handOver->vehicle->license_plate }}<br>
                        : {{ $handOver->vehicle->chassis_no }}<br>
                        : {{ $handOver->vehicle->engine_no }}<br>
                        : {{ $handOver->vehicle->color }}<br>
                    </td>
                @endif
            </tr>
        </table>
        <h4>Detail Request</h4>
        <table class="request">
            <tr style="background-color: #d3d3d3">
                <th style="width: 10%">#</th>
                <th style="width: 90%">Request</th>
            </tr>
            @if ($handOver->handOverRequest->count() > 0)
                @foreach ($handOver->handOverRequest as $key => $req)
                    <tr>
                        <td style="width: 10%; text-align: center">{{ $key+1 }}</td>
                        <td style="width: 90%; margin-left:5px;">{{ $req->request }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #d3d3d3">
                    <th colspan="2"></th>
                </tr>
            @endif
        </table>
        <div style="margin-top: 30px; margin-left:35px;">
            <p style="font-size: 12px" class="table-ttd">Yogyakarta, {{ date('d F Y') }}</p>
        </div>
        <div style="margin-top: 15px; margin-left: 35px;">
            <table class="table-ttd">
                <tr>
                    <td style="width: 380px;">Hormat Kami</td>
                    <td style="width: 250px; text-align:center;">Pelanggan</td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 3px; margin-left: 35px;">
            <table class="table-ttd">
                <tr>
                    <td style="width: 250px;">
                        <img src="data:image/png;base64,{{ $qrcode_ttd }}" style="width: 50; height:50">
                    </td>
                    <td style="width: 250px; text-align:center;"></td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 3px; margin-left: 35px;">
            <table class="table-ttd">
                <tr>
                    <td style="width: 380px;">(INT AutoCare)</td>
                    <td style="width: 250px; text-align:center;">(...........................................)</td>
                </tr>
            </table>
        </div>
    </main>
</body>
</html>
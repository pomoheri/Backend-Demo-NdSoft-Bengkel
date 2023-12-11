<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Service Invoice</title>
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
            margin-top: 95px;
            font-family: 'Helvetica Neue', Helvetica, Arial;
        }
        main h4 {
            text-align: center;
            margin-bottom: 10px;
        }
        .table-content {
            width: 100%;
            text-align: center;
            border-spacing: 0.5em;
        }
        .table-content th {
            width: 23%;
            text-align: left;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-weight: normal;
            font-size: 12px;
            padding: 5px;
            font-style: bold;
        }
        .table-content td {
            text-align: left;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-weight: normal;
            font-size: 12px;
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
        .detail-request {
            margin-top: 5px;
            margin-bottom: 2px;
        }
        .tbl-request {
            border: 1px solid;
            border-collapse: collapse;
        }

        .tbl-request th {
            border: 1px solid;
            border-collapse: collapse;
        }
        .tbl-request td {
            border: 1px solid;
            border-collapse: collapse;
            padding: 8px 5px; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 10px;
            text-align: left;
        }
        .tbl-request {
            width: 100%;
        }
        .tbl-request th { 
            padding: 8px 5px; 
            text-align: center; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 12px;
        }
        .detail-invoice {
            margin-top: 15px;
            margin-bottom: 2px;
        }
        .tbl-invoice {
            width: 100%;
        }
        .tbl-invoice, th {
            border-collapse: collapse;
        }
        .tbl-invoice th { 
            padding: 8px 5px; 
            text-align: center; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 12px;
            background-color: #d3d3d3;
        }
        .tbl-invoice td {
            border-collapse: collapse;
            padding: 8px 5px; 
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 10px;
            text-align: left;
        }
        .table-ttd {
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-size: 12px;
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
                    <b style="font-size: 14px">YOUR COMPANY</b><br>
                    <span style="font-size: 9px">
                        Your Address Bar<br>
                        Klaten<br>
                        Klaten<br>
                        Klaten<br>
                        0838-6955-0401<br>
                    </span>
                </td>
                <td style="text-align: right; width: 25%; font-size: 14px"><b>INVOICE SERVICE</b></td>
            </tr>
        </table>
    </header>
    <footer>
        <p>Copyright © {{ date('Y') }} - YOUR COMPANY</p>
    </footer>
    <main>
        <table class="table-header">
            <tr>
                <td><b>Kepada :</b></td>
                <td colspan="2"><b>Detail Kendaraan :</b></td>
                <td colspan="2"><b>No Invoice : {{ $invoice->transaction_code }}</b></td>
            </tr>
            <tr>
                <td style="width: 34%">
                    <b>{{ ($customer) ? $customer->name : '' }}</b><br>{{ ($customer) ? $customer->address : '' }}<br>
                    {{ ($customer) ? $customer->phone : '' }}<br>
                </td>
                <td style="width: 11%">
                    Jenis Mobil<br>
                    Transmisi<br>
                    No. Polisi<br>
                    No. Rangka <br>
                    No. Mesin <br>
                    Warna
                </td>
                @if ($vehicle)
                    <td style="width: 22%">
                        :  {{ ($vehicle->carType) ? (($vehicle->carType->carBrand) ? $vehicle->carType->carBrand->name : '') : '' }} - {{ ($vehicle->carType) ? $vehicle->carType->name : '' }}<br>
                        : {{ $vehicle->transmission }}<br>
                        : {{ $vehicle->license_plate }}<br>
                        : {{ $vehicle->chassis_no }}<br>
                        : {{ $vehicle->engine_no }}<br>
                        : {{ $vehicle->color }}<br>
                    </td>
                @endif
                <td style="width: 11%">
                    Tgl Invoice<br>
                    No. WO<br>
                    Tgl WO<br>
                    Kilometer<br>
                    Teknisi
                </td>
                @if ($vehicle)
                    <td style="width: 22%">
                        : {{ Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y') }}<br>
                        : {{ $workOrder->transaction_code }}<br>
                        : {{ Carbon\Carbon::parse($workOrder->created_at)->format('d-M-Y') }}<br>
                        : {{ $workOrder->km }}<br>
                        : {{ $workOrder->technician }}<br>
                    </td>
                @endif
            </tr>
        </table>
        <h6 class="detail-invoice">Detail Invoice</h6>
        <table class="tbl-invoice">
            <tr>
                <th>#</th>
                <th>Kode Item</th>
                <th>Nama Item</th>
                <th>Qty/FRT</th>
                <th>Harga (Rp.)</th>
                <th>Discount</th>
                <th>Total (Rp.)</th>
            </tr>
            @php
                $number = 0;
                $subtotallabour = 0;
                $subtotalpart = 0;
                $subtotalsublet = 0;
            @endphp
            @if ($workOrder->serviceLabour->count() > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold;">JASA SERVICE</td>
                    </tr>
                @foreach ($workOrder->serviceLabour as $keys => $labour)
                    <tr>
                        <td>{{ $number = $number + 1 }}</td>
                        <td>{{ ($labour->labour) ? $labour->labour->labour_code : '' }}</td>
                        <td>{{ ($labour->labour) ? $labour->labour->labour_name : '' }}</td>
                        <td style="text-align: right">{{ $labour->frt }}</td>
                        <td style="text-align: right">{{ ($labour->labour) ? number_format($labour->labour->price,2,',','.') : '' }}</td>
                        <td style="text-align: right">{{ $labour->discount.' %' }}</td>
                        <td style="text-align: right">{{ number_format($labour->subtotal,2,',','.') }}</td>
                    </tr>
                    @php
                        $subtotallabour += $labour->subtotal;
                    @endphp
                @endforeach
            @endif
            @if ($workOrder->sellSparepartDetail->count() > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold;">SPARE PART</td>
                    </tr>
                @foreach ($workOrder->sellSparepartDetail as $keys => $sparepart)
                    <tr>
                        <td>{{ $number = $number + 1 }}</td>
                        <td>{{ ($sparepart->sparepart) ? $sparepart->sparepart->part_number : '' }}</td>
                        <td>{{ ($sparepart->sparepart) ? $sparepart->sparepart->name : '' }}</td>
                        <td style="text-align: right">{{ $sparepart->quantity }}</td>
                        <td style="text-align: right">{{ ($sparepart->sparepart) ? number_format($sparepart->sparepart->selling_price,2,',','.') : '' }}</td>
                        <td style="text-align: right">{{ $sparepart->discount.' %' }}</td>
                        <td style="text-align: right">{{ number_format($sparepart->subtotal,2,',','.') }}</td>
                    </tr>
                    @php
                        $subtotalpart += $sparepart->subtotal;
                    @endphp
                @endforeach
            @endif
            @if ($workOrder->serviceSublet->count() > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold;">SUBLET</td>
                    </tr>
                @foreach ($workOrder->serviceSublet as $keyss => $sublet)
                    <tr>
                        <td>{{ $number = $number + 1 }}</td>
                        <td></td>
                        <td>{{ $sublet->sublet }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right">{{ number_format($sublet->subtotal,2,',','.') }}</td>
                    </tr>
                    @php
                        $subtotalsublet += $sublet->subtotal;
                    @endphp
                @endforeach
            @endif
            @php
                $total = $subtotalsublet+$subtotalpart+$subtotallabour;
            @endphp
            @if ($total > 0)
            <tr>
                <th colspan="6" style="text-align: right">Total (Rp.)</th>
                
                <th style="text-align: right">{{ number_format($total,2,',','.') }}</th>
            </tr>
            @endif
        </table>
        <div style="margin-top: 10px; margin-left:15px;">
            <p style="font-size: 10px; margin-top:15px;" class="table-ttd">Yogyakarta, {{ date('d F Y') }}</p>
        </div>
        <div style="margin-top: 2px; margin-left:15px;">
            <table class="table-ttd">
                <tr>
                    <td style="width: 250px;">
                        <img src="data:image/png;base64,{{ $qrcode_ttd }}" style="width: 50; height:50">
                    </td>
                </tr>
                <tr>
                    <td style="width: 250px;">
                        (INT AutoCare)
                    </td>
                </tr>
            </table>
        </div>
    </main>
</body>
</html>
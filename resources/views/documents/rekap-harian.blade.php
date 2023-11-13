<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Rekap Harian</title>
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
            font-size: 20px;
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
            border-radius: 10px;
            width: 23%;
        }
        .table-content th p {
            text-align: left;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-weight: normal;
            font-size: 14px;
            margin-left: 10px;
            margin-right: 10px;
            padding: 5px;
            border-bottom: 2px solid #696969;
        }
        .table-content th h4 {
            line-height: 0px;
            text-align: left;
            font-family: 'Helvetica Neue', Helvetica, Arial;
            font-weight: normal;
            font-size: 14px;
            margin-left: 10px;
            margin-right: 10px;
            padding: 5px;
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
                <td style="text-align: right; width: 25%; font-size: 14px"><b>REKAP HARIAN<br>{{ $date }}</b></td>
            </tr>
        </table>
    </header>
    <footer>
        <p>Copyright © {{ date('Y') }} - INT AUTOCARE</p>
    </footer>
    <main>
        <h4>Rekap Invoice Service</h4>
        <table class="table-content">
            <tr>
                <th style="background-color: #92b9f3;">
                    <p>CASH</p>
                    <h4>{{ "Rp. " . number_format($service_cash,2,',','.') }}</h4>
                </th>
                <th style=" background-color: rgb(146, 241, 243);">
                    <p>TRANSFER</p>
                    <h4>{{ "Rp. " . number_format($service_transfer,2,',','.') }}</h4>
                </th>
                <th style=" background-color: #f4f482;">
                    <p>QRIS</p>
                    <h4>{{ "Rp. " . number_format($service_qris,2,',','.') }}</h4>
                </th>
                <th style="background-color: #f588ae">
                    <p>EDC</p>
                    <h4>{{ "Rp. " . number_format($service_edc,2,',','.') }}</h4>
                </th>
            </tr>
        </table>
        <h4>Rekap Invoice Penjualan Langsung Spare Part</h4>
        <table class="table-content">
            <tr>
                <th style="background-color: #92b9f3;">
                    <p>CASH</p>
                    <h4>{{ "Rp. " . number_format($sell_cash,2,',','.') }}</h4>
                </th>
                <th style=" background-color: rgb(146, 241, 243);">
                    <p>TRANSFER</p>
                    <h4>{{ "Rp. " . number_format($sell_transfer,2,',','.') }}</h4>
                </th>
                <th style=" background-color: #f4f482;">
                    <p>QRIS</p>
                    <h4>{{ "Rp. " . number_format($sell_qris,2,',','.') }}</h4>
                </th>
                <th style="background-color: #f588ae">
                    <p>EDC</p>
                    <h4>{{ "Rp. " . number_format($sell_edc,2,',','.') }}</h4>
                </th>
            </tr>
        </table>
    </main>
</body>
</html>
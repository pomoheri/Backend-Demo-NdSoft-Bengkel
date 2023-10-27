<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: 48mm;
            border: 1px solid #000;
        }

        .header {
            text-align: center;
        }

        .receipt-details {
            margin-top: 3mm;
        }

        .item {
            margin-bottom: 2mm;
        }

        .item-description {
            display: inline-block;
            width: 60%;
        }

        .item-amount {
            display: inline-block;
            width: 40%;
            text-align: right;
        }

        .total {
            border-top: 1px solid #000;
            margin-top: 3mm;
            padding-top: 2mm;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h5>Your Store Name</h5>
            <p>123 Main Street</p>
            <p>City, State ZIP</p>
            <p>Phone: (555) 555-5555</p>
        </div>
        <div class="receipt-details">
            <div class="item">
                <div class="item-description">Item 1 Description</div>
                <div class="item-amount">$10.00</div>
            </div>
            <div class="item">
                <div class="item-description">Item 2 Description</div>
                <div class="item-amount">$15.00</div>
            </div>
            <div class="item">
                <div class="item-description">Item 3 Description</div>
                <div class="item-amount">$5.00</div>
            </div>
        </div>
        <div class="total">
            <p>Total: $30.00</p>
        </div>
    </div>
</body>
</html>

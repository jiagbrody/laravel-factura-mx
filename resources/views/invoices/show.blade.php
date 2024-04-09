<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<h1>Invoice: {{ $invoice->id }}</h1>
@if($invoice->invoiceBalance)
<h3>Balance</h3>
<pre>{{ $invoice->invoiceBalance }}</pre>
@endif

@if($invoice->invoiceCfdi)
<h3>CFDI</h3>
<pre>{{ $invoice->invoiceCfdi }}</pre>
@endif

@if($invoice->invoiceDetail)
<h3>Detail</h3>
<pre>{{ $invoice->invoiceDetail }}</pre>
@endif

@if($invoice->invoiceTaxes)
<h3>Taxes</h3>
<pre>{{ $invoice->invoiceTaxes }}</pre>
@endif

@if($invoice->documents)
<h3>Documents</h3>
<pre>{{ $invoice->documents }}</pre>
@endif

</body>
</html>

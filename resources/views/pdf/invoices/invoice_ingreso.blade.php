<!DOCTYPE html>
<html>
<head>
    <title>Factura en formato PDF</title>
    <style>
        @page {
            margin: 1cm 1cm 1cm 1cm;
            font-family: Arial, sans-serif;
        }

        body {
            background: white;
            margin: 0;
            font-size: 0.7rem;
            font-family: "Source Sans Pro", sans-serif;
        }

        table {
            padding: 0;
            margin: 0;
            border-spacing: 0;
            border-collapse: separate;
        }

        div, table {
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
        }

        p {
            margin: 0.3rem;
        }

        table th, table td {
            padding: 0;
            margin: 0;
            vertical-align: top;
        }

        table th {
            padding: .1rem;
            color: #000;
        }

        .bg-light {
            background: #eee;
        }

        table td {
            padding: 0.1rem;
        }

        h1 {
            margin: 0;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .border {
            border: solid .5px #555;
        }

        .table-inside-center th, .table-inside-center td {
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .width-12 {
            width: 100%;
        }

        .width-8 {
            width: 66.66%;
        }

        .width-7 {
            width: 58.8%;
        }

        .width-6 {
            width: 50%;
        }

        .width-4 {
            width: 33.33%;
        }

        .width-3 {
            width: 25%;
        }

        .width-2 {
            width: 16.6%;
        }

        .width-1 {
            width: 8.3%;
        }

        .logo {
            width: 10rem;
        }

        .strong {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .ml-40 {
            margin-left: 33.34%;
        }

        .ml-50 {
            margin-left: 50%;
        }

        .mbt-0 {
            margin-top: 0;
            margin-bottom: 0;
        }

        .emisor tr th, .emisor tr td {
            border: .5px solid #555;
        }

        .border-child-no tr td, .border-child-no tr th {
            border: 0;
            padding-bottom: .3rem;
            padding-top: .3rem;
        }

        .mt-3 {
            margin-top: 0.75rem;
        }

        .bg-primary {
            background: #002F87;
            color: white;
        }

        .watermark {
            position: fixed;
            bottom: 48%;
            left: 1cm;
            font-size: 4rem;
            transform: rotate(-55deg);
            font-weight: bold;
            opacity: 0.1;
        }
    </style>
</head>
<body>
@if( ! isset($comprobante['Complemento']['TimbreFiscalDigital']))
    <div class="watermark">
        SIN VALIDEZ OFICIAL
    </div>
@endif
<div class="">
    <div class="width-4" style="position:absolute;">
        <img src="{{ asset('storage/layout/logo-cmq-color.svg') }}" class="logo" alt="Logo Hospital CMQ">
    </div>
    <div class="width-8 ml-40">
        <div class="width-12 strong bg-light border text-center">
            <span class="strong" style="padding-top:.2rem;padding-bottom: .2rem;display: block;">
                {{ $comprobante['Emisor']['Nombre'] ?? null }}
            </span>
        </div>
        <div>
            <table class="width-12 emisor">
                <tbody>
                <tr>
                    <td class="bg-light text-right">
                        <p class="strong mbt-0">
                            {{ __('RFC del Emisor') }}
                        </p>
                    </td>
                    <td class="text-left">
                        <p class="mbt-0">
                            {{ $comprobante['Emisor']['Rfc'] ?? null }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="bg-light text-right">
                        <p class="strong mbt-0">
                            {{ __('Tipo de comprobante') }}
                        </p>
                    </td>
                    <td class="text-left">
                        <p class="mbt-0">
                            {{ $comprobante['TipoDeComprobante'] ?? null }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="bg-light text-right">
                        <p class="strong mbt-0">
                            {{ __('Lugar de Expedición') }}
                        </p>
                    </td>
                    <td class="text-left">
                        <p class="mbt-0">
                            {{ $comprobante['LugarExpedicion'] ?? null }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="bg-light text-right">
                        <p class="strong mbt-0">
                            {{ __('Régimen Fiscal') }}
                        </p>
                    </td>
                    <td class="text-left">
                        <p class="mbt-0">
                            {{ $comprobante['Emisor']['RegimenFiscal'] ?? null }}
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="width-12 mt-3">
    <table class="width-12 emisor">
        <tbody>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Forma de pago') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{--                    {{ $comprobante['FormaPago'] ?? null }}--}}
                </p>
            </td>
            <td class="bg-light text-right width-2">
                <p class="strong mbt-0">
                    {{ __('Serie y folio') }}
                </p>
            </td>
            <td class="text-left width-2">
                <p class="mbt-0">
                    {{ $comprobante['Serie'] ?? null }} - {{ $comprobante['Folio'] ?? null }}
                </p>
            </td>
        </tr>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Método de pago') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{--                    {{ $comprobante['MetodoPago'] ?? null }}--}}
                </p>
            </td>
            <td class="bg-light text-right width-2">
                <p class="strong mbt-0">
                    {{ __('Fecha y hora') }}
                </p>
            </td>
            <td class="text-left width-2">
                <p class="mbt-0">
                    {{ $comprobante['Fecha'] ?? null }}
                </p>
            </td>
        </tr>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Moneda') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{ $comprobante['Moneda'] ?? null }}
                </p>
            </td>
            <td class="bg-light text-right width-2">
                <p class="strong mbt-0">

                </p>
            </td>
            <td class="text-left width-2">
                <p class="mbt-0">

                </p>
            </td>
        </tr>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Versión') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{ $comprobante['Version'] ?? null }}
                </p>
            </td>
            <td class="text-left width-2 bg-light">
                <p class="mbt-0 strong text-right">
                    {{ __('Tipo de cambio') }}
                </p>
            </td>
            <td class="text-left width-2">
                <p class="mbt-0">
                    {{--                    {{ ( ! isset($comprobante['TipoCambio']))?'N/A':$comprobante['TipoCambio'] ?? null }}--}}
                </p>
            </td>
        </tr>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Fecha Exp.') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{ $comprobante['Fecha'] ?? null }}
                </p>
            </td>
            <td class="text-left width-2">

            </td>
            <td class="text-left width-2">

            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="width-12 mt-3">
    <div class="width-12 text-center bg-primary strong ">
        Receptor
    </div>
    <table class="width-12 emisor">
        <tbody>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Nombre') }}
                </p>
            </td>
            <td class="text-left width-7">
                <p class="mbt-0">
                    {{ $comprobante['Receptor']['Nombre'] ?? null }}
                </p>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('R.F.C.') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{ $comprobante['Receptor']['Rfc'] ?? null }}
                </p>
            </td>
            <td class="text-left width-2 bg-light">
                <p class="mbt-0 strong text-right">
                    {{ __('Uso CFDI') }}
                </p>
            </td>
            <td class="width-3">
                <p class="mbt-0">
                    {{ $comprobante['Receptor']['UsoCFDI'] ?? null }}
                </p>
            </td>
        </tr>
        <tr>
            <td class="bg-light text-right width-3">
                <p class="strong mbt-0">
                    {{ __('Domicilio fiscal') }}
                </p>
            </td>
            <td class="text-left width-4">
                <p class="mbt-0">
                    {{ $comprobante['Receptor']['DomicilioFiscalReceptor'] ?? null }}
                </p>
            </td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>


<div class="width-12 mt-3">
    <div class="width-12 text-center bg-primary strong ">
        Detalles de conceptos
    </div>
    <table class="width-12 emisor border-child-no">
        <thead class="border-child-no">
        <tr style="font-size:.5rem;">
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('ClaveProd') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('No. Identificacion') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Cantidad') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Clave unidad') }}
                </p>
            </th>
            <!--<th class="bg-light text-center width-1">
                <p class="mbt-0">
                    {{ __('Unidad') }}
            </p>
        </th>-->
            <th class="bg-light text-center width-4">
                <p class="mbt-0">
                    {{ __('Descripción') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('PrecioUnitario') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Importe') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Descuento') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Base') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Impuesto') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Tipo factor') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Tasa o Cuota') }}
                </p>
            </th>
            <th class="bg-light text-center">
                <p class="mbt-0">
                    {{ __('Importe Impuesto') }}
                </p>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($comprobante['Conceptos'] as $conceptos)
            @foreach($conceptos as $concepto)
                <tr style="font-size:.5rem;">
                    <td>
                        <p class="mbt-0 text-center">
                            {{ $concepto['ClaveProdServ'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            {{--                            {{ $concepto['NoIdentificacion'] ?? null }}--}}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            ${{ $concepto['Cantidad'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0">
                            {{ $concepto['ClaveUnidad'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0">
                            {{ $concepto['Descripcion'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            ${{ $concepto['ValorUnitario'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            ${{ $concepto['Importe'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            ${{ $concepto['Descuento'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            ${{ $concepto['Impuestos']['Traslados']['Traslado'][0]['Base'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            {{ $concepto['Impuestos']['Traslados']['Traslado'][0]['Impuesto'] ?? null }}
                        </p>

                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            {{ $concepto['Impuestos']['Traslados']['Traslado'][0]['TipoFactor'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            {{ $concepto['Impuestos']['Traslados']['Traslado'][0]['TasaOCuota'] ?? null }}
                        </p>
                    </td>
                    <td>
                        <p class="mbt-0 text-center">
                            ${{ $concepto['Impuestos']['Traslados']['Traslado'][0]['Importe'] ?? null }}
                        </p>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>


@if(isset($comprobante['Complemento']['Pagos']))
    @foreach($comprobante['Complemento']['Pagos']['Pago'] as $key => $pago)

        <div style="border-top:1px solid #ccc;padding-top:1rem;margin-top:.5rem;">
            <strong>PAGO {{$key+1}}</strong>
        </div>

        <table class="width-12"
               style="font-size:.6rem;padding-top: .5rem;margin-top:.5rem;">
            <tr>
                @if(isset($pago["FechaPago"]))
                    <td><strong>{{ __('Fecha de pago:') }}</strong> {{$pago['FechaPago']}}</td>
                @endif
                @if(isset($pago["FormaDePagoP"]))
                    <td><strong>{{ __('Formato de pago:') }}</strong> {{$pago['FormaDePagoP']}}</td>
                @endif
                @if(isset($pago["MonedaP"]))
                    <td><strong>{{ __('Moneda:') }}</strong> {{$pago['MonedaP']}}</td>
                @endif
                @if(isset($pago["TipoCambioP"]))
                    <td><strong>{{ __('Tipo de cambio P:') }}</strong> {{$pago['TipoCambioP']}}</td>
                @endif
                @if(isset($pago["NumOperacion"]))
                    <td><strong>{{ __('Número de operación:') }}</strong> {{$pago['NumOperacion']}}</td>
                @endif
                @if(isset($pago["RfcEmisorCtaOrd"]))
                    <td><strong>{{ __('RFC Emisor Cta. Ord:') }}</strong> {{$pago['RfcEmisorCtaOrd']}}</td>
                @endif
            </tr>
            <tr>
                @if(isset($pago["MonedaP"]))
                    <td><strong>{{ __('Monto:') }}</strong> {{$pago['MonedaP']}} </td>
                @endif
                @if(isset($pago["NomBancoOrdExt"]))
                    <td><strong>{{ __('Nombre Banco Ord.:') }}</strong>{{$pago['NomBancoOrdExt']}} </td>
                @endif
                @if(isset($pago["CtaOrdenante"]))
                    <td><strong>{{ __('Cuenta ord.:') }}</strong> {{$pago['CtaOrdenante']}} </td>
                @endif
                @if(isset($pago["RfcEmisorCtaBen"]))
                    <td><strong>{{ __('RFC Emisor Cta. Ben.:') }}</strong> {{$pago['RfcEmisorCtaBen']}} </td>
                @endif
                @if(isset($pago["TipoCadPago"]))
                    <td><strong>{{ __('Tipo cad. Pago:') }}</strong> {{$pago['TipoCadPago']}} </td>
                @endif
                @if(isset($pago["CertPago"]))
                    <td><strong>{{ __('Certificado pago:') }}</strong> {{$pago['CertPago']}} </td>
                @endif
            </tr>
            <tr>
                @if(isset($pago["CadPago"]))
                    <td><strong>{{ __('Cad. Pago:') }}</strong> {{$pago['CadPago']}} </td>
                @endif
                @if(isset($pago["SelloPago"]))
                    <td><strong>{{ __('Sello Pago:') }}</strong> {{$pago['SelloPago']}} </td>
                @endif
            </tr>
        </table>


        @foreach($pago['DoctoRelacionado'] as $documento)
            <div style="margin-top:15px;margin-bottom: 10px;font-size:.6rem;">
                <strong>Documento relacionado</strong>
            </div>
            <table class="width-12" style="font-size:.6rem;">
                <tr>
                    @if(isset($documento["IdDocumento"]))
                        <td style="width:120px">
                            <strong>{{ __('UUID:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["Serie"]))
                        <td style="width:60px">
                            <strong>{{ __('Serie:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["Folio"]))
                        <td style="width:50px">
                            <strong>{{ __('Folio:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["MonedaDR"]))
                        <td style="width:50px">
                            <strong>{{ __('Moneda:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["Equivalencia"]))
                        <td style="width:80px;">
                            <strong>{{ __('Equivalencia:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["NumParcialidad"]))
                        <td style="width:70px;">
                            <strong>{{ __('Parcialidad:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["ObjetoImpDR"]))
                        <td style="width:70px;">
                            <strong>{{ __('Objeto impuesto:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["ImpSaldoAnt"]))
                        <td style="width:70px;">
                            <strong>{{ __('Imp. Saldo anterior:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["ImpPagado"]))
                        <td style="width:80px;">
                            <strong>{{ __('Imp. Pagado:') }}</strong>
                        </td>
                    @endif
                    @if(isset($documento["ImpSaldoInsoluto"]))
                        <td style="width:80px;">
                            <strong>{{ __('Imp. Saldo Insoluto:') }}</strong>
                        </td>
                    @endif
                </tr>
                <tr>
                    @if(isset($documento["IdDocumento"]))
                        <td>
                            {{$documento['IdDocumento']}}
                        </td>
                    @endif
                    @if(isset($documento["Serie"]))
                        <td>
                            {{$documento['Serie']}}
                        </td>
                    @endif
                    @if(isset($documento["Folio"]))
                        <td>
                            {{$documento['Folio']}}
                        </td>
                    @endif
                    @if(isset($documento["MonedaDR"]))
                        <td>
                            {{$documento['MonedaDR']}}
                        </td>
                    @endif
                    @if(isset($documento["Equivalencia"]))
                        <td>
                            {{$documento['EquivalenciaDR']}}
                        </td>
                    @endif
                    @if(isset($documento["NumParcialidad"]))
                        <td>
                            {{$documento['NumParcialidad']}}
                        </td>
                    @endif
                    @if(isset($documento["ObjetoImpDR"]))
                        <td>
                            {{$documento['ObjetoImpDR']}}
                        </td>
                    @endif
                    @if(isset($documento["ImpSaldoAnt"]))
                        <td>
                            ${{$documento['ImpSaldoAnt']}}
                        </td>
                    @endif
                    @if(isset($documento["ImpPagado"]))
                        <td>
                            ${{$documento['ImpPagado']}}
                        </td>
                    @endif
                    @if(isset($documento["ImpSaldoInsoluto"]))
                        <td>
                            ${{$documento['ImpSaldoInsoluto']}}
                        </td>
                    @endif

                </tr>
            </table>

            <div style="margin:5px 0;"></div>
            @if(isset($documento["ImpuestosDR"]["TrasladosDR"]["TrasladoDR"]))
                <table class="width-12" style="font-size:.6rem;">
                    <strong>Impuestos DR</strong>
                    <tr>
                        <td style="width:80px;">
                            <strong>{{__('Base') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Impuesto') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Tipo de Factor') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Tasa o Cuota') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Importe') }}</strong>
                        </td>
                    </tr>
                    @foreach($documento["ImpuestosDR"]["TrasladosDR"]["TrasladoDR"] as $impuestoTraslados)
                        <tr>
                            @if(isset($impuestoTraslados["BaseDR"]))
                                <td>
                                    {{ $impuestoTraslados['BaseDR'] }}
                                </td>
                            @endif
                            @if(isset($impuestoTraslados["ImpuestoDR"]))
                                <td>
                                    {{ $impuestoTraslados['ImpuestoDR'] }}
                                </td>
                            @endif
                            @if(isset($impuestoTraslados["TipoFactorDR"]))
                                <td>
                                    {{ $impuestoTraslados['TipoFactorDR'] }}
                                </td>
                            @endif
                            @if(isset($impuestoTraslados["TasaOCuotaDR"]))
                                <td>
                                    {{ $impuestoTraslados['TasaOCuotaDR'] }}
                                </td>
                            @endif
                            @if(isset($impuestoTraslados["ImporteDR"]))
                                <td>
                                    {{ $impuestoTraslados['ImporteDR'] }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            @endif
        @endforeach
        <br>
        @if($pago["ImpuestosP"]["TrasladosP"]["TrasladoP"])
            <div style="margin-left:1rem;">
                <strong style="font-size:.6rem;">Impuestos P</strong>
                <table class="width-12" style="font-size:.6rem;">
                    <tr>
                        <td style="width:80px;">
                            <strong>{{__('Base') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Impuesto') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Tipo de Factor') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Tasa o Cuota') }}</strong>
                        </td>
                        <td>
                            <strong>{{__('Importe') }}</strong>
                        </td>
                    </tr>
                    @foreach($pago["ImpuestosP"]["TrasladosP"]["TrasladoP"] as $impuestoP)
                        <tr>
                            @if(isset($impuestoP["BaseP"]))
                                <td>
                                    {{ $impuestoP['BaseP'] }}
                                </td>
                            @endif
                            @if(isset($impuestoP["ImpuestoP"]))
                                <td>
                                    {{ $impuestoP['ImpuestoP'] }}
                                </td>
                            @endif
                            @if(isset($impuestoP["TipoFactorP"]))
                                <td>
                                    {{ $impuestoP['TipoFactorP'] }}
                                </td>
                            @endif
                            @if(isset($impuestoP["TasaOCuotaP"]))
                                <td>
                                    {{ $impuestoP['TasaOCuotaP'] }}
                                </td>
                            @endif
                            @if(isset($impuestoP["ImporteP"]))
                                <td>
                                    {{ $impuestoP['ImporteP'] }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    @endforeach

@endif

@if(isset($comprobante['Complemento']['TimbreFiscalDigital']))
    <div class="width-12 mt-3">
        <div class="width-12 text-center bg-primary strong">
            Importe con letra
        </div>
        <table class="width-12 emisor">
            <tbody>
            <tr>
                <td class="text-left">
                    <p class="mbt-0">
                        {{ $readableText }}
                        Moneda {{ $comprobante['Moneda'] ?? null }}
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="width-12 mt-3">
        <div class="width-6">
        </div>
        <div class="width-6 ml-50">
            <div class="width-12 text-center bg-primary strong ">
                Totales
            </div>
            <div>
                <table class="width-12 emisor">
                    <tbody class="">
                    <tr>
                        <td class="bg-light text-right">
                            <p class="strong mbt-0">
                                {{ __('Importe') }}
                            </p>
                        </td>
                        <td class="text-left">
                            <p class="mbt-0">
                                ${{ number_format(($comprobante['SubTotal'] ?? 0), 2, '.', ',') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light text-right">
                            <p class="strong mbt-0">
                                {{ __('Descuento') }}
                            </p>
                        </td>
                        <td class="text-left">
                            <p class="mbt-0">
                                {{--                            ${{ number_format(($comprobante['Descuento'] ?? 0), 2, '.', ',') }}--}}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light text-right">
                            <p class="strong mbt-0">
                                {{ __('Iva') }}
                            </p>
                        </td>
                        <td class="text-left">
                            <p class="mbt-0">
                                {{--                            ${{ number_format(($comprobante['Impuestos']['TotalImpuestosTrasladados'] ?? 0), 2, '.', ',') }}--}}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light text-right">
                            <p class="strong mbt-0">
                                {{ __('Total') }}
                            </p>
                        </td>
                        <td class="text-left">
                            <p class="mbt-0">
                                ${{ number_format(($comprobante['Total'] ?? 0), 2, '.', ',') }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="height:20px;width: 100%;"></div>
    <div style="overflow-wrap: break-word;width:560px;float: left">
        <strong style="font-size: 9px">Sello Digital del CFDI</strong><br>
        <span
            style="font-size:8px;">{{$comprobante['Complemento']['TimbreFiscalDigital']['SelloCFD']}}</span><br><br>
        <strong style="font-size: 9px">Sello Digital del SAT</strong><br>
        <span
            style="font-size:8px;">{{$comprobante['Complemento']['TimbreFiscalDigital']['SelloSAT']}}</span><br><br>
        <strong style="font-size: 9px">Cadena Original del Complemento de Certificación Digital del
            SAT</strong><br>

        <span style="font-size:8px;"> {{$comprobante['Complemento']['TimbreFiscalDigital']['UUID']}} || {{$comprobante['Complemento']['TimbreFiscalDigital']['FechaTimbrado']}} || {{$comprobante['Complemento']['TimbreFiscalDigital']['SelloCFD']}} || {{$comprobante['Complemento']['TimbreFiscalDigital']['NoCertificadoSAT']}}||</span>
        <div style="height:10px;width: 100%;"></div>
        <table>
            <tbody>
            <tr>
                <td>
                    <strong style="font-size: 9px">No. De Serie de Certificado del
                        SAT:</strong>
                </td>
                <td style="padding-left:20px">
                <span
                    style="font-size:8px;">{{$comprobante['Complemento']['TimbreFiscalDigital']['NoCertificadoSAT']}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="font-size: 9px">Fecha y hora de
                        Certificación:</strong>
                </td>
                <td style="padding-left:20px">
                <span
                    style="font-size:8px;">{{$comprobante['Complemento']['TimbreFiscalDigital']['FechaTimbrado']}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="font-size: 9px">RFC del proveedor de
                        certificación:</strong>
                </td>
                <td style="padding-left:20px">
                <span
                    style="font-size:8px;">{{$comprobante['Complemento']['TimbreFiscalDigital']['RfcProvCertif']}}</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="text-right">
        <img style="margin-top: 15px;"
             src="data:image/svg+xml;base64,{!! base64_encode(QrCode::size(140)->mergeString('https://www.picng.com/upload/instagram/png_instagram_64241.png')->generate('https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id='.$comprobante['Complemento']['TimbreFiscalDigital']['UUID'].'&re='.$comprobante['Emisor']['Rfc'].'&rr='.$comprobante['Receptor']['Rfc'].'&tt=0000000'.$comprobante['Total'].'0000&fe='.substr($comprobante['Complemento']['TimbreFiscalDigital']['SelloCFD'], -8))) !!}">
    </div>
@endif
</body>
</html>

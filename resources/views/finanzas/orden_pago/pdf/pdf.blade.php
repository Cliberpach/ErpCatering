<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fffdfd; /* Fondo suave para toda la hoja */
        }
        .container {
            width: 97%;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff; /* Fondo blanco para el contenedor principal */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra ligera para profundidad */
        }
        .header, .footer-table, .products-table, .solicitud-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header td, .footer-table td, .products-table td, .solicitud-table td {
            padding: 6px;
            vertical-align: middle;
            font-size: 12px;
            color: #333; /* Color de texto gris oscuro */
        }
        .header img {
            max-width: 100px; /* Limita el ancho máximo de la imagen */
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .header .company {
            font-size: 20px;
            font-weight: bold;
            color: #007bff; /* Color azul para el nombre de la empresa */
            margin-bottom: 10px;
            text-align: center; /* Alinea el texto al centro */
        }
        .header .address, .header .phone, .header .email {
            font-size: 14px;
            color: #666; /* Color de texto gris claro */
            margin-bottom: 5px;
            text-align: center; /* Alinea el texto al centro */
        }
        .info-column {
            vertical-align: bottom; /* Alinea el contenido de la columna al fondo */
            padding-left: 10px; /* Opcional: espaciado interno a la izquierda */
            text-align: center; /* Alinea el texto al centro horizontalmente */
        }
        .footer-table {
            border: 1px solid #ddd; /* Borde gris claro para la tabla del pie de página */
        }
        .footer-table td {
            font-size: 12px;
            font-weight: normal;
            border-top: 1px solid #ddd; /* Borde superior en celdas para separación */
        }
        .products-table {
            border: 1px solid #ddd; /* Borde gris claro para la tabla de productos */
        }
        .products-table th {
            padding: 6px;
            background-color: #007bff; /* Fondo azul oscuro para encabezado */
            color: #ffffff; /* Texto blanco en el encabezado */
            font-size: 12px;
            text-align: left; /* Alinea el texto a la izquierda */
            border-bottom: 2px solid #0056b3; /* Borde inferior para el encabezado */
        }
        .products-table td {
            border-top: 1px solid #ddd; /* Borde superior en celdas para separación */
        }
        .products-table tr:nth-child(even) {
            background-color: #f9f9f9; /* Fondo gris claro para filas pares */
        }
        .solicitud-table td {
            text-align: right; /* Alinea el texto al final de la celda */
            padding: 20px; /* Espaciado adicional para mejor apariencia */
            font-size: 16px; /* Tamaño de fuente más grande para el texto */
            font-weight: bold; /* Texto en negrita para resaltar */
            color: #007bff; /* Color azul para el texto */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Primera Tabla -->
        <table class="header">
            <tr>
                <td class="image-column" style="width: 30%;">
                    @if ($empresa->img_ruta)
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($empresa->img_ruta))) }}" alt="Logo">
                    @else 
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/img_default.png'))) }}" alt="Logo">
                    @endif                
                </td>
                <td class="info-column" style="width: 70%; vertical-align: bottom; text-align: center;">
                    <div class="company">{{$empresa->razon_social}}</div>
                    <div class="address">RUC: {{$empresa->ruc}}</div>
                    <div class="address">{{$empresa->direccion}}</div>
                    <div class="phone">Teléfono: {{$empresa->telefono}}</div>
                    <div class="email">Correo: {{$empresa->correo}}</div>
                </td>
            </tr>
        </table>

        <!-- Segunda Tabla -->
        <table class="solicitud-table">
            <tr>
                <td>{{"ORDEN DE PAGO N°".$orden_pago->id}}</td>
            </tr>
        </table>

        <!-- Tercera Tabla (con información del proveedor) -->
        <table class="footer-table" style="margin-bottom: 20px;">
            <tr>
                <td><strong>PROVEEDOR:</strong></td>
                <td>{{$orden_pago->proveedor_nombre}}</td>
            </tr>
            <tr>
                <td><strong>{{$orden_pago->proveedor_tipo_documento.":"}}</strong></td>
                <td>{{$orden_pago->proveedor_nro_documento}}</td>
            </tr>
            <tr>
                <td><strong>PROYECTO:</strong></td>
                <td>{{$orden_pago->proyecto_nombre}}</td>
            </tr>
            <tr>
                <td><strong>MEDIO PAGO:</strong></td>
                <td>{{$orden_pago->medio_pago}}</td>
            </tr>
            <tr>
                <td><strong>DOCUMENTO:</strong></td>
                <td>{{$orden_pago->documento}}</td>
            </tr>
            <tr>
                <td><strong>ELABORADO POR:</strong></td>
                <td>{{$orden_pago->colaborador_registrador_nombre}}</td>
            </tr>
        </table>

        <!-- Cuarta Tabla -->
        <p style="margin:0;padding:0;font-size:14px;">Por pagos de:</p>
        <table class="products-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CANT</th>
                    <th>UNIDAD</th>
                    <th>PRECIO</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden_pago_detalle as $item)
                    <tr>
                        <td>{{$item->producto_nombre}}</td>
                        <td>{{number_format($item->cantidad, 2)}}</td>
                        <td>{{$item->producto_unidad_medida}}</td>
                        @if ($orden_pago->moneda === 'PEN')
                            <td>{{number_format($item->precio_soles, 2)}}</td>
                            <td>{{number_format($item->precio_soles * $item->cantidad, 2)}}</td>
                        @endif
                        @if ($orden_pago->moneda === 'USD')
                            <td>{{number_format($item->precio_dolares, 2)}}</td>
                            <td>{{number_format($item->precio_dolares * $item->cantidad, 2)}}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>   
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  style="text-align: right;"><strong>SUBTOTAL</strong></td>
                    <td  >
                        <strong>
                            {{ number_format($orden_pago->subtotal, 2) }}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  style="text-align: right;"><strong>IGV</strong></td>
                    <td  >
                        <strong>
                            {{ number_format($orden_pago->monto_igv, 2) }}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  style="text-align: right;"><strong>TOTAL</strong></td>
                    <td  >
                        <strong>
                            {{ number_format($orden_pago->total, 2) }}
                        </strong>
                    </td>
                </tr>
            </tfoot>         
        </table> 

        <table class="products-table">
            <tbody>
                <tr>
                    <td><strong>OBSERVACIONES:</strong></td>
                    <td>{{$orden_pago->observacion}}</td>
                </tr>
                <tr>
                    <td><strong>{{"CUENTA ".$orden_pago->banco_nombre}}</strong></td>
                    <td><strong style="font-size: 14px;">{{$orden_pago->nro_cuenta}}</strong></td>
                </tr>
                <tr>
                    <td><strong>{{"CCI"}}</strong></td>
                    <td><strong style="font-size: 14px;">{{$orden_pago->cci}}</strong></td>
                </tr>
                <tr>
                    <td><strong>{{"CUENTA DETRACCIÓN"}}</strong></td>
                    <td><strong style="font-size: 14px;">{{$orden_pago->nro_cuenta_detraccion}}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de Compra</title>
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
            padding: 5px;
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
            padding: 5px;
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
            padding: 2px; /* Espaciado adicional para mejor apariencia */
            font-size: 12px; /* Tamaño de fuente más grande para el texto */
            font-weight: bold; /* Texto en negrita para resaltar */
            color: #007bff; /* Color azul para el texto */
        }
        .mensaje-table td {
            text-align: left; /* Alinea el texto al final de la celda */
            padding: 2px; /* Espaciado adicional para mejor apariencia */
            font-size: 12px; /* Tamaño de fuente más grande para el texto */
            color: #000000; /* Color azul para el texto */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Primera Tabla -->
        <table class="header">
            <tr>
                <td class="image-column" style="width: 30%;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($empresa->img_ruta))) }}" alt="Logo">
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
                <td>ORDEN DE COMPRA</td>
            </tr>
            <tr>
                <td>{{'FECHA: '.$fecha_impresion}}</td>
            </tr>
        </table>

        <!-- Tercera Tabla (con información del proveedor) -->
        <table class="footer-table" style="margin-bottom:10px;">
            <tr>
                <td>PROVEEDOR:</td>
                <td>{{$orden_compra->proveedor_nombre}}</td>
            </tr>
            <tr>
                <td>{{$orden_compra->tipo_documento_nombre.':'}}</td>
                <td>{{$orden_compra->nro_documento}}</td> 
            </tr>
            <tr>
                <td>MODALIDAD PAGO:</td>
                @if ($orden_compra->modalidad_pago_nombre === 'CONTADO')
                    <td>{{$orden_compra->modalidad_pago_nombre}}</td>
                @endif
                @if ($orden_compra->modalidad_pago_nombre === 'CREDITO')
                    <td>{{$orden_compra->modalidad_pago_nombre.' - '.$orden_compra->modalidad_pago_nro_dias.' '.'DIAS'}}</td>
                @endif
            </tr>
            <tr>
                <td>PROYECTO:</td>
                <td>{{$orden_compra->proyecto_nombre}}</td>
            </tr>
            <tr>
                <td>DOCUMENTO:</td>
                <td>{{$orden_compra->documento}}</td>
            </tr>
            <tr>
                <td>MONEDA:</td>
                <td>{{$orden_compra->moneda}}</td>
            </tr>
        </table>

        <table class="mensaje-table">
            <tr>
                <td>SIRVACE POR ESTE MEDIO SUMINISTRARNOS LOS SIGUIENTES ARTÍCULOS:</td>
            </tr>
        </table>

        <!-- Cuarta Tabla -->
        <table class="products-table">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>UNIDAD</th>
                    <th>CANT</th>
                    <th>P.U.</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden_compra_detalle as $item)
                    <tr>
                        <td>{{$item->producto_nombre}}</td>
                        <td>{{$item->producto_unidad_medida}}</td>
                        <td>{{$item->cantidad}}</td>
                        @if ($orden_compra->moneda == "PEN")
                            <td>{{$item->precio_soles}}</td>
                            <td>{{ number_format($item->precio_soles * $item->cantidad, 2) }}</td>
                        @endif
                        @if ($orden_compra->moneda == "USD")
                            <td>{{$item->precio_dolares}}</td>
                            <td>{{ number_format($item->precio_dolares * $item->cantidad, 2) }}</td>
                        @endif
                    </tr>         
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  style="text-align: right;"><strong>TOTAL</strong></td>
                    <td  >
                        <strong>
                            {{ number_format($orden_compra->total, 2) }}
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table> 

        <table class="products-table">
            <tbody>
                <tr>
                    <td><strong>PROYECTO:</strong></td>
                    <td>{{$orden_compra->proyecto_nombre}}</td>
                </tr>
                <tr>
                    <td><strong>DIRECCION OBRA:</strong></td>
                    <td>{{$orden_compra->direccion_obra}}</td>
                </tr>
                <tr>
                    <td><strong>PERSONA CONTACTO:</strong></td>
                    <td>{{$orden_compra->persona_contacto_nombre}}</td>
                </tr>
                <tr>
                    <td><strong>FECHA ENTREGA:</strong></td>
                    <td>{{$orden_compra->fecha_entrega}}</td>
                </tr>
                <tr>
                    <td><strong>TERMINOS DE ENTREGA:</strong></td>
                    <td>{{$orden_compra->terminos_entrega}}</td>
                </tr>
                <tr>
                    <td><strong>MODALIDAD PAGO:</strong></td>
                    <td>
                        @if($orden_compra->modalidad_pago_nombre == 'CREDITO')
                            {{ $orden_compra->modalidad_pago_nombre . ' (' . $orden_compra->modalidad_pago_nro_dias . ' días)' }}
                        @else
                            {{ $orden_compra->modalidad_pago_nombre }}
                        @endif
                    </td> 
                </tr>
                <tr>
                    <td><strong>OBSERVACIONES:</strong></td>
                    <td>{{$orden_compra->observacion}}</td>
                </tr>
            </tbody>
        </table>
        

        
        
    </div>
</body>
</html>

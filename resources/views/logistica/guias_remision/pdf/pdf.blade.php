<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Guía de Remisión Electrónica</title>
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
            padding: 4px;
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
            font-size: 16px;
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
            font-size: 14px; /* Tamaño de fuente más grande para el texto */
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
                <td style="margin: 0; padding: 0;">
                    GUÍA DE REMISIÓN ELECTRÓNICA
                </td>
            </tr>
            <tr>
                <td style="margin: 0; padding: 0;">
                    {{$guia_remision->serie.'-'.$guia_remision->correlativo}}
                </td>
            </tr>
        </table>
        

        <!-- Tercera Tabla (con información del traslado) -->
        <table class="footer-table" style="margin-bottom:20px;">
            <caption style="caption-side: top; font-size: 12px; font-weight: bold; padding-bottom: 6px; text-align: left;">
                DATOS DEL TRASLADO
            </caption>
            <tr>
                <td style="font-weight: bold;width: 40%;">FECHA EMISIÓN:</td>
                <td>{{$guia_remision->fecha_emision}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">FECHA TRASLADO:</td>
                <td>{{$guia_remision->fecha_traslado}}</td>
            </tr> 
            <tr>
                <td style="font-weight: bold;">MOTIVO TRASLADO:</td>
                <td>{{$guia_remision->motivo_traslado}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">MODALIDAD TRANSPORTE:</td>
                <td>{{$guia_remision->modo_traslado_descripcion}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">{{"PESO BRUTO TOTAL"." "."(".$guia_remision->unidad_peso_total.")"}}:</td>
                <td>{{$guia_remision->peso_total}}</td>
            </tr>
        </table>

        <table class="footer-table" style="margin-bottom:20px;">
            <caption style="caption-side: top; font-size: 12px; font-weight: bold; padding-bottom: 6px; text-align: left;">
                DATOS DEL DESTINATARIO
            </caption>

            <tr>
                <td style="font-weight: bold;width: 40%;">NOMBRE O RAZÓN:</td>
                <td style="text-align:start;">{{$guia_remision->destinatario_razon_social}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">DOCUMENTO:</td>
                <td>{{$guia_remision->destinatario_nro_documento}}</td>
            </tr> 
        </table>

        <table class="footer-table" style="margin-bottom:20px;">
            <caption style="caption-side: top; font-size: 12px; font-weight: bold; padding-bottom: 6px; text-align: left;">
                DATOS DEL PUNTO DE PARTIDA Y LLEGADA
            </caption>
            <tr>
                <td style="font-weight: bold;width: 40%;">DIRECCIÓN DEL PUNTO DE PARTIDA:</td>
                <td>{{$guia_remision->direccion_origen_ubigeo.' - '.$guia_remision->direccion_origen_nombre}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">DIRECCIÓN DEL PUNTO DE LLEGADA:</td>
                <td>{{$guia_remision->direccion_destino_ubigeo.' - '.$guia_remision->direccion_destino_nombre}}</td>
            </tr> 
        </table>

        <table class="footer-table" style="margin-bottom:20px;">
            <caption style="caption-side: top; font-size: 12px; font-weight: bold; padding-bottom: 6px; text-align: left;">
                DATOS DEL TRANSPORTE
            </caption>
            <tr>
                <td style="font-weight: bold;width: 40%;">DATOS DEL VEHÍCULO:</td>
                <td>{{"PLACA: ".$guia_remision->placa}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">DATOS DEL CONDUCTOR:</td>
                <td>{{$guia_remision->conductor_tipo_documento.':'.$guia_remision->conductor_nro_documento}}</td>
            </tr> 
        </table>

        <!-- Cuarta Tabla -->
        <table class="products-table">
            <thead>
                <tr>
                    <th>NRO</th>
                    <th>DESCRIPCIÓN</th>
                    <th>UNIDAD</th>
                    <th>CANTIDAD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guia_remision_detalle as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td> 
                        <td>{{ $item->producto_nombre }}</td>
                        <td>{{ $item->unidad_medida_nombre }}</td>
                        <td>{{ $item->cantidad }}</td>
                    </tr>       
                @endforeach 
            </tbody>
        </table> 
        
    </div>
</body>
</html>

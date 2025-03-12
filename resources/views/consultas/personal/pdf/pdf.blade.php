<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Personal</title>
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
        .header, .footer-table, .consulta-table, .solicitud-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header td, .consulta-table td, .solicitud-table td {
            padding: 12px;
            vertical-align: middle;
            font-size: 14px;
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
            padding: 6px;
            vertical-align: middle;
            color: #333; /* Color de texto gris oscuro */;
            font-size: 11px;
            border-top: 1px solid #ddd; /* Borde superior en celdas para separación */
        }
        .consulta-table {
            margin-top: 40px;
            border: 1px solid #ddd; /* Borde gris claro para la tabla de productos */
        }
        .consulta-table th {
            padding: 1px;
            background-color: #007bff; /* Fondo azul oscuro para encabezado */
            color: #ffffff; /* Texto blanco en el encabezado */
            font-size: 11px;
            text-align: left; /* Alinea el texto a la izquierda */
            border-bottom: 2px solid #0056b3; /* Borde inferior para el encabezado */
            text-align: center;
        }
        .consulta-table td {
            border-top: 1px solid #ddd; /* Borde superior en celdas para separación */
            font-size: 11px;
            text-align: center;
        }
        .consulta-table tr:nth-child(even) {
            background-color: #f9f9f9; /* Fondo gris claro para filas pares */
        }


        .solicitud-table td {
            text-align: right; /* Alinea el texto al final de la celda */
            padding: 14px; /* Espaciado adicional para mejor apariencia */
            font-size: 12px; /* Tamaño de fuente más grande para el texto */
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

        <table class="solicitud-table">
            <tr>
                <td>REPORTE DE PERSONAL</td>
            </tr>
        </table>

        <table class="footer-table">
            <tr>
                <td>PROYECTO:</td>
                <td>{{$proyecto->nombre}}</td>
            </tr>
            <tr>
                <td>FECHA INICIO REPORTE:</td>
                <td>{{$fecha_inicio}}</td>
            </tr>
            <tr>
                <td>FECHA FIN REPORTE:</td>
                <td>{{$fecha_fin}}</td>
            </tr>
            <tr>
                <td>USUARIO:</td>
                <td>{{Auth::user()->name}}</td>
            </tr>
            <tr>
                <td>FECHA:</td>
                <td>{{$fecha_impresion}}</td>
            </tr>
        </table>

        <table class="consulta-table">
            <thead>
                <tr>
                    <th>DOC</th>
                    <th>N° DOC</th>
                    <th>PERSONAL</th>
                    <th>CARGO</th>
                    <th>DÍAS TRABAJADOS</th>
                    <th>FERIADOS TRABAJADOS</th>
                    <th>TOTAL DÍAS TRABAJADOS</th>
                    <th>PAGO/DÍA</th>
                    <th>PAGO MENSUAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($consulta as $item)
                    <tr>
                        <td>{{$item->tipo_documento}}</td>
                        <td>{{$item->nro_documento}}</td>
                        <td>{{$item->colaborador_nombre}}</td>
                        <td>{{$item->cargo}}</td>
                        <td>{{$item->no_feriados_trabajados}}</td>
                        <td>{{$item->feriados_trabajados}}</td>
                        <td>{{$item->total_dias_trabajados}}</td>
                        <td>{{$item->pago_dia}}</td>
                        <td>{{$item->pago_mensual}}</td>
                    </tr>         
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

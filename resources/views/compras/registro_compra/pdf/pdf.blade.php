<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Compra</title>
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
            padding: 12px;
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
            font-size: 14px;
            font-weight: normal;
            border-top: 1px solid #ddd; /* Borde superior en celdas para separación */
        }
        .products-table {
            border: 1px solid #ddd; /* Borde gris claro para la tabla de productos */
        }
        .products-table th {
            padding: 10px;
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
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($empresa->img_ruta))) }}" alt="Logo">
                </td>
                <td class="info-column" style="width: 70%; vertical-align: bottom; text-align: center;">
                    <div class="company">{{$empresa->nombre}}</div>
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
                <td>REGISTRO DE COMPRA</td>
            </tr>
        </table>

        <!-- Tercera Tabla (con información del proveedor) -->
        <table class="footer-table">
           

            <tr>
                <td>USUARIO:</td>
                <td>{{$registro_compra->colaborador_nombre}}</td>
            </tr>
            <tr>
                <td>FECHA IMPRESIÓN:</td>
                <td>{{$fecha_impresion}}</td>
            </tr>
            <tr>
                <td>FECHA ENTREGA:</td>
                <td>{{$registro_compra->fecha_entrega}}</td>
            </tr>
            <tr>
                <td>FECHA REGISTRO:</td>
                <td>{{$registro_compra->created_at}}</td>
            </tr>
            <tr>
                <td>DOCUMENTO:</td>
                <td>{{$registro_compra->serie.'-'.$registro_compra->correlativo}}</td>
            </tr>
            <tr>
                <td>PROVEEDOR:</td>
                <td>{{$registro_compra->proveedor_nombre}}</td>
            </tr>
            <tr>
                <td>MONEDA:</td>
                <td>{{$registro_compra->moneda}}</td>
            </tr>
            <tr>
                <td>OBSERVACIÓN:</td>
                <td>{{$registro_compra->observacion}}</td>
            </tr>
        </table>

        <!-- Cuarta Tabla -->
        <table class="products-table">
            <thead>
                <tr>
                    <th>CANT</th>
                    <th>PRODUCTO</th>
                    <th>ALMACÉN</th>
                    <th>PRECIO S/</th>
                    <th>PRECIO $/</th>
                    <th>UN</th>
                    <th>IMPORT S/</th>
                    <th>IMPORT $/</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registro_compra_detalle as $item)
                    <tr>
                        <td>{{$item->cantidad}}</td>
                        <td>{{$item->producto_nombre}}</td>
                        <td>{{$item->almacen_nombre}}</td>
                        <td>{{$item->precio_mas_igv_soles}}</td>
                        <td>{{$item->precio_mas_igv_dolares}}</td>
                        <td>{{$item->producto_unidad_medida}}</td>
                        <td>{{$item->precio_mas_igv_soles * $item->cantidad}}</td>
                        <td>{{$item->precio_mas_igv_dolares * $item->cantidad}}</td>

                    </tr>         
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cotización</title>
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
        .header, .footer-table, .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header td, .footer-table td, .products-table td {
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
        }
        .header .address, .header .phone, .header .email {
            font-size: 14px;
            color: #666; /* Color de texto gris claro */
            margin-bottom: 5px;
        }
        .header .additional {
            text-align: center; /* Centra el contenido en la columna adicional */
            font-size: 14px;
            color: #007bff; /* Color azul para el contenido adicional */
            border: 1px solid #007bff; /* Borde azul */
            border-radius: 8px; /* Bordes redondeados */
            padding: 10px;
            background-color: #e9f5ff; /* Fondo azul muy claro */
            max-width: 150px;
            margin: 0 auto; /* Centra el cuadro dentro de la columna */
            box-sizing: border-box; /* Asegura que el padding no afecte al tamaño total del cuadro */
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
            padding: 12px;
            background-color: #007bff; /* Fondo azul oscuro para encabezado */
            color: #ffffff; /* Texto blanco en el encabezado */
            font-size: 16px;
            text-align: left; /* Alinea el texto a la izquierda */
            border-bottom: 2px solid #0056b3; /* Borde inferior para el encabezado */
        }
        .products-table td {
            border-top: 1px solid #ddd; /* Borde superior en celdas para separación */
        }
        .products-table tr:nth-child(even) {
            background-color: #f9f9f9; /* Fondo gris claro para filas pares */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Primera Tabla -->
        <table class="header">
            <tr>
                <td class="image-column">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($empresa->img_ruta))) }}" alt="Logo">
                </td>
                <td class="info-column">
                    <div class="company">{{$empresa->nombre}}</div>
                    <div class="address">{{$empresa->direccion}}</div>
                    <div class="phone">Teléfono: {{$empresa->telefono}}</div>
                    <div class="email">Correo: {{$empresa->correo}}</div>
                </td>
                <td class="additional-column">
                    <div class="additional">
                        <div class="additional-content">RUC: {{$empresa->ruc}}</div>
                        <div>{{"CO-".$cotizacion_compra->id}}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Segunda Tabla -->
        <table class="footer-table">
            <tr>
                <td>USUARIO:</td>
                <td>{{$cotizacion_compra->colaborador_nombre}}</td>
            </tr>
            <tr>
                <td>FECHA IMPRESIÓN:</td>
                <td>{{$fecha_impresion}}</td>
            </tr>
        </table>

        <!-- Tercera Tabla -->
        <table class="products-table">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CANTIDAD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cotizacion_compra_detalle as $item)
                    <tr>
                        <td>{{$item->producto_nombre}}</td>
                        <td>{{$item->cantidad}}</td>
                    </tr>         
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

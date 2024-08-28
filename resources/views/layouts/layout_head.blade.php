<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon" />
    <title>PlainAdmin Demo | Bootstrap 5 Admin Template</title>

    <script src="{{asset('jquery/jquery.js')}}"></script>


    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{asset('layout/assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('layout/assets/css/lineicons.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('layout/assets/css/materialdesignicons.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('layout/assets/css/fullcalendar.css')}}" />
    <link rel="stylesheet" href="{{asset('layout/assets/css/fullcalendar.css')}}" />
    <link rel="stylesheet" href="{{asset('layout/assets/css/main.css')}}" />

    <!-- ========== FONTAWESOME ============ -->
    <link href="{{asset('fontawesome/css/fontawesome.css')}}" rel="stylesheet" />
    <link href="{{asset('fontawesome/css/brands.css')}}" rel="stylesheet" />
    <link href="{{asset('fontawesome/css/solid.css')}}" rel="stylesheet" />

    <!-- ======== DATATABLE ====== -->
    <link href="{{asset('datatable/datatables.min.css')}}" rel="stylesheet">


<!-- ======== SELECT2 ========== -->
   <!-- Styles -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

<!-- ======== SWEETALERT2 ======= -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- ========= SPINNER 1 - CUADRADOS COMPRIMIENDOSE Y EXPANDIENDOSE ========= -->
<link rel="stylesheet" href="{{asset('css/spinner_1.css')}}">

<!-- ========= CARGAR UTILIDADES CSS ========= -->
<link rel="stylesheet" href="{{asset('css/utils.css')}}">

<!-- ========= ANCHO SELECT2 100% ========= -->
<style>
  .select2-container--bootstrap-5 .selection {
      width: 100% !important;
  }
</style>

<style>
    .overlay_1 {
        position: fixed; /* Posición fija para cubrir toda la pantalla */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Color oscuro tenue con transparencia */
        justify-content: center; /* Centrar horizontalmente */
        align-items: center; /* Centrar verticalmente */
        z-index: 9999; /* Asegura que el overlay esté por encima de otros elementos */
        display: none; /* Ocultar el overlay por defecto */
    } 
</style>

  @vite(['resources/js/app.js'])
  @yield('css-page')

</head>
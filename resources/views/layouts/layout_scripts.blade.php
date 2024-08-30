  <!-- ========= All Javascript files linkup ======== -->
<script src="{{asset('layout/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('layout/assets/js/Chart.min.js')}}"></script>
<script src="{{asset('layout/assets/js/dynamic-pie-chart.js')}}"></script>
<script src="{{asset('layout/assets/js/moment.min.js')}}"></script>
<script src="{{asset('layout/assets/js/fullcalendar.js')}}"></script>
<script src="{{asset('layout/assets/js/jvectormap.min.js')}}"></script>
<script src="{{asset('layout/assets/js/world-merc.js')}}"></script>
<script src="{{asset('layout/assets/js/polyfill.js')}}"></script>
<script src="{{asset('layout/assets/js/main.js')}}"></script>

<script src="{{asset('datatable/datatables.min.js')}}"></script>
<script src="{{asset('js/utils.js')}}"></script>

<script>
   
    function mostrarAnimacion1() {
        document.getElementById('overlay_1').style.display = 'flex';
    }

    function ocultarAnimacion1() {
        document.getElementById('overlay_1').style.display = 'none';
    }

    //=========== OBTENER FILA POR EL ID DE UN DATATABLE =========
    function getRowById(dtTabla,registro_id) {
        let data    = dtTabla.rows().data();
        let rowData = null;

        for (let i = 0; i < data.length; i++) {
            if (data[i].id == registro_id) {
                rowData = data[i];
                break;
            }
        }

        return rowData;
    }
 
</script>
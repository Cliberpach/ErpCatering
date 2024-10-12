<!DOCTYPE html>
<html lang="en">

  @include('layouts.layout_head')

  <body>

    <!-- ======== Preloader =========== -->
    <div id="preloader">
      <div class="spinner"></div>
    </div>
    <!-- ======== Preloader =========== -->

    <!-- ======== sidebar-nav start =========== -->
    @include('layouts.layout_nav')
    <div class="overlay"></div>
    <!-- ======== sidebar-nav end =========== -->

    <!-- ======== main-wrapper start =========== -->
    <main class="main-wrapper">
      <!-- ========== header start ========== -->
      <header class="header">
        <div class="container-fluid">

          <div class="row">
    <div class="col-lg-5 col-md-5 col-6">
        <div class="header-left d-flex align-items-center">
            <div class="menu-toggle-btn mr-15">
                <button id="menu-toggle" class="main-btn primary-btn btn-hover">
                    <i class="lni lni-chevron-left me-2"></i>
                </button>
            </div>
            {{-- <div class="header-search d-none d-md-flex">
            <form action="#">
                <input type="text" placeholder="Search..." />
                <button><i class="lni lni-search-alt"></i></button>
            </form>
        </div> --}}
        </div>
    </div>

    <div class="col-lg-7 col-md-7 col-6 d-flex flex-column flex-md-row justify-content-end align-items-end">

        <div class="header-right d-flex align-items-center justify-content-between">
          <!-- notification start -->
          @include('layouts.layout_notificaciones')
          <!-- notification end -->
        </div>

        <!-- profile start -->
        <div class="header-profile mt-3 mt-md-0 header-right">
          <div class="row">
            <div class="col-12">

              @include('layouts.layout_profile')

            </div>
          </div>
        </div>
        <!-- profile end -->

    </div>


</div>


        </div>
      </header>
      <!-- ========== header end ========== -->

      <!-- ========== section start ========== -->
      <section class="section">
        @include('layouts.layout_section')
      </section>
      <!-- ========== section end ========== -->

      <!-- ========== footer start =========== -->
      <footer class="footer">
        @include('layouts.layout_footer')
        <!-- end container -->
      </footer>
      <!-- ========== footer end =========== -->
    </main>
    <!-- ======== main-wrapper end =========== -->

    @include('layouts.layout_scripts')


<script>
  document.addEventListener('DOMContentLoaded', () => {
    const socket            = window.io;
    const lstRequerimientos = @json($requerimientos);   

    pintarNotificacionesRequerimientos(lstRequerimientos);

    // Escuchar evento de conexión
    socket.on('connect', () => {
      console.log('Conectado al servidor de Socket.IO AA');
    });

    // Escuchar otros eventos personalizados, por ejemplo, 'message'
    socket.on('message', (data) => {
      console.log('Mensaje recibido:', data);
    });

    // Escuchar evento de desconexión
    socket.on('disconnect', () => {
      console.log('Desconectado del servidor de Socket.IO');
    });

    socket.on('nuevoRequerimiento', (data) => {
        console.log('Nuevo requerimiento recibido:', data);
        toastr.info(`Nuevo mensaje: ${data.mensaje}, Requerimiento: ${data.requerimiento}`);
    });

  });

  function pintarNotificacionesRequerimientos(lstRequerimientos) {
    
  }
</script>

    
  </body>
</html>

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
    const socket            =   window.io;
    const lstRequerimientos =   @json($requerimientos);   

    console.log(lstRequerimientos);

    limpiarNotificaciones();

    const rol_nombre  = @json(Auth::user()->getRoleNames()[0]);

    if(rol_nombre === 'LOGISTICA'){
      pintarNotificacionesRequerimientosLogistica(lstRequerimientos);
    }

    //Escuchar evento de conexión
    socket.on('connect', () => {
      console.log('Conectado al servidor de Socket.IO AA');
    });

    //Escuchar otros eventos personalizados, por ejemplo, 'message'
    socket.on('message', (data) => {
      console.log('Mensaje recibido:', data);
      console.log(data);
    });

    //Escuchar evento de desconexión
    socket.on('disconnect', () => {
      console.log('Desconectado del servidor de Socket.IO');
    });

    socket.on('nuevoRequerimiento', (data) => {
      console.log('Nuevo requerimiento recibido:', data);

      toastr.options = {
          closeButton: true, 
          timeOut: 0,        
          extendedTimeOut: 0, 
          allowHtml: true    
      };

      toastr.info(`
          <div class="content">
               <h6 style="color:white;">
                   ${data.requerimiento.supervisor_nombre}
                   <span class="text-regular">
                       ${data.requerimiento.fecha_registro}
                   </span>
               </h6>
               <p>
                   RQ-${data.requerimiento.id}
               </p>
               <p>
                   <strong>PROYECTO:</strong> ${data.requerimiento.proyecto_nombre}
               </p>
               <span>${tiempoTranscurrido(data.requerimiento.fecha_registro)}</span>
          </div>
      `,'NUEVO REQUERIMIENTO');

      pintarNuevoRequerimientoLogistica(data.requerimiento);
    });

  });

  function limpiarNotificaciones(){
    const ulNotificaciones      =   document.querySelector('#ul_notificaciones');
    ulNotificaciones.innerHTML  =   ``;
  }

  function tiempoTranscurrido(fechaRegistro) {
      const fecha = new Date(fechaRegistro);
      const ahora = new Date();
      const diferencia = Math.floor((ahora - fecha) / 1000); 

      const minutos = Math.floor(diferencia / 60);
      const horas = Math.floor(minutos / 60);
      const dias = Math.floor(horas / 24);

      if (dias > 0) {
          return `hace ${dias} días`;
      } else if (horas > 0) {
          return `hace ${horas} hrs`;
      } else if (minutos > 0) {
          return `hace ${minutos} mins`;
      } else {
          return "Justo ahora";
      }
  }

  function pintarNuevoRequerimientoLogistica(nuevo_requerimiento) {
      const ulNotificaciones = document.querySelector('#ul_notificaciones');
      let nuevoElemento = '';

      if (nuevo_requerimiento.estado !== 'ANULADO') {
          nuevoElemento = `
              <li>
                  <a href="#0">
                      <div class="image">
                          <img src="{{asset('layout/assets/images/lead/lead-6.png')}}" alt="" />
                      </div>
                      <div class="content">
                          <h6>
                              ${nuevo_requerimiento.supervisor_nombre}
                              <span class="text-regular">
                                  ${nuevo_requerimiento.fecha_registro}
                              </span>
                          </h6>
                          <p>
                              RQ-${nuevo_requerimiento.id}
                          </p>
                          <p>
                              <strong>PROYECTO:</strong> ${nuevo_requerimiento.proyecto_nombre}
                          </p>
                          <span>${tiempoTranscurrido(nuevo_requerimiento.fecha_registro)}</span>
                      </div>
                  </a>
              </li>
          `;
      }

    ulNotificaciones.innerHTML = nuevoElemento + ulNotificaciones.innerHTML;
  }


  function pintarNotificacionesRequerimientosLogistica(lstRequerimientos) {
    const ulNotificaciones      =   document.querySelector('#ul_notificaciones');
    let notificaciones          =   ``;

    lstRequerimientos.forEach((r)=>{
      
      if(r.estado !== 'ANULADO'){
        notificaciones  +=  ` <li>
            <a href="#0">
                <div class="image">
                    <img src="{{asset('layout/assets/images/lead/lead-6.png')}}" alt="" />
                </div>
                <div class="content">
                    <h6>
                        ${r.supervisor_nombre}
                        <span class="text-regular">
                          ${r.fecha_registro}
                        </span>
                    </h6>
                    <p>
                      RQ-${r.id}
                    </p>
                    <p>
                      <p>PROYECTO:</p> ${r.proyecto_nombre}
                    </p>
                    <span>${tiempoTranscurrido(r.fecha_registro)}</span>
                </div>
            </a>
        </li>`;
      }
      
    })

    ulNotificaciones.innerHTML  = notificaciones;
  }
</script>

    
  </body>
</html>

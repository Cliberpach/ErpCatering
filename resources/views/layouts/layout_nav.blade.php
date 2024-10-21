<aside class="sidebar-nav-wrapper">

  <div class="navbar-logo" style="margin-bottom: 0; margin-top: 0; display: flex; flex-direction: column; align-items: center; text-align: center;">
    <a href="javascript:void(0);">
        <img 
            style="height: 80px; width: 80px; object-fit: cover; border-radius: 50%;" 
            @if ($empresa->img_ruta)
              src="{{asset($empresa->img_ruta)}}"
            @else 
              src="{{asset('img/img_default.png')}}"
            @endif  
            id="img_nav_empresa" 
            class="img-fluid" 
            alt="logo" />
    </a>
    <div class="col-12" style="width: 100%; overflow: auto; max-height: 150px;">
        <p style="font-size: 14px; font-weight: bold; margin-top: 5px; margin-bottom: 0;" id="nombre_nav_empresa">
            {{ $empresa->razon_social }}
        </p>
    </div>
</div>





    <nav class="sidebar-nav">
      <ul>

        @canany([])
          <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_1"
              aria-controls="ddmenu_1"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon">
                <i class="fa-solid fa-chart-line"></i>
              </span>
              <span class="text">Panel de Control</span>
            </a>
            <ul id="ddmenu_1" class="collapse dropdown-nav">
              <li>
                
                <a href="index.html" > Dashboard </a>
                
              </li>
            </ul>
          </li>
        @endcanany


        @canany(['registros.colaborador','registros.cargo','registros.maquinaria','registros.proyecto','registros.almacen','registros.categoria','registros.marca','registros.producto','registros.conductor'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('registros-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_2"
            aria-controls="ddmenu_2"
            aria-expanded="@yield('registros-expanded')"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-file-invoice"></i>
            </span>
            <span class="text">Registros</span>
          </a>
          <ul id="ddmenu_2" class="collapse dropdown-nav @yield('registros-show')">
            <li>
              @can('registros.colaborador')
                <a class="@yield('colaboradores-active')" href="{{route('registros.colaborador.index')}}">Colaboradores</a>
              @endcan
            </li>
            <li>
              @can('registros.cargo')
                <a class="@yield('cargos-active')" href="{{route('registros.cargo.index')}}">Cargos</a>
              @endcan
            </li>
            <li>
              @can('registros.maquinaria')
                <a class="@yield('maquinarias-active')" href="{{route('registros.maquinaria.index')}}"> Maquinaria </a>
              @endcan
            </li>
            <li>
              @can('registros.proyecto')
                <a class="@yield('proyectos-active')" href="{{route('registros.proyecto.index')}}">Proyecto</a>
              @endcan
            </li>
            <li>
              @can('registros.almacen')
                <a class="@yield('almacenes-active')" href="{{route('registros.almacen.index')}}">Almacén</a>
              @endcan
            </li>
            <li>
              @can('registros.categoria')
                <a class="@yield('categorias-active')" href="{{route('registros.categoria.index')}}">Categoría</a>
              @endcan
            </li>
            <li>
              @can('registros.marca')
                <a class="@yield('marcas-active')" href="{{route('registros.marca.index')}}">Marca</a>
              @endcan
            </li>
            <li>
              @can('registros.producto')
                <a class="@yield('productos-active')" href="{{route('registros.producto.index')}}">Producto</a>
              @endcan
            </li>
            <li>
              @can('registros.modalidad_pago')
                <a class="@yield('modalidad_pago-active')" href="{{route('registros.modalidad_pago.index')}}">Modalidad Pago</a>
              @endcan
            </li>
            <li>
              @can('registros.conductor')
                <a class="@yield('conductor-active')" href="{{route('registros.conductor.index')}}">Conductores</a>
              @endcan
            </li>
            <li>
              @can('registros.vehiculo')
                <a class="@yield('vehiculo-active')" href="{{route('registros.vehiculo.index')}}">Vehículos</a>
              @endcan
            </li>
          </ul>
        </li>
        @endcanany


        @canany(['jornal.registro_labor'])
        <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="@yield('jornales-collapsed', 'collapsed')"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_3"
              aria-controls="ddmenu_3"
              aria-expanded="@yield('jornales-expanded')"
              aria-label="Toggle navigation"
            >
              <span class="icon">
                <i class="fa-solid fa-briefcase"></i>
              </span>
              <span class="text">Jornal</span>
            </a>
            <ul id="ddmenu_3" class="collapse dropdown-nav @yield('jornales-show')" >
              <li>
                @can('jornal.registro_labor')
                  <a class="@yield('registro_labor-active')" href="{{route('jornales.registro_labor.index')}}"> Registro de Labor </a>
                @endcan
              </li>
              {{-- <li>
                @can('jornal.consulta_labor')
                  <a class="@yield('consulta_labor-active')" href="blank-page.html"> Consulta de Labor </a>
                @endcan
              </li> --}}
            </ul>
        </li>
        @endcanany


        @canany(['trabajo_equipo.registro_tarea'])
        <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="@yield('trabajo_equipos-collapsed', 'collapsed')"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_4"
              aria-controls="ddmenu_4"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon">
                <i class="fa-solid fa-truck-moving"></i>
              </span>
              <span class="text">Trabajo Equipos</span>
            </a>
            <ul id="ddmenu_4" class="collapse dropdown-nav @yield('trabajo_equipos-show')">
              <li>
                @can('trabajo_equipo.registro_tarea')
                  <a class="@yield('registro_tarea-active')" href="{{route('trabajo_equipos.registro_tarea.index')}}">Registro de Tarea</a>
                @endcan
              </li>
            </ul>
        </li>
        @endcanany
        

        @canany(['requerimientos.requerimientos'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('requerimientos-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_5"
            aria-controls="ddmenu_5"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-bell-concierge"></i>
            </span>
            <span class="text">Requerimientos</span>
          </a>
          <ul id="ddmenu_5" class="collapse dropdown-nav @yield('requerimientos-show')">
            
            <li>
              @can('requerimientos.requerimientos')
                <a class="@yield('requerimientos-active')" href="{{route('requerimientos.requerimientos.index')}}">Requerimientos</a>
              @endcan
            </li>
           
          </ul>
        </li>
        @endcanany


        @canany(['logistica.registro_salida', 'logistica.lista_requerimientos'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('logistica-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_6"
            aria-controls="ddmenu_6"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-truck-fast"></i>
            </span>
            <span class="text">Logística</span>
          </a>
          <ul id="ddmenu_6" class="collapse dropdown-nav @yield('logistica-show')">
            
            <li>
              @can('logistica.registro_salida')
                <a class="@yield('registro_salida-active')" href="{{route('logistica.registro_salida.index')}}">Registro de Salidas</a>
              @endcan
            </li>

            <li>
              @can('logistica.lista_requerimientos')
                <a class="@yield('lista_requerimientos-active')" href="{{route('logistica.lista_requerimientos.index')}}">Lista Requerimientos</a>
              @endcan
            </li>

            <li>
              @can('logistica.guias_remision')
                <a class="@yield('guias_remision-active')" href="{{route('logistica.guias_remision.index')}}">Guías Remisión</a>
              @endcan
            </li>
            
          </ul>
        </li>
        @endcanany


        @canany(['compras.registro_compra', 'compras.cotizacion_compra','compras.proveedor'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('compras-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_7"
            aria-controls="ddmenu_7"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-cart-shopping"></i>
            </span>
            <span class="text">Compras</span>
          </a>
          <ul id="ddmenu_7" class="collapse dropdown-nav @yield('compras-show')">
            <li>
              @can('compras.registro_compra')
                <a class="@yield('registro_compra-active')" href="{{route('compras.registro_compra.index')}}">Registro de Compras</a>
              @endcan
            </li>
    
            <li>
              @can('compras.cotizacion_compra')
                <a class="@yield('cotizacion_compra-active')" href="{{route('compras.cotizacion_compra.index')}}" >Cotización Compra</a>
              @endcan
            </li>

            <li>
              @can('compras.orden_compra')
                <a class="@yield('orden_compra-active')" href="{{route('compras.orden_compra.index')}}" >Orden Compra</a>
              @endcan
            </li>

            <li>
              @can('compras.proveedor')
                <a class="@yield('proveedores-active')" href="{{route('compras.proveedor.index')}}" >Proveedores</a>
              @endcan
            </li>
          </ul>
        </li>
        @endcanany


        @canany(['plan_proyecto.tarea'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('plan_proyecto-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_8"
            aria-controls="ddmenu_8"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-diagram-project"></i>
            </span>
            <span class="text">Plan Proyecto</span>
          </a>
          <ul id="ddmenu_8" class="collapse dropdown-nav @yield('plan_proyecto-show')">
            <li>
              @can('plan_proyecto.tarea')
                <a class="@yield('tareas-active')" href="{{route('plan_proyecto.tarea.index')}}">Tareas</a>
              @endcan
            </li>
          </ul>
        </li>
        @endcanany
        
        <span class="divider"><hr /></span>


        @canany(['herramientas.usuarios','herramientas.roles','herramientas.empresa'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('herramientas-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_9"
            aria-controls="ddmenu_9"
            aria-expanded="@yield('herramientas-expanded')"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-screwdriver-wrench"></i>
            </span>
            <span class="text">Herramientas</span>
          </a>
          <ul id="ddmenu_9" class="collapse dropdown-nav @yield('herramientas-show')">
            <li>
              @can('herramientas.usuarios')
                <a class="@yield('usuarios-active')" href="{{route('herramientas.usuario.index')}}">Usuarios</a>
              @endcan
            </li>
            <li>
              @can('herramientas.roles')
                <a class="@yield('roles-active')" href="{{route('herramientas.rol.index')}}">Roles</a>
              @endcan
            </li>
            <li>
              @can('herramientas.empresa')
                <a class="@yield('empresa-active')" href="{{route('herramientas.empresa.index')}}">Empresa</a>
              @endcan
            </li>
            <li>
              @can('herramientas.configuracion')
                <a class="@yield('configuracion-active')" href="{{route('herramientas.configuracion.index')}}">Configuracion</a>
              @endcan
            </li>
          </ul>
        </li>
        @endcanany

        @canany(['consultas.personal','consultas.maquinaria','consultas.producto'])
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('consultas-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_10"
            aria-controls="ddmenu_10"
            aria-expanded="@yield('consultas-expanded')"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-clipboard-question"></i>
            </span>
            <span class="text">Consultas</span>
          </a>
          <ul id="ddmenu_10" class="collapse dropdown-nav @yield('consultas-show')">
            <li>
              @can('consultas.personal')
                <a class="@yield('personal-active')" href="{{route('consultas.personal.index')}}">Personal</a>
              @endcan
            </li>
            <li>
              @can('consultas.maquinaria')
                <a class="@yield('consulta_maquinaria-active')" href="{{route('consultas.maquinaria.index')}}">Maquinaria</a>
              @endcan
            </li>
            <li>
              @can('consultas.producto')
                <a class="@yield('consulta_producto-active')" href="{{route('consultas.producto.index')}}">Producto</a>
              @endcan
            </li>
          </ul>
        </li>
        @endcanany

      </ul>
    </nav>
    {{-- <div class="promo-box">
      <div class="promo-icon">
        <img class="mx-auto" src="{{asset('layout/assets/images/logo/logo-icon-big.svg')}}" alt="Logo">
      </div>
      <h3>Upgrade to PRO</h3>
      <p>Improve your development process and start doing more with PlainAdmin PRO!</p>
      <a href="https://plainadmin.com/pro" target="_blank" rel="nofollow" class="main-btn primary-btn btn-hover">
        Upgrade to PRO
      </a>
    </div> --}}
  </aside>
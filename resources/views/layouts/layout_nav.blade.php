<aside class="sidebar-nav-wrapper">
    <div class="navbar-logo">
      <a href="index.html">
        <img src="assets/images/logo/logo.svg" alt="logo" />
      </a>
    </div>
    <nav class="sidebar-nav">
      <ul>

        @can('panel_control.dashboard')
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
        @endcan

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
              @can('registros.colaborador')
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
          </ul>
        </li>

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
              <li>
                @can('jornal.consulta_labor')
                  <a class="@yield('consulta_labor-active')" href="blank-page.html"> Consulta de Labor </a>
                @endcan
              </li>
            </ul>
        </li>

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
              <li>
                @can('trabajo_equipo.consulta_tarea')
                  <a href="blank-page.html">Consulta de Tarea</a>
                @endcan
              </li>
            </ul>
        </li>
     
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('logistica-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_5"
            aria-controls="ddmenu_5"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-cart-shopping"></i>
            </span>
            <span class="text">Logística</span>
          </a>
          <ul id="ddmenu_5" class="collapse dropdown-nav @yield('logistica-show')">
            <li>
              @can('logistica.registro_compra')
                <a class="@yield('registro_compra-active')" href="{{route('logistica.registro_compra.index')}}">Registro de Compras</a>
              @endcan
            </li>
            <li>
              @can('logistica.registro_salida')
                <a href="signup.html">Registro de Salidas</a>
              @endcan
            </li>
            <li>
              @can('logistica.cotizacion_compra')
                <a class="@yield('cotizacion_compra-active')" href="{{route('logistica.cotizacion_compra.index')}}" >Cotización Compra</a>
              @endcan
            </li>
          </ul>
        </li>
        <span class="divider"><hr /></span>
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="@yield('herramientas-collapsed', 'collapsed')"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_6"
            aria-controls="ddmenu_6"
            aria-expanded="@yield('herramientas-expanded')"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-screwdriver-wrench"></i>
            </span>
            <span class="text">Herramientas</span>
          </a>
          <ul id="ddmenu_6" class="collapse dropdown-nav @yield('herramientas-show')">
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
          </ul>
        </li>
        
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
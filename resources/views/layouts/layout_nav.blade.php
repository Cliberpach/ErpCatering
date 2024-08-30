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
            <ul id="ddmenu_1" class="collapse show dropdown-nav">
              <li>
                
                <a href="index.html" class="active"> Dashboard </a>
                
              </li>
            </ul>
          </li>
        @endcan

        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_2"
            aria-controls="ddmenu_2"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-file-invoice"></i>
            </span>
            <span class="text">Registros</span>
          </a>
          <ul id="ddmenu_2" class="collapse dropdown-nav">
            <li>
              @can('registros.colaborador')
                <a href="{{route('registros.colaborador.index')}}">Colaboradores</a>
              @endcan
            </li>
            <li>
              @can('registros.maquinaria')
                <a href="blank-page.html"> Maquinaria </a>
              @endcan
            </li>
            <li>
              @can('registros.proyecto')
                <a href="blank-page.html">Proyecto</a>
              @endcan
            </li>
            <li>
              @can('registros.almacen')
                <a href="blank-page.html">Almacén</a>
              @endcan
            </li>
            <li>
              @can('registros.categoria')
                <a href="{{route('registros.categoria.index')}}">Categoría</a>
              @endcan
            </li>
            <li>
              @can('registros.marca')
                <a href="{{route('registros.marca.index')}}">Marca</a>
              @endcan
            </li>
            <li>
              @can('registros.producto')
                <a href="blank-page.html">Producto</a>
              @endcan
            </li>
          </ul>
        </li>

        <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_3"
              aria-controls="ddmenu_3"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon">
                <i class="fa-solid fa-briefcase"></i>
              </span>
              <span class="text">Jornal</span>
            </a>
            <ul id="ddmenu_3" class="collapse dropdown-nav">
              <li>
                @can('jornal.registro_labor')
                  <a href="settings.html"> Registro de Labor </a>
                @endcan
              </li>
              <li>
                @can('jornal.consulta_labor')
                  <a href="blank-page.html"> Consulta de Labor </a>
                @endcan
              </li>
            </ul>
        </li>

        <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_4"
              aria-controls="ddmenu_4"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon">
                <i class="fa-solid fa-people-group"></i>
              </span>
              <span class="text">Trabajo Equipos</span>
            </a>
            <ul id="ddmenu_4" class="collapse dropdown-nav">
              <li>
                @can('trabajo_equipo.registro_tarea')
                  <a href="settings.html">Registro de Tarea</a>
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
            class="collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_5"
            aria-controls="ddmenu_5"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-truck-fast"></i>
            </span>
            <span class="text">Logística</span>
          </a>
          <ul id="ddmenu_5" class="collapse dropdown-nav">
            <li>
              @can('logistica.registro_compra')
                <a href="signin.html">Registro de Compras</a>
              @endcan
            </li>
            <li>
              @can('logistica.registro_salida')
                <a href="signup.html">Registro de Salidas</a>
              @endcan
            </li>
          </ul>
        </li>
        <span class="divider"><hr /></span>
        <li class="nav-item nav-item-has-children">
          <a
            href="#0"
            class="collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#ddmenu_6"
            aria-controls="ddmenu_6"
            aria-expanded="true"
            aria-label="Toggle navigation"
          >
            <span class="icon">
              <i class="fa-solid fa-screwdriver-wrench"></i>
            </span>
            <span class="text">Herramientas</span>
          </a>
          <ul id="ddmenu_6" class="collapse show dropdown-nav">
            <li>
              @can('herramientas.usuarios')
                <a href="{{route('herramientas.usuario.index')}}" class="active">Usuarios</a>
              @endcan
            </li>
            <li>
              @can('herramientas.roles')
                <a href="{{route('herramientas.rol.index')}}" class="active">Roles</a>
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
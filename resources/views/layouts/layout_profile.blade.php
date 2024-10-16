@php

  $rol_usuario  =   Auth::user()->getRoleNames()[0];
  $proyecto     =   null;

  if($rol_usuario !== 'SUPERVISOR'){
    $proyecto = DB::select('select 
                pr.nombre
                from proyectos as pr
                inner join proyecto_personal as pp on pr.id = pp.proyecto_id
                where 
                pr.estado != "ANULADO" 
                and pr.estado != "FINALIZADO"
                and pp.colaborador_id = ?',
                [Auth::user()->colaborador_id]);
  }else{
    $proyecto = DB::select('select 
                pr.nombre
                from proyectos as pr
                where 
                pr.estado != "ANULADO" 
                and pr.estado != "FINALIZADO"
                and pr.supervisor_id = ?',
                [Auth::user()->colaborador_id]);
  }

@endphp

<div class="profile-box ">
    <button class="dropdown-toggle bg-transparent border-0" type="button" id="profile"
      data-bs-toggle="dropdown" aria-expanded="false">
      <div class="profile-info">
        <div class="info">
          <div class="image">
            <img src="{{asset('img/user_default.png')}}" alt="" />
          </div>
          <div>
            <h6 class="fw-500">{{Auth::user()->name}}</h6>
            <p>
              {{ Auth::user()->getRoleNames()[0] }} - 
              @if (!empty($proyecto))
                  {{ $proyecto[0]->nombre }}
              @else
                  SIN PROYECTO
              @endif
            </p>
          </div>
        </div>
      </div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile">
      <li>
        <div class="author-info flex items-center !p-1">
          <div class="image">
            <img src="{{asset('img/user_default.png')}}" alt="image">
          </div>
          <div class="content">
            <h4 class="text-sm">{{Auth::user()->name}}</h4>
            <a class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white text-xs" href="#">{{Auth::user()->email}}</a>
          </div>
        </div>
      </li>
      <li class="divider"></li>
      <li>
        <a href="#0">
          <i class="lni lni-user"></i> View Profile
        </a>
      </li>
      <li>
        <a href="#0">
          <i class="lni lni-alarm"></i> Notifications
        </a>
      </li>
      <li>
        <a href="#0"> <i class="lni lni-inbox"></i> Messages </a>
      </li>
      <li>
        <a href="#0"> <i class="lni lni-cog"></i> Settings </a>
      </li>
      <li class="divider"></li>
      <li>
        <form action="{{route('logout')}}" method="post">
            @csrf
            
                <a href="#0" class="p-0"> 
                    <button style="background-color: unset;font-size:14px;border:unset;" type="submit"> <i class="lni lni-exit"></i> Cerrar Sesión </button>
                </a>
            
        </form>
      </li>
    </ul>
  </div>
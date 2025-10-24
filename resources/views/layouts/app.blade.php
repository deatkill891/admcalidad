<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Admin Calidad') }}</title>

  {{-- Estilos --}}
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/original.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  {{-- ** ÚNICA CARGA DE JQUERY (en el head) ** --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  {{-- Estilos para el menú desplegable --}}
  <style>
  .sidebar .nav-link.dropdown-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .sidebar .nav-link.dropdown-toggle::after {
    display: none;
  }

  .sidebar .nav-link .sub-arrow {
    transition: transform 0.15s ease-in-out;
    font-size: 0.8em;
  }

  .sidebar .nav-link[aria-expanded="true"] .sub-arrow {
    transform: rotate(180deg);
  }

  .sidebar .nav-submenu {
    background-color: #f8f9fa;
    padding-left: 1.5rem;
  }

  .sidebar .nav-submenu .nav-link {
    padding-top: 0.35rem;
    padding-bottom: 0.35rem;
  }

  /* Efecto hover */
  .sidebar .nav-pills>.nav-item>.nav-link.text-dark:hover {
    background-color: #e9ecef;
  }

  .sidebar .nav-submenu .nav-link.text-dark:hover {
    background-color: #dee2e6;
  }

  /* Centrar icono toggle colapsado */
  .sidebar.toggled .d-flex.align-items-center {
    justify-content: center;
  }

  .sidebar.toggled #sidebarToggle {
    margin-right: 0 !important;
    font-size: 1.2rem;
  }
  </style>

</head>

<body>
  <div class="d-flex">

    {{-- BARRA LATERAL (SIDEBAR) --}}
    <nav class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-white border-end shadow-sm">
      {{-- Encabezado del Sidebar --}}
      <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <i class="bi bi-list fs-4 me-3 toggle-btn d-none d-md-block" id="sidebarToggle"></i>
        <span class="fs-4 hide-on-collapse">Admin Calidad</span>
      </div>
      <hr>

      {{-- Lista de Navegación Principal --}}
      <ul class="nav nav-pills flex-column mb-auto">

        {{-- 1. Dashboard --}}
        <li class="nav-item mb-1">
          <a href="{{ route('dashboard') }}"
            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }} rounded-3"
            aria-current="page">
            <i class="bi bi-house-door me-2"></i>
            <span class="hide-on-collapse">Dashboard</span>
          </a>
        </li>

        {{-- 2. Grupo Admin --}}
        @if(Auth::user() && Auth::user()->permiso && Auth::user()->permiso->Administrador == 1)
        <li class="nav-item mb-1">
          <a href="#admin-submenu" data-bs-toggle="collapse"
            class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : 'text-dark' }} rounded-3 dropdown-toggle"
            aria-expanded="{{ request()->routeIs('usuarios.*') ? 'true' : 'false' }}">
            <span> <i class="bi bi-shield-lock me-2"></i> <span class="hide-on-collapse">Admin</span> </span>
            <i class="bi bi-chevron-down sub-arrow hide-on-collapse"></i>
          </a>
          <div class="collapse {{ request()->routeIs('usuarios.*') ? 'show' : '' }}" id="admin-submenu">
            <ul class="nav flex-column mt-1 nav-submenu rounded-2">
              <li class="nav-item">
                <a href="{{ route('usuarios.index') }}"
                  class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : 'text-dark' }} rounded-3">
                  <i class="bi bi-person-workspace me-2"></i> <span class="hide-on-collapse">Usuarios</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        @endif

        {{-- 3. Grupo Análisis Acerías --}}
        @if(Auth::user() && Auth::user()->permiso && Auth::user()->permiso->Analisis == 1)
        <li class="nav-item mb-1">
          <a href="#acerias-submenu" data-bs-toggle="collapse"
            class="nav-link {{ request()->routeIs('analisis-horno.*') ? 'active' : 'text-dark' }} rounded-3 dropdown-toggle"
            aria-expanded="{{ request()->routeIs('analisis-horno.*') ? 'true' : 'false' }}">
            <span> <i class="bi bi-fire me-2"></i> <span class="hide-on-collapse">Análisis Acerías</span> </span>
            <i class="bi bi-chevron-down sub-arrow hide-on-collapse"></i>
          </a>
          <div class="collapse {{ request()->routeIs('analisis-horno.*') ? 'show' : '' }}" id="acerias-submenu">
            <ul class="nav flex-column mt-1 nav-submenu rounded-2">
              <li class="nav-item">
                <a href="{{ route('analisis-horno.create', ['tipo' => 'hf']) }}"
                  class="nav-link {{ (request()->routeIs('analisis-horno.create') && request()->route('tipo') == 'hf') ? 'active' : 'text-dark' }} rounded-3">
                  <i class="bi bi-graph-up me-2"></i> <span class="hide-on-collapse">Fusión (HF)</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('analisis-horno.create', ['tipo' => 'ha']) }}"
                  class="nav-link {{ (request()->routeIs('analisis-horno.create') && request()->route('tipo') == 'ha') ? 'active' : 'text-dark' }} rounded-3">
                  <i class="bi bi-gem me-2"></i> <span class="hide-on-collapse">Afino (HA)</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('analisis-horno.create', ['tipo' => 'mcc']) }}"
                  class="nav-link {{ (request()->routeIs('analisis-horno.create') && request()->route('tipo') == 'mcc') ? 'active' : 'text-dark' }} rounded-3">
                  <i class="bi bi-funnel me-2"></i> <span class="hide-on-collapse">Colada (MCC)</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        @endif

      </ul>
      <hr>

      {{-- Dropdown de Usuario --}}
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" id="dropdownUser"
          data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://i.pravatar.cc/32?u={{ Auth::id() }}" alt="" class="rounded-circle me-2 border">
          <strong class="hide-on-collapse">{{ Auth::user()->email ?? 'email' }}</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('logout') }}"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              Cerrar Sesión
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="main-content flex-grow-1">

      <div class="p-2 d-md-none border-bottom bg-white shadow-sm sticky-top">
        <button class="btn btn-light" type="button" id="mobileSidebarToggle">
          <i class="bi bi-list fs-4"></i>
        </button>
        <span class="fs-5 ms-2 fw-semibold">Admin Calidad</span>
      </div>

      <div class="p-4">
        {{ $slot }}
      </div>
    </main>

    <div class="sidebar-overlay d-none" id="sidebarOverlay"></div>

  </div>

  {{-- Scripts --}}
  {{-- ** ERROR CORREGIDO: Eliminada la carga duplicada de jQuery 3.6.0 de aquí ** --}}
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>

  <script>
  $(document).ready(function() {
    // Este script es para la tabla de Usuarios (si existe)
    $('#usersTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
      },
      responsive: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"]
      ]
    });
  });
  </script>

  <script src="{{ asset('js/original.js') }}"></script>

</body>

</html>
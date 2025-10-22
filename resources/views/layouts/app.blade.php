<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Admin Calidad') }}</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/original.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div class="d-flex">

    <nav class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-white border-end shadow-sm">
      <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <i class="bi bi-box-seam fs-4 me-3 toggle-btn" id="sidebarToggle"></i>
        <span class="fs-4 hide-on-collapse">Admin Calidad</span>
      </div>
      <hr>

      <ul class="nav nav-pills flex-column mb-auto">
        <ul class="nav nav-pills flex-column mb-auto">

          <li class="nav-item mb-1">
            <a href="{{ route('dashboard') }}"
              class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }} rounded-3"
              aria-current="page">
              <i class="bi bi-house-door me-2"></i>
              <span class="hide-on-collapse">Dashboard</span>
            </a>
          </li>

          <li class="nav-item mt-2 mb-1">
            <h6 class="nav-link text-muted text-uppercase small fw-bold" style="pointer-events: none;">
              <i class="bi bi-shield-lock me-2"></i>
              <span class="hide-on-collapse">Admin</span>
            </h6>
          </li>

          <li class="nav-item mb-1">
            <a href="{{ route('usuarios.index') }}"
              class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : 'text-dark' }} rounded-3">
              <i class="bi bi-person-workspace ms-3 me-1"></i> <span class="hide-on-collapse">Usuarios</span>
            </a>
          </li>

        </ul>
      </ul>
      <hr>

      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" id="dropdownUser"
          data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://i.pravatar.cc/32?u={{ Auth::id() }}" alt="" class="rounded-circle me-2 border">
          <strong class="hide-on-collapse">{{ Auth::user()->name }}</strong>
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

    <main class="main-content flex-grow-1">
      <div class="p-4">
        {{ $slot }}
      </div>
    </main>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>

  <script>
  $(document).ready(function() {
    $('#usersTable').DataTable({
      // Opciones de configuración (Opcional, pero recomendado)
      language: {
        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' // Idioma español
      },
      responsive: true, // Habilita la tabla responsiva
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"]
      ] // Opciones del selector de resultados
    });
  });
  </script>
  <script src="{{ asset('js/original.js') }}"></script>

</body>

</html>
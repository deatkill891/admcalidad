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
        <li class="nav-item mb-1"><a href="#" class="nav-link active rounded-3" aria-current="page"
            style="--bs-bg-opacity: .9;"><i class="bi bi-house-door me-2"></i> <span
              class="hide-on-collapse">Dashboard</span></a></li>
        <li class="nav-item mb-1"><a href="#" class="nav-link text-dark rounded-3"><i class="bi bi-vial me-2"></i> <span
              class="hide-on-collapse">Muestras</span></a></li>
        <li class="nav-item mb-1"><a href="#" class="nav-link text-dark rounded-3"><i class="bi bi-box me-2"></i> <span
              class="hide-on-collapse">Usuarios</span></a></li>
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
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/original.js') }}"></script>

</body>

</html>
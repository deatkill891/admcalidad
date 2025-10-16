<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Admin Calidad') }}</title>

  @vite(['resources/scss/app.scss', 'resources/js/app.js'])

  <style>
  body {
    font-family: 'Inter', sans-serif;
  }

  .sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    width: 280px;
    transition: transform 0.3s ease-in-out;
  }

  .main-content {
    margin-left: 280px;
    transition: margin-left 0.3s ease-in-out;
  }

  @media (max-width: 768px) {
    .sidebar {
      transform: translateX(-100%);
    }

    .sidebar.active {
      transform: translateX(0);
    }

    .main-content {
      margin-left: 0;
    }
  }

  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.15) !important;
  }

  .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.08) !important;
  }
  </style>
</head>

<body class="bg-light">

  <nav class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white bg-dark">
    <a href="{{ route('dashboard') }}"
      class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <i class="bi bi-box-seam fs-4 me-3"></i>
      <span class="fs-4">Admin Calidad</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-1">
        <a href="#" class="nav-link active" aria-current="page">
          <i class="bi bi-house-door me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-vial me-2"></i> Muestras
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-box me-2"></i> Materiales
        </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser"
        data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://i.pravatar.cc/32?u={{ Auth::id() }}" alt="" class="rounded-circle me-2">
        <strong>{{ Auth::user()->name }}</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
        <li><a class="dropdown-item" href="#">Perfil</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item" href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Cerrar Sesión
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> @csrf </form>
        </li>
      </ul>
    </div>
  </nav>

  <main class="main-content">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm d-md-none">
      <div class="container-fluid">
        <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar-menu"
          aria-controls="sidebar-menu">
          <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h1">{{ config('app.name', 'Admin Calidad') }}</span>
      </div>
    </nav>

    <div class="p-4">
      {{ $slot }}
    </div>
  </main>

  <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebar-menu"
    aria-labelledby="sidebar-menu-label">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="sidebar-menu-label">
        <i class="bi bi-box-seam fs-4 me-3"></i> Admin Calidad
      </h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <hr class="text-white">
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
          <a href="#" class="nav-link active" aria-current="page">
            <i class="bi bi-house-door me-2"></i> Dashboard
          </a>
        </li>
        <li class="nav-item mb-1">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-vial me-2"></i> Muestras
          </a>
        </li>
        <li class="nav-item mb-1">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-box me-2"></i> Materiales
          </a>
        </li>
      </ul>
    </div>
  </div>
</body>

</html>
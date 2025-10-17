<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anónimo"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Administración de Usuarios') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">

          <a href="{{ route('usuarios.create') }}" class="btn btn-dark fw-bold text-uppercase mb-4">
            <i class="fas fa-user-plus me-2"></i> Crear Nuevo Usuario
          </a>

          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="d-block d-sm-inline">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <div class="table-responsive">
            <table id="usersTable" class="table table-hover table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th scope="col" class="fw-bold text-white">#</th>
                  <th scope="col" class="fw-bold text-white">Nombre de Usuario</th>
                  <th scope="col" class="fw-bold text-white">Email</th>
                  <th scope="col" class="fw-bold text-white text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                  <td>{{ $usuario->IdUsuario }}</td>
                  <td>{{ $usuario->username }}</td>
                  <td>{{ $usuario->email }}</td>
                  <td class="text-center">
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary me-2"
                      title="Editar Usuario">
                      <i class="fas fa-pen-to-square"></i>
                    </a>

                    <a href="{{ route('usuarios.permissions.edit', $usuario) }}" class="btn btn-sm btn-outline-success"
                      title="Asignar Permisos">
                      <i class="fas fa-user-lock"></i>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>